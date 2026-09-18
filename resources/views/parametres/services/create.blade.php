@extends('layouts.app')
@section('title', 'Ajouter un service')

@section('content')
@include('parametres.services.form', ['service' => null, 'action' => route('parametres.services.store'), 'titre' => 'Ajouter un service'])
@endsection
