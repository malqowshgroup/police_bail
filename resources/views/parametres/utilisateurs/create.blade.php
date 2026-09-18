@extends('layouts.app')
@section('title', 'Ajouter un utilisateur')

@section('content')
@include('parametres.utilisateurs.form', ['user' => null, 'action' => route('parametres.utilisateurs.store'), 'titre' => 'Ajouter un utilisateur'])
@endsection
