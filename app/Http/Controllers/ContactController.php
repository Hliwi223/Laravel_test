<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact.index');
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ], [
            'name.required' => 'Veuillez indiquer votre nom.',
            'email.required' => 'Veuillez indiquer votre email.',
            'email.email' => 'L\'adresse email n\'est pas valide.',
            'message.required' => 'Veuillez écrire votre message.',
        ]);

        // MVP : pas de serveur SMTP configuré par défaut -> on journalise la demande.
        // Décommenter pour activer l'envoi réel après configuration MAIL_* dans .env.
        // Mail::raw($data['message'], fn ($m) => $m->to(config('mail.from.address'))->subject($data['subject'])->from($data['email'], $data['name']));
        logger()->info('Message de contact reçu', $data);

        return back()->with('success', 'Votre message a bien été envoyé. Notre équipe vous répondra rapidement.');
    }
}
