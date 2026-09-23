<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    private function allow(): void { abort_unless(in_array(request()->user()->role, ['super_admin', 'admin'], true), 403); }
    public function index() { $this->allow(); return view('admin.reference.index', ['type' => 'category', 'items' => Category::withCount('articles')->orderBy('sort_order')->get()]); }
    public function create() { $this->allow(); return view('admin.reference.form', ['type' => 'category', 'item' => new Category]); }
    public function store(Request $request) { $this->allow(); $data = $request->validate(['name' => 'required|max:100', 'slug' => 'nullable|max:100|unique:categories,slug', 'description' => 'nullable|max:1000', 'is_active' => 'boolean', 'sort_order' => 'integer|min:0']); $data['slug'] = $data['slug'] ?? Str::slug($data['name']); $item = Category::create($data); AuditLog::record('category.created', $item); return redirect()->route('admin.categories.index')->with('status', 'Kategori dibuat.'); }
    public function edit(Category $category) { $this->allow(); return view('admin.reference.form', ['type' => 'category', 'item' => $category]); }
    public function update(Request $request, Category $category) { $this->allow(); $data = $request->validate(['name' => 'required|max:100', 'slug' => 'required|max:100|unique:categories,slug,'.$category->id, 'description' => 'nullable|max:1000', 'is_active' => 'boolean', 'sort_order' => 'integer|min:0']); $category->update($data); AuditLog::record('category.updated', $category); return redirect()->route('admin.categories.index')->with('status', 'Kategori diperbarui.'); }
    public function destroy(Category $category) { $this->allow(); if ($category->articles()->exists()) return back()->withErrors(['category' => 'Kategori masih dipakai artikel. Pindahkan artikel ke kategori lain terlebih dahulu.']); AuditLog::record('category.deleted', $category); $category->delete(); return back()->with('status', 'Kategori dihapus.'); }
}
