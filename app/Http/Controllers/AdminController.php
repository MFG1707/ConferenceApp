<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Conference;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    /**
     * Affiche le tableau de bord avec la liste des participants et le filtre par date.
     */
    public function dashboard(Request $request)
    {
        // Récupérer la liste des dates de conférences pour le filtre
        $conferences = Conference::orderBy('date')->get();

        // Si un filtre de date est appliqué, on récupère les participants de cette conférence
        $query = Participant::with('conference');

        if ($request->filled('date')) {
            $query->whereHas('conference', function($q) use ($request) {
                $q->whereDate('date', $request->date); // Utilisation de whereDate pour PostgreSQL
            });
        }

        // Paginer les résultats
        $participants = $query->orderBy('id', 'desc')->paginate(10);

        return view('admin.dashboard', compact('participants', 'conferences'));
    }

    /**
     * Exportation des participants au format CSV
     */
    public function exportCSV(Request $request)
    {
        // Appliquer le même filtre que dans le dashboard
        $query = Participant::with('conference');
        if ($request->filled('date')) {
            $query->whereHas('conference', function($q) use ($request) {
                $q->whereDate('date', $request->date); // whereDate pour s'assurer que seule la date est comparée
            });
        }
        $participants = $query->orderBy('id', 'desc')->get();

        $filename = 'participants_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            "Content-Type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Nom', 'Prénom', 'Téléphone', 'Email', 'Date de Conférence'];

        $callback = function() use ($participants, $columns) {
            $file = fopen('php://output', 'w');
            // Forcer l'encodage en UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // Ajoute un BOM UTF-8 pour éviter les problèmes d'affichage

            fputcsv($file, $columns);

            foreach ($participants as $participant) {
                fputcsv($file, [
                    $participant->id,
                    $participant->nom,
                    $participant->prenom,
                    $participant->telephone,
                    $participant->email,
                    \Carbon\Carbon::parse($participant->conference->date)->format('d/m/Y')
                ]);
            }

            fclose($file);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }
}
