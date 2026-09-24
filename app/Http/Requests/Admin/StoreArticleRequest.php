<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Article::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:articles,slug'],
            'excerpt' => ['required', 'string', 'max:1000'],
            'body' => ['required', 'string'],
            'status' => ['required', Rule::in(['draft', 'review', 'scheduled', 'published', 'archived'])],
            'published_at' => [
                'nullable',
                'date',
                Rule::requiredIf(fn (): bool => $this->input('status') === 'scheduled'),
                Rule::when($this->input('status') === 'scheduled', ['after:now']),
            ],
            'is_editor_pick' => ['sometimes', 'boolean'],
            'tag_ids' => ['array'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],
            'media' => ['array', 'max:20'],
            'media.*.id' => ['required', 'integer', 'exists:media,id'],
            'media.*.role' => ['required', Rule::in(['featured', 'gallery', 'inline'])],
            'media.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'media.*.alt_text' => ['nullable', 'string', 'max:255'],
            'media.*.caption' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
