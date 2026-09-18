@extends('layouts.app')
@section('title', 'Paramètres')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Paramètres</h1>
    <p class="text-sm text-slate-500 mt-0.5">Gestion des listes de référence de l'application</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

    @php
        $cards = [
            ['parametres.utilisateurs.index', 'ti-users-group', 'Utilisateurs', 'Comptes, rôles et accès à l\'application', $counts['users'].' comptes', '#dc2626'],
            ['parametres.grades.index', 'ti-award', 'Grades', 'Grilles de grade et taux de bail associés', $counts['grades'].' grades', '#F77F00'],
            ['parametres.localites.index', 'ti-map-pin', 'Localités', 'Villes et zones géographiques', $counts['localites'].' localités', '#009A44'],
            ['parametres.services.index', 'ti-building-community', 'Services', 'Directions et services de la Police', $counts['services'].' services', '#1a2440'],
            ['parametres.nomenclatures.index', 'ti-list-details', 'Statuts & types', 'Libellés et couleurs des statuts/types métier', $counts['nomenclatures'].' valeurs · '.$counts['categories'].' catégories', '#6366f1'],
        ];
    @endphp

    @foreach($cards as [$route, $icon, $titre, $desc, $meta, $couleur])
    <a href="{{ route($route) }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition group">
        <div class="flex items-start gap-4">
            <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:{{ $couleur }}1f;">
                <i class="ti {{ $icon }} text-2xl" style="color:{{ $couleur }};"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h2 class="text-sm font-bold text-slate-800 group-hover:text-orange-600 transition">{{ $titre }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">{{ $desc }}</p>
                <p class="text-xs font-semibold text-slate-400 mt-2">{{ $meta }}</p>
            </div>
            <i class="ti ti-chevron-right text-slate-300 group-hover:text-orange-500 transition"></i>
        </div>
    </a>
    @endforeach

</div>

<div class="mt-6 rounded-2xl bg-blue-50 border border-blue-100 p-4 flex items-start gap-3 text-sm text-blue-800">
    <i class="ti ti-info-circle text-lg text-blue-500 mt-0.5"></i>
    <div>
        <p class="font-semibold">À propos des « Statuts & types »</p>
        <p class="text-xs mt-0.5 text-blue-700">Vous pouvez modifier librement les libellés, couleurs et l'ordre d'affichage. Les valeurs marquées « système » pilotent la logique métier : leur code n'est pas modifiable et elles ne sont pas supprimables. Ajouter une nouvelle valeur la rend disponible à l'affichage, mais ne crée aucune nouvelle règle de gestion (cela nécessite du développement).</p>
    </div>
</div>

@endsection
