<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;

class HomeController extends Controller
{
    public function index()
    {
        $featured = Item::where('is_featured', true)->where('is_available', true)->with('category')->limit(8)->get();
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        return view('home', compact('featured', 'categories'));
    }
}
