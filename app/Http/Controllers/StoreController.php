<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Category;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function show(Store $store, Request $request)
    {
        $products = $store->products()
            ->with('category');

        // Filter kategori
        if ($request->category) {
            $products->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Sorting
        if ($request->sort) {
            match ($request->sort) {
                'price_asc' => $products->orderBy('price', 'asc'),
                'price_desc' => $products->orderBy('price', 'desc'),
                'newest' => $products->latest(),
                default => $products->latest(),
            };
        }

        $categories = Category::whereHas('products', function ($q) use ($store) {
            $q->where('store_id', $store->id);
        })->get();

        return view('stores.show', [
            'store' => $store,
            'products' => $products->paginate(12)->appends($request->query()),
            'categories' => $categories
        ]);
    }
}
