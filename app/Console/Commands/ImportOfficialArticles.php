<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\ArticleMedia;
use App\Models\Category;
use App\Models\Media;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DOMDocument;
use DOMElement;
use DOMXPath;

class ImportOfficialArticles extends Command
{
    protected $signature = 'news:import-official {--yes : Confirm replacement of demo articles}';
    protected $description = 'Import two latest articles and images per official Harian Merah Putih category';

    private array $categoryMap = [
        'nasional' => 'nasional', 'politik' => 'politik', 'hukrim' => 'hukrim',
        'kesehatan' => 'kesehatan', 'ekbis' => 'ekbis', 'pendidikan' => 'pendidikan',
        'olahraga' => 'olah-raga', 'lifestyle' => 'lifestyle', 'viral' => 'viral',
        'internasional' => 'internasional',
    ];

    public function handle(): int
    {
        if (! $this->option('yes')) {
            $this->error('Jalankan dengan --yes setelah memastikan database ini lokal dan sudah dibackup.');
            return self::FAILURE;
        }

        $items = [];
        foreach ($this->categoryMap as $local => $remote) {
            $response = Http::timeout(30)->get('https://harianmerahputih.id/category/'.$remote);
            if (! $response->successful()) { $this->error("Gagal mengambil kategori {$remote}"); return self::FAILURE; }
            foreach ($this->categoryArticles($response->body()) as $item) {
                if (count($items[$local] ?? []) < 2) { $items[$local][] = $item; }
            }
        }

        if (collect($items)->flatten(1)->count() !== 20) {
            $this->error('Tidak ditemukan tepat 2 artikel untuk setiap kategori; tidak ada data yang diubah.');
            return self::FAILURE;
        }

        $details = [];
        foreach ($items as $local => $categoryItems) {
            foreach ($categoryItems as $item) {
                $details[] = [$local, $item, $this->articleDetail($item['url'])];
            }
        }
        $imageCount = collect($details)->sum(fn ($row) => count($row[2]['images']));
        $this->info("Validasi selesai: 20 artikel, {$imageCount} gambar.");

        DB::transaction(function () use ($details): void {
            Article::query()->where('is_demo', true)->delete();
            $author = User::query()->where('role', 'super_admin')->first() ?? User::query()->firstOrFail();
            foreach ($details as [$local, $item, $detail]) {
                $category = Category::query()->where('slug', $local)->firstOrFail();
                $slug = Str::slug($detail['title']);
                $article = Article::query()->create([
                    'category_id' => $category->id, 'author_id' => $author->id,
                    'title' => $detail['title'], 'slug' => $slug,
                    'excerpt' => Str::limit($detail['excerpt'], 280), 'body' => $detail['body'],
                    'status' => 'published', 'published_at' => $detail['published_at'],
                    'is_demo' => false, 'view_count' => 0, 'is_editor_pick' => false,
                ]);
                foreach ($detail['images'] as $order => $imageUrl) {
                    $media = $this->downloadMedia($imageUrl, $article, $order, $detail['title']);
                    ArticleMedia::query()->create(['article_id' => $article->id, 'media_id' => $media->id, 'role' => $order === 0 ? 'featured' : 'gallery', 'sort_order' => $order]);
                }
            }
        });

        $this->info('Import selesai dan artikel demo telah diganti.');
        return self::SUCCESS;
    }

    private function categoryArticles(string $html): array
    {
        $xpath = $this->xpath($html); $result = [];
        foreach ($xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' item-list ')]") as $article) {
            $link = $xpath->query(".//*[contains(concat(' ', normalize-space(@class), ' '), ' post-box-title ')]//a", $article)->item(0);
            if (! $link instanceof DOMElement) continue;
            $url = $link->getAttribute('href'); $title = trim($link->textContent);
            if ($url && $title) $result[] = ['url' => $url, 'title' => $title];
            if (count($result) === 2) break;
        }
        return $result;
    }

    private function articleDetail(string $url): array
    {
        $html = Http::timeout(30)->get($url)->throw()->body(); $xpath = $this->xpath($html);
        $title = trim($xpath->query('//meta[@property="og:title"]/@content')->item(0)?->nodeValue ?? '');
        $excerpt = trim($xpath->query('//meta[@name="description"]/@content')->item(0)?->nodeValue ?? '');
        $published = $xpath->query('//meta[@property="article:published_time"]/@content')->item(0)?->nodeValue;
        $entry = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' entry ') and contains(@class, 'isi_berita')]")->item(0);
        $paragraphs = [];
        if ($entry) foreach ($xpath->query('.//p|.//h2|.//h3|.//h4|.//li', $entry) as $node) { $text = trim(strip_tags(html_entity_decode($node->textContent, ENT_QUOTES | ENT_HTML5, 'UTF-8'))); $text = trim(preg_replace('/\\s+/', ' ', $text)); if ($text !== '') $paragraphs[] = $text; }
        $images = [];
        $ogImage = trim($xpath->query('//meta[@property="og:image"]/@content')->item(0)?->nodeValue ?? '');
        if (str_starts_with($ogImage, 'https://harianmerahputih.id/') && ! str_contains($ogImage, '/logo/')) {
            $images[] = $ogImage;
        }
        foreach ($xpath->query('.//img', $entry) as $img) {
            $src = $img->getAttribute('data-src') ?: $img->getAttribute('src');
            if (str_starts_with($src, 'https://harianmerahputih.id/') && ! str_contains($src, '/logo/') && ! str_contains($src, 'data:image') && ! in_array($src, $images, true)) $images[] = $src;
        }
        return ['title' => $title ?: 'Berita Harian Merah Putih', 'excerpt' => $excerpt, 'published_at' => $published ?: now(), 'body' => implode("\n\n", $paragraphs), 'images' => array_slice($images, 0, 8)];
    }

    private function downloadMedia(string $url, Article $article, int $order, string $title): Media
    {
        $response = Http::timeout(60)->get($url)->throw(); $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
        $path = 'media/'.now()->format('Y/m').'/'.$article->slug.'-'.$order.'.'.$extension;
        Storage::disk('public')->put($path, $response->body()); $absolute = Storage::disk('public')->path($path); $dimensions = @getimagesize($absolute) ?: [1200, 630];
        return Media::query()->create(['disk' => 'public', 'path' => $path, 'original_name' => basename($path), 'mime_type' => $response->header('Content-Type', 'image/jpeg'), 'size' => strlen($response->body()), 'width' => $dimensions[0], 'height' => $dimensions[1], 'alt_text' => $title, 'uploaded_by' => $article->author_id, 'is_temporary' => false]);
    }

    private function xpath(string $html): DOMXPath
    {
        $document = new DOMDocument('1.0', 'UTF-8'); libxml_use_internal_errors(true); $document->loadHTML('<?xml encoding="utf-8" ?>'.$html); libxml_clear_errors(); return new DOMXPath($document);
    }
}
