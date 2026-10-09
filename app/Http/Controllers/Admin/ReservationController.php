<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::with(['pack', 'quote']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('customer_name', 'like', "%$s%")
                  ->orWhere('customer_phone', 'like', "%$s%")
                  ->orWhere('location', 'like', "%$s%");
            });
        }

        $reservations = $query->latest()->paginate(15)->withQueryString();

        return view('admin.reservations.index', compact('reservations'));
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['pack.products.category', 'quote.items', 'products.category']);

        return view('admin.reservations.show', compact('reservation'));
    }

    /** Changement de statut avec workflow simple. */
    public function updateStatus(Request $request, Reservation $reservation)
    {
        $data = $request->validate([
            'status' => ['required', 'in:' . implode(',', Reservation::STATUSES)],
        ]);

        $newStatus = $data['status'];

        // re-confirmer une réservation : revérifier la disponibilité du pack
        if ($newStatus === 'confirmed' && $reservation->pack) {
            $available = $reservation->pack->availableForPeriod($reservation->start_date, $reservation->end_date);
            // on exclut la réservation elle-même du compte
            $otherReservations = Reservation::query()
                ->where('pack_id', $reservation->pack_id)
                ->where('id', '!=', $reservation->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->where('start_date', '<=', $reservation->end_date)
                ->where('end_date', '>=', $reservation->start_date)
                ->count();

            if ($reservation->pack->quantity - $otherReservations <= 0) {
                return back()->withErrors(['status' => 'Impossible de confirmer : le pack est déjà réservé pour cette période.']);
            }
        }

        $reservation->update(['status' => $newStatus]);

        return back()->with('success', 'Statut mis à jour : ' . $reservation->statusLabel());
    }
}
