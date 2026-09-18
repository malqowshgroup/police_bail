@extends('layouts.app')
@section('title', 'Modifier la valeur')

@section('content')
@include('parametres.nomenclatures.form', [
    'nomenclature' => $nomenclature,
    'action' => route('parametres.nomenclatures.update', $nomenclature),
    'titre' => 'Modifier la valeur',
    'categorie' => $nomenclature->categorie,
    'categories' => \App\Models\Nomenclature::CATEGORIES,
])
@endsection
