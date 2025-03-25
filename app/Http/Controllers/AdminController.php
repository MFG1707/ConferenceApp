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
                $q->where('date', $request->date);
            });
        }

        // On paginate les résultats
        $participants = $query->orderBy('id', 'desc')->paginate(10);

        return view('admin.dashboard', compact('participants', 'conferences'));
    }

    /**
     * Exportation des participants au format CSV
     */
    public function exportCSV(Request $request)
    {
        // Reproduire la même logique de filtrage
        $query = Participant::with('conference');
        if ($request->filled('date')) {
            $query->whereHas('conference', function($q) use ($request) {
                $q->where('date', $request->date);
            });
        }
        $participants = $query->orderBy('id', 'desc')->get();

        $filename = 'participants_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Nom', 'Prénom', 'Téléphone', 'Email', 'Date de Conférence'];

        $callback = function() use ($participants, $columns) {
            $file = fopen('php://output', 'w');
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
