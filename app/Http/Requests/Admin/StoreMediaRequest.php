<?php
namespace App\Http\Requests\Admin;
use Illuminate\Foundation\Http\FormRequest;
class StoreMediaRequest extends FormRequest { public function authorize(): bool { return $this->user() !== null; } public function rules(): array { return ['image'=>['required','image','mimes:jpg,jpeg,png,webp','max:10240','dimensions:min_width=100,min_height=100,max_width=8000,max_height=8000']]; } }