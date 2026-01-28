<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class StoreController extends Controller
{
    public function index(): Response
    {
        $featuredProducts = Product::with('category')
            ->active()
            ->featured()
            ->inStock()
            ->latest()
            ->take(8)
            ->get();

        $categories = Category::withCount(['products' => function ($query) {
            $query->active()->inStock();
        }])->get();

        return Inertia::render('Store/Home', [
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
        ]);
    }

    public function products(?string $category = null): Response
    {
        $query = Product::with('category')->active()->inStock();

        if ($category) {
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('slug', $category);
            });
        }

        $products = $query->latest()->paginate(12);
        $categories = Category::withCount(['products' => function ($query) {
            $query->active()->inStock();
        }])->get();

        $currentCategory = $category ? Category::where('slug', $category)->first() : null;

        return Inertia::render('Store/Products', [
            'products' => $products,
            'categories' => $categories,
            'currentCategory' => $currentCategory,
        ]);
    }

    public function show(string $slug): Response
    {
        $product = Product::with('category')
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        $relatedProducts = Product::with('category')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->inStock()
            ->take(4)
            ->get();

        return Inertia::render('Store/ProductDetail', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }
}
