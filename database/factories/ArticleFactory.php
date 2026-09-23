<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ArticleFactory extends Factory
{
    public function definition(): array
    {
        $title = 'Demonstrasi: '.fake()->sentence(7);

        return [
            'category_id' => Category::factory(),
            'author_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numerify('####'),
            'excerpt' => 'Konten ini adalah data demonstrasi untuk pengembangan portal, bukan berita nyata.',
            'body' => "Ini adalah artikel demonstrasi untuk pengembangan Harian Merah Putih.\n\nKonten faktual belum dipublikasikan pada fase ini.",
            'status' => 'draft',
            'published_at' => null,
            'view_count' => 0,
            'is_editor_pick' => false,
            'seo_title' => null,
            'seo_description' => null,
            'is_demo' => true,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['status' => 'published', 'published_at' => now()->subDays(fake()->numberBetween(0, 20))]);
    }

    public function scheduled(): static
    {
        return $this->state(fn () => ['status' => 'scheduled', 'published_at' => now()->addDays(fake()->numberBetween(1, 5))]);
    }

    public function archived(): static
    {
        return $this->state(fn () => ['status' => 'archived', 'published_at' => now()->subDays(30)]);
    }
}
