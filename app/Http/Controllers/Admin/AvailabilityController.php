<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pack;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AvailabilityController extends Controller
{
    /** Vue calendrier mensuel des réservations par pack. */
    public function index(Request $request)
    {
        $month = Carbon::parse($request->input('month', Carbon::now()->format('Y-m-01')))->startOfMonth();

        $packs = Pack::where('is_active', true)->orderBy('name')->get();

        $reservations = Reservation::with('pack')
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('start_date', '<=', $month->copy()->endOfMonth())
            ->where('end_date', '>=', $month->copy()->startOfMonth())
            ->get();

        // Disponibilité par pack et par jour du mois
        $grid = [];
        for ($day = $month->copy(); $day->lte($month->copy()->endOfMonth()); $day->addDay()) {
            foreach ($packs as $pack) {
                $busy = $reservations
                    ->where('pack_id', $pack->id)
                    ->filter(fn (Reservation $r) => $day->betweenIncluded($r->start_date, $r->end_date))
                    ->count();
                $grid[$pack->id][$day->toDateString()] = [
                    'total' => $pack->quantity,
                    'busy' => $busy,
                    'free' => max(0, $pack->quantity - $busy),
                ];
            }
        }

        return view('admin.availability.index', [
            'month' => $month,
            'packs' => $packs,
            'grid' => $grid,
            'reservations' => $reservations,
        ]);
    }
}
