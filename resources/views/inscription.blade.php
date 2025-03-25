<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription à la Conférence</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Inscription à la Conférence</h1>

    {{-- Affichage des erreurs de validation --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulaire d'inscription --}}
    <form method="POST" action="{{ route('inscription.store') }}">
        @csrf

        <div class="form-group">
            <label for="nom">Nom :</label>
            <input type="text" name="nom" id="nom" class="form-control" value="{{ old('nom') }}" required>
        </div>

        <div class="form-group">
            <label for="prenom">Prénom :</label>
            <input type="text" name="prenom" id="prenom" class="form-control" value="{{ old('prenom') }}" required>
        </div>

        <div class="form-group">
            <label for="telephone">Numero de Téléphone WhatsApp :</label>
            <input type="text" name="telephone" id="telephone" class="form-control" value="{{ old('telephone') }}" required>
        </div>

        <div class="form-group">
            <label for="email">Email :</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
        </div>

        <div class="form-group">
            <label for="conference_id">Date de la conférence :</label>
            <select name="conference_id" id="conference_id" class="form-control" required>
                <option value="">-- Sélectionnez une date --</option>
                @foreach ($conferences as $conference)
                    <option value="{{ $conference->id }}"
                        {{ old('conference_id') == $conference->id ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::parse($conference->date)->format('d/m/Y') }}
                        ({{ $conference->places_disponibles }} places disponibles)
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">S'inscrire</button>
    </form>
</div>
</body>
</html>
