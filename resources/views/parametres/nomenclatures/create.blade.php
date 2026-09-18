@extends('layouts.app')
@section('title', 'Ajouter une valeur')

@section('content')
@include('parametres.nomenclatures.form', [
    'nomenclature' => null,
    'action' => route('parametres.nomenclatures.store'),
    'titre' => 'Ajouter une valeur',
    'categorie' => $categorie,
    'categories' => $categories,
])
@endsection
