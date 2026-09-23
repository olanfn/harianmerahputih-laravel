<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; use App\Models\Article;
class DashboardController extends Controller { public function __invoke() { $query=Article::query(); if (request()->user()->role==='writer') $query->where('author_id',request()->user()->id); return view('admin.dashboard',['articleCount'=>(clone $query)->count(),'draftCount'=>(clone $query)->where('status','draft')->count(),'reviewCount'=>(clone $query)->where('status','review')->count()]); } }