@extends('layouts.app')
@section('title', 'Modifier l\'utilisateur')

@section('content')
@include('parametres.utilisateurs.form', ['user' => $user, 'action' => route('parametres.utilisateurs.update', $user), 'titre' => 'Modifier l\'utilisateur'])
@endsection
