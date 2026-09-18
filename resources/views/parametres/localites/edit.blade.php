@extends('layouts.app')
@section('title', 'Modifier la localité')

@section('content')
@include('parametres.localites.form', ['localite' => $localite, 'action' => route('parametres.localites.update', $localite), 'titre' => 'Modifier la localité'])
@endsection
