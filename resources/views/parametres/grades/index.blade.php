@extends('layouts.app')
@section('title', 'Grades')

@section('content')

@include('parametres.partials.flash')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
            <a href="{{ route('parametres.index') }}" class="hover:text-orange-500">Paramètres</a>
            <i class="ti ti-chevron-right"></i><span>Grades</span>
        </div>
        <h1 class="text-2xl font-bold text-slate-800">Grades</h1>
        <p class="text-sm text-slate-500 mt-0.5">{{ $grades->total() }} grades · le taux est figé à la création d'un contrat</p>
    </div>
    <a href="{{ route('parametres.grades.create') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white shadow-sm transition hover:opacity-90 flex-shrink-0" style="background:#F77F00;">
        <i class="ti ti-plus text-base"></i> Ajouter un grade
    </a>
</div>

@include('parametres.partials.filtres', ['placeholder' => 'Rechercher un grade…', 'route' => 'parametres.grades.index'])

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    @if($grades->isEmpty())
    @include('parametres.partials.vide', ['label' => 'Aucun grade trouvé'])
    @else
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-gray-100" style="background:#f8f9fb;">
                    <th class="px-5 py-3">Grade</th>
                    <th class="px-5 py-3 text-right">Taux de bail</th>
                    <th class="px-5 py-3 text-center hidden sm:table-cell">Ordre</th>
                    <th class="px-5 py-3 text-center hidden md:table-cell">Policiers</th>
                    <th class="px-5 py-3 text-center hidden md:table-cell">Contrats</th>
                    <th class="px-5 py-3 text-center">Statut</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($grades as $grade)
                <tr class="hover:bg-slate-50/70 group">
                    <td class="px-5 py-3.5 font-semibold text-slate-800">{{ $grade->libelle }}</td>
                    <td class="px-5 py-3.5 text-right font-semibold" style="color:#F77F00;">{{ number_format($grade->taux_bail, 0, ',', ' ') }} F</td>
                    <td class="px-5 py-3.5 text-center hidden sm:table-cell text-slate-500">{{ $grade->ordre }}</td>
                    <td class="px-5 py-3.5 text-center hidden md:table-cell text-slate-600">{{ $grade->policiers_count }}</td>
                    <td class="px-5 py-3.5 text-center hidden md:table-cell text-slate-600">{{ $grade->contrats_bail_count }}</td>
                    <td class="px-5 py-3.5 text-center">@include('parametres.partials.actif', ['actif' => $grade->actif])</td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-end gap-1 opacity-60 group-hover:opacity-100 transition">
                            <a href="{{ route('parametres.grades.edit', $grade) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50" title="Modifier"><i class="ti ti-pencil text-base"></i></a>
                            <form method="POST" action="{{ route('parametres.grades.destroy', $grade) }}" onsubmit="return confirm('Supprimer le grade {{ addslashes($grade->libelle) }} ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-red-600 hover:bg-red-50" title="Supprimer"><i class="ti ti-trash text-base"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($grades->hasPages())
    <div class="px-5 py-3.5 border-t border-gray-100" style="background:#f8f9fb;">{{ $grades->links() }}</div>
    @endif
    @endif
</div>

@endsection
