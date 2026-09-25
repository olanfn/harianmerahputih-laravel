<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Models\Article;
use App\Models\ArticleRedirect;
use App\Models\Category;
use App\Models\Tag;
use App\Support\ArticleContentRenderer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PublicNewsController extends Controller
{
    public function index(): View
    {
        $published = $this->publishedArticles();
        $panel = request()->string('panel')->toString();
        $panel = in_array($panel, ['popular', 'latest', 'editor'], true) ? $panel : 'popular';
        $popular = (clone $published)->reorder()->orderByDesc('view_count')->orderByDesc('published_at')->take(5)->get();
        $editorPicks = (clone $published)->where('is_editor_pick', true)->reorder()->latest('published_at')->take(5)->get();
        $headline = $editorPicks->first() ?: (clone $published)->first();
        $secondaryQuery = clone $published;
        $latestQuery = clone $published;
        if ($headline) {
            $secondaryQuery->whereKeyNot($headline->getKey());
            $latestQuery->whereKeyNot($headline->getKey());
        }
        $secondary = $secondaryQuery->take(3)->get();
        $latest = $latestQuery->skip(3)->take(10)->get();
        $panelArticles = match ($panel) {
            'latest' => (clone $published)->take(5)->get(),
            'editor' => $editorPicks,
            default => $popular,
        };
        $viralCategory = Category::query()->where('slug', 'viral')->first();
        $viralArticles = $viralCategory
            ? $viralCategory->articles()->published()->with(['category', 'featuredMedia.media'])->latest('published_at')->take(5)->get()
            : collect();

        return view('home', [
            'headline' => $headline,
            'secondary' => $secondary,
            'tickerArticles' => (clone $published)->take(4)->get(),
            'latest' => $latest,
            'popular' => $popular,
            'editorPicks' => $editorPicks,
            'panelArticles' => $panelArticles,
            'activePanel' => $panel,
            'categories' => $this->categories(),
            'viralArticles' => $viralArticles,
        ]);
    }

    public function category(Category $category): View
    {
        $tab = request()->string('tab')->toString();
        $tab = in_array($tab, ['latest', 'popular', 'editor'], true) ? $tab : 'latest';
        $articles = $category->articles()->published()->with(['category', 'tags', 'featuredMedia.media']);

        match ($tab) {
            'popular' => $articles->reorder()->orderByDesc('view_count')->orderByDesc('published_at'),
            'editor' => $articles->where('is_editor_pick', true)->reorder()->latest('published_at'),
            default => $articles->reorder()->latest('published_at'),
        };

        return view('news.category', [
            'category' => $category,
            'articles' => $articles->paginate(9)->withQueryString(),
            'activeTab' => $tab,
            'categories' => $this->categories(),
        ]);
    }

    public function show(string $slug): View|RedirectResponse
    {
        $article = Article::query()->published()->where('slug', $slug)->first();

        if (! $article) {
            $redirect = ArticleRedirect::query()->with(['article' => fn ($query) => $query->published()])->where('old_slug', $slug)->first();

            if ($redirect?->article) {
                return redirect()->route('news.show', $redirect->article->slug, 301);
            }

            abort(404);
        }

        $article->load(['category', 'tags', 'author', 'mediaLinks.media', 'featuredMedia.media']);
        $viewedArticles = session()->get('viewed_articles', []);
        if (! in_array($article->id, $viewedArticles, true)) {
            $article->increment('view_count');
            $viewedArticles[] = $article->id;
            session()->put('viewed_articles', array_slice($viewedArticles, -100));
        }

        return view('news.show', [
            'article' => $article,
            'contentSegments' => app(ArticleContentRenderer::class)->segments($article),
            'related' => $article->category->articles()->published()->with(['category', 'featuredMedia.media'])->where('articles.id', '!=', $article->getKey())->latest('published_at')->take(3)->get(),
            'categories' => $this->categories(),
        ]);
    }

    public function search(SearchRequest $request): View
    {
        $term = $request->validated('q');
        $articles = $this->publishedArticles();

        if ($term) {
            $articles->where(function (Builder $query) use ($term): void {
                $query->where('title', 'like', '%'.$term.'%')
                    ->orWhere('excerpt', 'like', '%'.$term.'%')
                    ->orWhere('body', 'like', '%'.$term.'%');
            });
        }

        return view('news.search', [
            'term' => $term,
            'articles' => $articles->latest('published_at')->paginate(9)->withQueryString(),
            'categories' => $this->categories(),
        ]);
    }

    public function tag(Tag $tag): View
    {
        return view('news.tag', [
            'tag' => $tag,
            'articles' => $tag->articles()->published()->with(['category', 'tags', 'featuredMedia.media'])->latest('published_at')->paginate(9)->withQueryString(),
            'categories' => $this->categories(),
        ]);
    }

    public function archive(): View
    {
        return view('news.index', [
            'articles' => $this->publishedArticles()->latest('published_at')->paginate(15)->withQueryString(),
            'categories' => $this->categories()->loadCount(['articles' => fn (Builder $query) => $query->published()]),
        ]);
    }

    private function publishedArticles(): Builder
    {
        return Article::query()->published()->with(['category', 'tags', 'author', 'featuredMedia.media'])->latest('published_at');
    }

    private function categories()
    {
        return Category::query()->active()->orderBy('sort_order')->get();
    }
}
