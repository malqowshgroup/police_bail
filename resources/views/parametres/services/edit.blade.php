@extends('layouts.app')
@section('title', 'Modifier le service')

@section('content')
@include('parametres.services.form', ['service' => $service, 'action' => route('parametres.services.update', $service), 'titre' => 'Modifier le service'])
@endsection
