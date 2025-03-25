<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Conference;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ParticipantController extends Controller
{
    // Affiche le formulaire d'inscription
    public function create()
    {
        // Récupère toutes les conférences disponibles
        $conferences = Conference::where('places_disponibles', '>', 0)->get();
        return view('inscription', compact('conferences'));
    }

    // Traite l'inscription et effectue la validation
    public function store(Request $request)
{
    // Validation des données
    $validatedData = $request->validate([
        'nom'           => 'required|string|max:255',
        'prenom'        => 'required|string|max:255',
        'telephone'     => 'required|string|max:20',
        'email'         => 'required|email|unique:participants,email',
        'conference_id' => 'required|exists:conferences,id',
    ]);

    // Vérifier que la conférence dispose encore de places
    $conference = Conference::findOrFail($validatedData['conference_id']);
    if ($conference->places_disponibles <= 0) {
        return back()->withErrors(['conference_id' => 'Plus de places disponibles pour cette conférence.'])->withInput();
    }

    // Exécution en transaction pour éviter des erreurs
    \DB::transaction(function () use ($validatedData, $conference) {
        // Création de l'inscription
        $participant = Participant::create([
            'nom' => $validatedData['nom'],
            'prenom' => $validatedData['prenom'],
            'telephone' => $validatedData['telephone'],
            'email' => strtolower($validatedData['email']), // Normalisation pour PostgreSQL
            'conference_id' => $validatedData['conference_id'],
        ]);

        // Générer le QR Code
        $qrCodePath = 'qrcodes/' . $participant->id . '.png';
        Storage::disk('public')->put($qrCodePath, QrCode::format('png')->size(200)->generate((string) $participant->id));
        $participant->update(['qr_code' => $qrCodePath]);

        // Envoi d'un email de confirmation
        Mail::raw("Votre inscription à la conférence est confirmée. Veuillez trouver votre QR Code en pièce jointe.", function ($message) use ($participant, $qrCodePath) {
            $message->to($participant->email)
                ->subject('Confirmation d’inscription')
                ->attach(storage_path('app/public/' . $qrCodePath));
        });

        // Décrémentation des places disponibles
        $conference->decrement('places_disponibles');
    });

    return redirect()->back()->with('success', 'Inscription réussie ! Un email de confirmation vous a été envoyé.');
}

}
