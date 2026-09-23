<?php
namespace App\Http\Requests\Admin;
use Illuminate\Validation\Rule;
class UpdateArticleRequest extends StoreArticleRequest { public function authorize(): bool { return $this->user()?->can('update', $this->route('adminArticle')) ?? false; } public function rules(): array { $rules=parent::rules(); $rules['slug']=['nullable','string','max:255',Rule::unique('articles','slug')->ignore($this->route('adminArticle'))]; return $rules; } }