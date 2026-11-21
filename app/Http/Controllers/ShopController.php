<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $base = fn () => Product::where('is_active', true)->where('stock', '>', 0);

        $query = $base()->search($request->q);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->boolean('promo')) {
            $query->onSale();
        }

        $products = $query->latest()->paginate(12)->withQueryString();

        $categories = $base()->whereNotNull('category')->where('category', '!=', '')
            ->selectRaw('category, COUNT(*) as c')->groupBy('category')->orderBy('category')->pluck('c', 'category');

        $promoCount = $base()->onSale()->count();
        $totalCount = $base()->count();

        return view('shop.index', compact('products', 'categories', 'promoCount', 'totalCount'));
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        $related = Product::where('is_active', true)->where('stock', '>', 0)
            ->where('id', '!=', $product->id)
            ->when($product->category, fn ($q) => $q->where('category', $product->category))
            ->latest()->take(4)->get();

        return view('shop.show', compact('product', 'related'));
    }
}
