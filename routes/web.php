<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MediaLibraryController;
use App\Http\Controllers\Admin\MediaUploadController;
use App\Http\Controllers\Admin\PasswordResetController;
use App\Http\Controllers\Admin\RedactionPageController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ShowcaseController as AdminShowcaseController;
use App\Http\Controllers\Admin\SiteContactController;
use App\Http\Controllers\DistributionController;
use App\Http\Controllers\PublicNewsController;
use App\Http\Controllers\ReadinessController;
use App\Http\Controllers\RedactionController;
use App\Http\Controllers\ShowcaseController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicNewsController::class, 'index'])->name('home');
Route::get('/kategori/{category:slug}', [PublicNewsController::class, 'category'])->name('news.category');
Route::get('/berita/{slug}', [PublicNewsController::class, 'show'])->name('news.show');
Route::get('/cari', [PublicNewsController::class, 'search'])->name('news.search');
Route::get('/tag/{tag:slug}', [PublicNewsController::class, 'tag'])->name('news.tag');
Route::get('/indeks', [PublicNewsController::class, 'archive'])->name('news.index');
Route::get('/sitemap.xml', [DistributionController::class, 'sitemap'])->name('distribution.sitemap');
Route::get('/news-sitemap.xml', [DistributionController::class, 'newsSitemap'])->name('distribution.news-sitemap');
Route::get('/feed.xml', [DistributionController::class, 'rss'])->name('distribution.rss');
Route::get('/robots.txt', [DistributionController::class, 'robots'])->name('distribution.robots');
Route::get('/internal/readiness', ReadinessController::class)->name('internal.readiness');
Route::get('/foto-peristiwa', [ShowcaseController::class, 'photos'])->name('showcase.photos');
Route::get('/merah-putih-tv', [ShowcaseController::class, 'tv'])->name('showcase.tv');
Route::get('/redaksi', RedactionController::class)->name('redaction.show');
Route::get('/tentang-kami', [RedactionController::class, 'about'])->name('institutional.about');
Route::get('/pedoman-media-siber', [RedactionController::class, 'cyberMediaGuidelines'])->name('institutional.cyber-media-guidelines');
Route::get('/kebijakan-privasi', [RedactionController::class, 'privacy'])->name('institutional.privacy');
Route::get('/hubungi-redaksi', [RedactionController::class, 'contact'])->name('institutional.contact');

Route::prefix(config('news.admin_path'))->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'create'])->name('login');
        Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
        Route::get('/forgot-password', [PasswordResetController::class, 'requestForm'])->name('password.request');
        Route::post('/forgot-password', [PasswordResetController::class, 'send'])->middleware('throttle:3,5')->name('password.email');
        Route::get('/reset-password/{token}', [PasswordResetController::class, 'form'])->name('password.reset');
        Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
    });
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::post('/media', [MediaUploadController::class, 'store'])->name('media.store');
        Route::get('/media-library', [MediaLibraryController::class, 'index'])->name('media.index');
        Route::delete('/media-library/{media}', [MediaLibraryController::class, 'destroy'])->name('media.destroy');
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.index');
        Route::get('/redaksi', [RedactionPageController::class, 'edit'])->name('redaction.edit');
        Route::put('/redaksi', [RedactionPageController::class, 'update'])->name('redaction.update');
        Route::get('/halaman/{page:slug}/edit', [RedactionPageController::class, 'editPage'])->name('pages.edit');
        Route::put('/halaman/{page:slug}', [RedactionPageController::class, 'updatePage'])->name('pages.update');
        Route::get('/site-contact', [SiteContactController::class, 'edit'])->name('site-contact.edit');
        Route::put('/site-contact', [SiteContactController::class, 'update'])->name('site-contact.update');
        Route::bind('adminArticle', fn ($value) => \App\Models\Article::query()->findOrFail($value));
        Route::get('articles/{adminArticle}/preview', [ArticleController::class, 'preview'])->name('articles.preview');
        Route::get('articles/{adminArticle}/revisions', [ArticleController::class, 'revisions'])->name('articles.revisions');
        Route::post('articles/{adminArticle}/revisions/{revision}/restore', [ArticleController::class, 'restore'])->name('articles.revisions.restore');
        Route::resource('articles', ArticleController::class)->parameters(['articles' => 'adminArticle'])->except('show');
        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('tags', TagController::class)->except('show');
        Route::resource('users', UserController::class)->except('show');
        Route::get('/showcase/{type}', [AdminShowcaseController::class, 'index'])->name('showcase.index');
        Route::get('/showcase/{type}/create', [AdminShowcaseController::class, 'create'])->name('showcase.create');
        Route::post('/showcase/{type}', [AdminShowcaseController::class, 'store'])->name('showcase.store');
        Route::get('/showcase/{type}/{id}/edit', [AdminShowcaseController::class, 'edit'])->name('showcase.edit');
        Route::put('/showcase/{type}/{id}', [AdminShowcaseController::class, 'update'])->name('showcase.update');
        Route::delete('/showcase/{type}/{id}', [AdminShowcaseController::class, 'destroy'])->name('showcase.destroy');
    });
});
