<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\RedactionPage;
use Illuminate\View\View;

class RedactionController extends Controller
{
    public function __invoke(): View
    {
        return $this->show('redaksi');
    }

    public function about(): View
    {
        return $this->show('tentang-kami');
    }

    public function cyberMediaGuidelines(): View
    {
        return $this->show('pedoman-media-siber');
    }

    public function privacy(): View
    {
        return $this->show('kebijakan-privasi');
    }

    public function contact(): View
    {
        return $this->show('hubungi-redaksi');
    }

    private function show(string $slug): View
    {
        $page = RedactionPage::query()->where('slug', $slug)->firstOrFail();
        $categories = Category::query()->active()->orderBy('sort_order')->get();
        $viralCategory = Category::query()->active()->where(function ($query): void {
            $query->where('slug', 'viral')->orWhereRaw('LOWER(name) = ?', ['viral']);
        })->first();

        return view('redaction.show', [
            'page' => $page,
            'title' => $page->title.' · Harian Merah Putih',
            'metaDescription' => $page->summary,
            'categories' => $categories,
            'topics' => $categories->take(5),
            'viralArticles' => $viralCategory
                ? Article::query()->published()->where('category_id', $viralCategory->id)->with(['category', 'featuredMedia.media'])->latest('published_at')->take(5)->get()
                : collect(),
        ]);
    }
}
