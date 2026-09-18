@extends('layouts.app')
@section('title', 'Modifier le grade')

@section('content')
@include('parametres.grades.form', ['grade' => $grade, 'action' => route('parametres.grades.update', $grade), 'titre' => 'Modifier le grade'])
@endsection
