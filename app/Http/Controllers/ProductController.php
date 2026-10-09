<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /** Catalogue avec filtres catégorie / puissance / prix / disponibilité. */
    public function index(Request $request)
    {
        $query = Product::with('category')->where('is_active', true);

        if ($request->filled('category')) {
            $query->where('category_id', $request->integer('category'));
        }

        if ($request->filled('min_power')) {
            $query->where('power_w', '>=', $request->integer('min_power'));
        }

        if ($request->filled('max_price')) {
            $query->where('daily_price', '<=', $request->float('max_price'));
        }

        if ($request->filled('available') && $request->boolean('available')) {
            $query->where('quantity', '>', 0);
        }

        switch ($request->input('sort', 'name')) {
            case 'price_asc':
                $query->orderBy('daily_price');
                break;
            case 'price_desc':
                $query->orderByDesc('daily_price');
                break;
            case 'power_desc':
                $query->orderByDesc('power_w');
                break;
            default:
                $query->orderBy('name');
        }

        $products = $query->paginate(9)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }

        return view('products.show', compact('product'));
    }
}
