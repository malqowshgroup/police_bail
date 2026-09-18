@extends('layouts.app')
@section('title', 'Ajouter un grade')

@section('content')
@include('parametres.grades.form', ['grade' => null, 'action' => route('parametres.grades.store'), 'titre' => 'Ajouter un grade'])
@endsection
