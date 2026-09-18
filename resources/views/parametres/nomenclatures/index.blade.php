@extends('layouts.app')
@section('title', 'Statuts & types')

@section('content')

@include('parametres.partials.flash')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
            <a href="{{ route('parametres.index') }}" class="hover:text-orange-500">Paramètres</a>
            <i class="ti ti-chevron-right"></i><span>Statuts &amp; types</span>
        </div>
        <h1 class="text-2xl font-bold text-slate-800">Statuts &amp; types</h1>
        <p class="text-sm text-slate-500 mt-0.5">Libellés, couleurs et ordre des listes métier</p>
    </div>
    <a href="{{ route('parametres.nomenclatures.create', ['categorie' => $categorie]) }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white shadow-sm transition hover:opacity-90 flex-shrink-0" style="background:#F77F00;">
        <i class="ti ti-plus text-base"></i> Ajouter une valeur
    </a>
</div>

{{-- Onglets catégories --}}
<div class="flex flex-wrap gap-2 mb-4">
    @foreach($categories as $key => $libelle)
    <a href="{{ route('parametres.nomenclatures.index', ['categorie' => $key]) }}"
       class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $categorie === $key ? 'text-white' : 'text-slate-600 bg-white border border-gray-200 hover:bg-gray-100' }}"
       style="{{ $categorie === $key ? 'background:#1a2440;' : '' }}">
        {{ $libelle }}
    </a>
    @endforeach
</div>

<div class="mb-4 flex items-start gap-2 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-4 py-3 text-xs">
    <i class="ti ti-bulb text-base text-amber-500 mt-0.5"></i>
    <span>Modifier un libellé ou une couleur est purement cosmétique. Les valeurs <strong>« système »</strong> sont câblées au workflow : code non modifiable, suppression impossible.</span>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    @if($entrees->isEmpty())
    @include('parametres.partials.vide', ['label' => 'Aucune valeur dans cette catégorie'])
    @else
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-gray-100" style="background:#f8f9fb;">
                    <th class="px-5 py-3">Libellé</th>
                    <th class="px-5 py-3">Code</th>
                    <th class="px-5 py-3">Aperçu</th>
                    <th class="px-5 py-3 text-center hidden sm:table-cell">Ordre</th>
                    <th class="px-5 py-3 text-center">Statut</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($entrees as $n)
                <tr class="hover:bg-slate-50/70 group">
                    <td class="px-5 py-3.5 font-semibold text-slate-800">
                        {{ $n->libelle }}
                        @if($n->systeme)<span class="ml-1.5 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-500" title="Câblée au workflow"><i class="ti ti-lock text-[10px]"></i> système</span>@endif
                    </td>
                    <td class="px-5 py-3.5"><span class="font-mono text-xs text-slate-500">{{ $n->code }}</span></td>
                    <td class="px-5 py-3.5">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $n->couleur_badge ?: 'bg-slate-100 text-slate-600' }}">
                            @if($n->couleur_dot)<span class="h-1.5 w-1.5 rounded-full {{ $n->couleur_dot }} inline-block"></span>@endif
                            {{ $n->libelle }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-center hidden sm:table-cell text-slate-500">{{ $n->ordre }}</td>
                    <td class="px-5 py-3.5 text-center">@include('parametres.partials.actif', ['actif' => $n->actif])</td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-end gap-1 opacity-60 group-hover:opacity-100 transition">
                            <a href="{{ route('parametres.nomenclatures.edit', $n) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50" title="Modifier"><i class="ti ti-pencil text-base"></i></a>
                            @if(! $n->systeme)
                            <form method="POST" action="{{ route('parametres.nomenclatures.destroy', $n) }}" onsubmit="return confirm('Supprimer la valeur {{ addslashes($n->libelle) }} ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-red-600 hover:bg-red-50" title="Supprimer"><i class="ti ti-trash text-base"></i></button>
                            </form>
                            @else
                            <span class="p-1.5 text-slate-200" title="Valeur système : non supprimable"><i class="ti ti-lock text-base"></i></span>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@endsection
