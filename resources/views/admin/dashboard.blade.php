<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord Admin</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Tableau de bord Admin</h1>

    {{-- Formulaire de filtrage par date --}}
    <form method="GET" action="{{ route('admin.dashboard') }}" class="form-inline mb-4">
        <div class="form-group mr-2">
            <label for="date" class="mr-2">Filtrer par date :</label>
            <select name="date" id="date" class="form-control">
                <option value="">-- Toutes les dates --</option>
                @foreach ($conferences as $conf)
                    <option value="{{ $conf->date }}" {{ request('date') == $conf->date ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::parse($conf->date)->format('d/m/Y') }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary mr-2">Filtrer</button>
        {{-- Bouton pour exporter en CSV --}}
        <a href="{{ route('admin.export_csv', ['date' => request('date')]) }}" class="btn btn-success">Exporter en CSV</a>
    </form>

    {{-- Tableau des participants --}}
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Téléphone</th>
                <th>Email</th>
                <th>Date de Conférence</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($participants as $participant)
                <tr>
                    <td>{{ $participant->id }}</td>
                    <td>{{ $participant->nom }}</td>
                    <td>{{ $participant->prenom }}</td>
                    <td>{{ $participant->telephone }}</td>
                    <td>{{ $participant->email }}</td>
                    <td>{{ \Carbon\Carbon::parse($participant->conference->date)->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Aucun participant trouvé.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination (si nécessaire) --}}
    <div class="d-flex justify-content-center">
        {{ $participants->withQueryString()->links() }}
    </div>
</div>
</body>
</html>
