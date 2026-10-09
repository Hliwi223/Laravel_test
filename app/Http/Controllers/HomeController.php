<?php

namespace App\Http\Controllers;

use App\Models\Pack;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $popularPacks = Pack::where('is_active', true)->orderBy('price_per_day')->take(3)->get();
        $featuredProducts = Product::with('category')->where('is_active', true)->take(4)->get();

        return view('home.index', compact('popularPacks', 'featuredProducts'));
    }

    public function faq()
    {
        return view('faq.index');
    }

    public function conditions()
    {
        return view('conditions.index');
    }
}
