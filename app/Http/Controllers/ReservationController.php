<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Models\Pack;
use App\Models\Quote;
use App\Models\Reservation;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;

class ReservationController extends Controller
{
    /** Formulaire de réservation. */
    public function create(Request $request = null)
    {
        $packs = Pack::where('is_active', true)->orderBy('price_per_day')->get();
        $quote = null;

        if ($request && $request->filled('quote_id')) {
            $quote = Quote::find($request->integer('quote_id'));
        }

        return view('reservations.create', [
            'packs' => $packs,
            'quote' => $quote,
            'selectedPackId' => $request?->integer('pack_id') ?: $quote?->recommended_pack_id,
        ]);
    }

    /** Enregistrement de la demande + vérification de disponibilité. */
    public function store(StoreReservationRequest $request)
    {
        $data = $request->validated();

        $pack = isset($data['pack_id']) ? Pack::find($data['pack_id']) : null;

        // Disponibilité du pack sur la période choisie
        if ($pack) {
            $available = $pack->availableForPeriod($data['start_date'], $data['end_date']);
            if ($available <= 0) {
                return back()
                    ->withInput()
                    ->withErrors(['pack_id' => 'Ce pack n\'est plus disponible pour les dates sélectionnées. Merci de choisir une autre période ou un autre pack.']);
            }
        }

        // Calcul du prix estimé (jours x prix journalier) + frais livraison configurables
        $days = max(1, Carbon::parse($data['start_date'])->diffInDays(Carbon::parse($data['end_date'])) + 1);
        $estimatedPrice = $pack ? $days * (float) $pack->price_per_day : null;
        $deposit = $pack ? (float) $pack->deposit : null;
        $deliveryFee = null;

        if (!empty($data['delivery_required'])) {
            $deliveryFee = (float) Setting::get('default_delivery_fee', 30);
            $estimatedPrice = $estimatedPrice !== null ? $estimatedPrice + $deliveryFee : $deliveryFee;
        }

        $reservation = Reservation::create([
            ...$data,
            'user_id' => auth()->id(),
            'status' => 'pending',
            'estimated_price' => $estimatedPrice,
            'deposit_amount' => $deposit,
            'delivery_fee' => $deliveryFee,
        ]);

        // Mémorise la dernière réservation pour la page de succès + message WhatsApp
        Session::put('last_reservation_id', $reservation->id);

        return redirect()->route('reservation.success', $reservation);
    }

    /** Page de confirmation après envoi de la demande. */
    public function success(Reservation $reservation)
    {
        $reservation->load(['pack', 'quote.items']);

        return view('reservations.success', compact('reservation'));
    }
}
