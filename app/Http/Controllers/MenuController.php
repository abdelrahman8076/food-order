<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;

class MenuController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)
            ->with(['activeItems'])
            ->orderBy('sort_order')
            ->get();
        return view('menu.index', compact('categories'));
    }

    public function category(string $slug)
    {
        $category = Category::where('slug', $slug)->where('is_active', true)
            ->with('activeItems')
            ->firstOrFail();
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        return view('menu.category', compact('category', 'categories'));
    }
}
