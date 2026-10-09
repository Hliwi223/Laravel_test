<?php

namespace App\Http\Controllers;

use App\Models\Pack;

class PackController extends Controller
{
    public function index()
    {
        $packs = Pack::with('products.category')->where('is_active', true)->orderBy('price_per_day')->get();

        return view('packs.index', compact('packs'));
    }

    public function show(Pack $pack)
    {
        if (!$pack->is_active) {
            abort(404);
        }

        $pack->load('products.category');

        return view('packs.show', compact('pack'));
    }
}
