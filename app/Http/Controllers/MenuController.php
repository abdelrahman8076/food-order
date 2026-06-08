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

    public function show(string $slug)
    {
        $item = Item::where('slug', $slug)->with('category')->firstOrFail();

        $related = Item::where('category_id', $item->category_id)
            ->where('id', '!=', $item->id)
            ->where('is_available', true)
            ->orderBy('sort_order')
            ->limit(4)
            ->get();

        return view('menu.show', compact('item', 'related'));
    }
}
