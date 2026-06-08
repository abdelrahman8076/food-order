<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with('category');
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        $items = $query->orderBy('category_id')->orderBy('sort_order')->paginate(15);
        $categories = Category::orderBy('name')->get();
        return view('admin.items.index', compact('items', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);
        $validated['slug'] = Str::slug($validated['name']) . '-' . uniqid();
        $validated['is_available'] = $request->boolean('is_available');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('items', 'public');
        }

        Item::create($validated);
        return redirect()->route('admin.items.index')->with('success', 'Item created.');
    }

    public function edit(Item $item)
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);
        $validated['slug'] = Str::slug($validated['name']) . '-' . $item->id;
        $validated['is_available'] = $request->boolean('is_available');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('items', 'public');
        }

        $item->update($validated);
        return redirect()->route('admin.items.index')->with('success', 'Item updated.');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('admin.items.index')->with('success', 'Item deleted.');
    }
}
