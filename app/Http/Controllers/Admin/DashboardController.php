<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pack;
use App\Models\Product;
use App\Models\Reservation;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'products' => Product::count(),
            'packs' => Pack::count(),
            'pending' => Reservation::where('status', 'pending')->count(),
            'confirmed' => Reservation::where('status', 'confirmed')->count(),
            'revenue' => (float) Reservation::whereIn('status', ['confirmed', 'completed'])->sum('estimated_price'),
            'available_products' => Product::where('is_active', true)->where('quantity', '>', 0)->count(),
        ];

        $recentReservations = Reservation::with('pack')->latest()->take(8)->get();

        return view('admin.dashboard', compact('stats', 'recentReservations'));
    }
}
