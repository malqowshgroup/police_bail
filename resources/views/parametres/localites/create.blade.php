@extends('layouts.app')
@section('title', 'Ajouter une localité')

@section('content')
@include('parametres.localites.form', ['localite' => null, 'action' => route('parametres.localites.store'), 'titre' => 'Ajouter une localité'])
@endsection
