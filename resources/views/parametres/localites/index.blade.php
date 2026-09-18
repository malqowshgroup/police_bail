@extends('layouts.app')
@section('title', 'Localités')

@section('content')

@include('parametres.partials.flash')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
            <a href="{{ route('parametres.index') }}" class="hover:text-orange-500">Paramètres</a>
            <i class="ti ti-chevron-right"></i><span>Localités</span>
        </div>
        <h1 class="text-2xl font-bold text-slate-800">Localités</h1>
        <p class="text-sm text-slate-500 mt-0.5">{{ $localites->total() }} localités</p>
    </div>
    <a href="{{ route('parametres.localites.create') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white shadow-sm transition hover:opacity-90 flex-shrink-0" style="background:#F77F00;">
        <i class="ti ti-plus text-base"></i> Ajouter une localité
    </a>
</div>

@include('parametres.partials.filtres', ['placeholder' => 'Rechercher une localité…', 'route' => 'parametres.localites.index'])

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    @if($localites->isEmpty())
    @include('parametres.partials.vide', ['label' => 'Aucune localité trouvée'])
    @else
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-gray-100" style="background:#f8f9fb;">
                    <th class="px-5 py-3">Code</th>
                    <th class="px-5 py-3">Localité</th>
                    <th class="px-5 py-3 text-center hidden md:table-cell">Logements</th>
                    <th class="px-5 py-3 text-center hidden md:table-cell">Policiers</th>
                    <th class="px-5 py-3 text-center hidden lg:table-cell">Propriétaires</th>
                    <th class="px-5 py-3 text-center">Statut</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($localites as $localite)
                <tr class="hover:bg-slate-50/70 group">
                    <td class="px-5 py-3.5"><span class="font-mono text-xs font-semibold text-slate-600 bg-slate-100 px-2 py-1 rounded-lg">{{ $localite->code ?? '—' }}</span></td>
                    <td class="px-5 py-3.5 font-semibold text-slate-800">{{ $localite->libelle }}</td>
                    <td class="px-5 py-3.5 text-center hidden md:table-cell text-slate-600">{{ $localite->logements_civils_count }}</td>
                    <td class="px-5 py-3.5 text-center hidden md:table-cell text-slate-600">{{ $localite->policiers_count }}</td>
                    <td class="px-5 py-3.5 text-center hidden lg:table-cell text-slate-600">{{ $localite->proprietaires_count }}</td>
                    <td class="px-5 py-3.5 text-center">@include('parametres.partials.actif', ['actif' => $localite->actif])</td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-end gap-1 opacity-60 group-hover:opacity-100 transition">
                            <a href="{{ route('parametres.localites.edit', $localite) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50" title="Modifier"><i class="ti ti-pencil text-base"></i></a>
                            <form method="POST" action="{{ route('parametres.localites.destroy', $localite) }}" onsubmit="return confirm('Supprimer la localité {{ addslashes($localite->libelle) }} ?')">
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
    @if($localites->hasPages())
    <div class="px-5 py-3.5 border-t border-gray-100" style="background:#f8f9fb;">{{ $localites->links() }}</div>
    @endif
    @endif
</div>

@endsection
