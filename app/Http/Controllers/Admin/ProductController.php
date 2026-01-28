<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Product::with('category');

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        if ($request->stock === 'low') {
            $query->where('stock', '<=', 10)->where('stock', '>', 0);
        } elseif ($request->stock === 'out') {
            $query->where('stock', 0);
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::all();

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category', 'stock']),
        ]);
    }

    public function create(): Response
    {
        $categories = Category::all();

        return Inertia::render('Admin/Products/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string',
            'sizes' => 'nullable|array',
            'colors' => 'nullable|array',
            'material' => 'nullable|string|max:255',
            'stock' => 'required|integer|min:0',
            'featured' => 'boolean',
            'active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(5);

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): Response
    {
        $categories = Category::all();

        return Inertia::render('Admin/Products/Edit', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string',
            'sizes' => 'nullable|array',
            'colors' => 'nullable|array',
            'material' => 'nullable|string|max:255',
            'stock' => 'required|integer|min:0',
            'featured' => 'boolean',
            'active' => 'boolean',
        ]);

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function updateStock(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $product->update(['stock' => $request->stock]);

        return back()->with('success', 'Stock updated successfully.');
    }
}
