<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) { return; }
        $categories = collect(config('news.categories'))->mapWithKeys(function (array $category, int $index) {
            return [$category['slug'] => Category::query()->updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'description' => 'Kategori demonstrasi '.$category['name'].'.',
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ],
            )];
        });

        $tags = collect(['Demonstrasi', 'Indonesia', 'Redaksi', 'Pengembangan', 'Publik'])->mapWithKeys(function (string $name) {
            $slug = Str::slug($name);

            return [$slug => Tag::query()->updateOrCreate(['slug' => $slug], ['name' => $name])];
        });

        foreach (['super_admin' => 'superadmin', 'admin' => 'admin', 'editor' => 'editor', 'writer' => 'writer'] as $role => $account) {
            User::query()->updateOrCreate(['email' => $account.'@harianmerahputih.test'], ['name' => Str::title(str_replace('_', ' ', $role)), 'password' => 'password', 'role' => $role]);
        }

        $author = User::query()->updateOrCreate(
            ['email' => 'demo@harianmerahputih.test'],
            ['name' => 'Redaksi Demonstrasi', 'password' => 'demo-only-not-production', 'role' => 'writer'],
        );

        $now = now();
        $articleSlugs = [];

        foreach (range(1, 12) as $number) {
            $slug = sprintf('demo-catatan-publik-%02d', $number);
            $articleSlugs[] = $slug;
            $article = Article::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $categories->values()->get(($number - 1) % $categories->count())->id,
                    'author_id' => $author->id,
                    'title' => sprintf('Demonstrasi: Catatan Publik %02d', $number),
                    'excerpt' => 'Konten ini adalah data demonstrasi untuk pengembangan portal, bukan berita nyata.',
                    'body' => "Ini adalah artikel demonstrasi untuk pengembangan Harian Merah Putih.\n\nKonten faktual belum dipublikasikan pada fase ini.",
                    'status' => 'published',
                    'published_at' => $now->copy()->subDays($number),
                    'view_count' => max(0, 130 - ($number * 7)),
                    'is_editor_pick' => $number <= 3,
                    'seo_title' => null,
                    'seo_description' => null,
                    'is_demo' => true,
                ],
            );

            $article->tags()->sync($tags->values()->slice(0, 2 + ($number % 3))->pluck('id'));
        }

        foreach (range(1, 2) as $number) {
            $draftSlug = sprintf('demo-draf-redaksi-%02d', $number);
            $articleSlugs[] = $draftSlug;
            Article::query()->updateOrCreate(
                ['slug' => $draftSlug],
                [
                    'category_id' => $categories->first()->id,
                    'author_id' => $author->id,
                    'title' => sprintf('Demonstrasi: Draf Redaksi %02d', $number),
                    'excerpt' => 'Draf demonstrasi internal, bukan berita nyata.',
                    'body' => 'Draf demonstrasi untuk pengembangan.',
                    'status' => 'draft',
                    'published_at' => null,
                    'is_demo' => true,
                ],
            );

            $scheduledSlug = sprintf('demo-jadwal-redaksi-%02d', $number);
            $articleSlugs[] = $scheduledSlug;
            Article::query()->updateOrCreate(
                ['slug' => $scheduledSlug],
                [
                    'category_id' => $categories->first()->id,
                    'author_id' => $author->id,
                    'title' => sprintf('Demonstrasi: Jadwal Redaksi %02d', $number),
                    'excerpt' => 'Artikel terjadwal demonstrasi, bukan berita nyata.',
                    'body' => 'Artikel terjadwal demonstrasi untuk pengembangan.',
                    'status' => 'scheduled',
                    'published_at' => $now->copy()->addDays($number),
                    'is_demo' => true,
                ],
            );
        }

        $articleSlugs[] = 'demo-arsip-redaksi';
        Article::query()->updateOrCreate(
            ['slug' => 'demo-arsip-redaksi'],
            [
                'category_id' => $categories->first()->id,
                'author_id' => $author->id,
                'title' => 'Demonstrasi: Arsip Redaksi',
                'excerpt' => 'Artikel arsip demonstrasi, bukan berita nyata.',
                'body' => 'Artikel arsip demonstrasi untuk pengembangan.',
                'status' => 'archived',
                'published_at' => $now->copy()->subDays(30),
                'is_demo' => true,
            ],
        );

        Article::query()->where('is_demo', true)->whereNotIn('slug', $articleSlugs)->delete();
    }
}
