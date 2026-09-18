@extends('layouts.app')
@section('title', $document->nom_original)

@section('content')

@if(session('success'))
<div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm">
    <i class="ti ti-circle-check text-lg text-green-500"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm">
    <i class="ti ti-alert-circle text-lg text-red-500"></i> {{ session('error') }}
</div>
@endif

<div x-data="{ rejeterModal: false }">

{{-- Header --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-4">
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
        <div class="h-14 w-14 rounded-2xl flex items-center justify-center flex-shrink-0 bg-slate-100">
            <i class="ti {{ $document->icone }} text-2xl text-slate-500"></i>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-xl font-bold text-slate-800 truncate max-w-md">{{ $document->nom_original }}</h1>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $document->statutEnum->badge() }}">
                    <span class="h-1.5 w-1.5 rounded-full {{ $document->statutEnum->dot() }} inline-block"></span>
                    {{ $document->statutEnum->label() }}
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-1">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $document->typeEnum->badge() }}">{{ $document->typeEnum->label() }}</span>
                &bull; {{ $document->format }} &bull; {{ $document->taille_humaine }}
            </p>
        </div>
        @php
        $previewData = json_encode([
            'url'         => route('documents.preview', $document),
            'nom'         => $document->nom_original,
            'format'      => strtolower($document->format ?? ''),
            'statutLabel' => $document->statutEnum->label(),
            'statutBadge' => $document->statutEnum->badge(),
            'downloadUrl' => route('documents.download', $document),
            'ficheUrl'    => null,
        ]);
        @endphp
        <div class="flex flex-wrap gap-2 flex-shrink-0">
            @if($document->fichierDisponible())
            <button type="button"
                    data-preview="{{ $previewData }}"
                    onclick="window.dispatchEvent(new CustomEvent('open-preview',{detail:JSON.parse(this.dataset.preview),bubbles:true}))"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90"
                    style="background:#6366f1;">
                <i class="ti ti-maximize text-sm"></i> Prévisualiser
            </button>
            @endif
            <a href="{{ route('documents.download', $document) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90" style="background:#009A44;">
                <i class="ti ti-download text-sm"></i> Télécharger
            </a>
            @if($document->peutEtreValide())
            <form method="POST" action="{{ route('documents.valider', $document) }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90" style="background:#3b82f6;">
                    <i class="ti ti-check text-sm"></i> Valider
                </button>
            </form>
            @endif
            @if($document->peutEtreRejete())
            <button type="button" @click="rejeterModal = true" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90" style="background:#dc2626;">
                <i class="ti ti-x text-sm"></i> Rejeter
            </button>
            @endif
            <a href="{{ route('documents.edit', $document) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90" style="background:#F77F00;">
                <i class="ti ti-pencil text-sm"></i><span class="hidden sm:inline">Modifier</span>
            </a>
            <a href="{{ route('documents.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200 transition">
                <i class="ti ti-arrow-left text-sm"></i><span class="hidden sm:inline">Retour</span>
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Détails --}}
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-2 border-b border-gray-100">
                <i class="ti ti-info-circle mr-1.5 text-slate-400"></i> Informations
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Type</p>
                    <p class="text-slate-700">{{ $document->typeEnum->label() }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Format / Taille</p>
                    <p class="text-slate-700">{{ $document->format }} · {{ $document->taille_humaine }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Téléversé par</p>
                    <p class="text-slate-700">{{ $document->uploadedPar?->name ?? '—' }}</p>
                    <p class="text-xs text-slate-400">{{ $document->created_at?->format('d/m/Y H:i') }}</p>
                </div>
                @if($document->date_validation)
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Validé par</p>
                    <p class="text-slate-700">{{ $document->validePar?->name ?? '—' }}</p>
                    <p class="text-xs text-slate-400">{{ $document->date_validation->format('d/m/Y H:i') }}</p>
                </div>
                @endif
            </div>
            @if(! $document->fichierDisponible())
            <div class="mt-4 flex items-center gap-2 text-amber-700 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2 text-xs">
                <i class="ti ti-alert-triangle"></i> Aucun fichier physique associé (métadonnées seules).
            </div>
            @endif
            @if($document->observations)
            <div class="mt-4 pt-3 border-t border-gray-100">
                <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Observations</p>
                <p class="text-sm text-slate-700 whitespace-pre-line">{{ $document->observations }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Rattachements --}}
    <div class="space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-2 border-b border-gray-100">
                <i class="ti ti-link mr-1.5 text-slate-400"></i> Rattachements ({{ $document->liens->count() }})
            </h2>
            @if($document->liens->isEmpty())
            <p class="text-sm text-slate-400">Ce document n'est rattaché à aucun élément.</p>
            @else
            <div class="space-y-2">
                @foreach($document->liens as $lien)
                <div class="flex items-center gap-3 rounded-xl bg-slate-50 px-3 py-2.5">
                    <div class="h-8 w-8 rounded-lg flex items-center justify-center flex-shrink-0 bg-white border border-gray-100">
                        <i class="ti ti-paperclip text-sm text-slate-400"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ $lien->entite_type_label }}</p>
                        @if($lien->entite_url)
                        <a href="{{ $lien->entite_url }}" class="text-sm font-medium text-blue-600 hover:underline truncate block">{{ $lien->entite_label }}</a>
                        @else
                        <span class="text-sm text-slate-700 truncate block">{{ $lien->entite_label }}</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
            <a href="{{ route('documents.edit', $document) }}" class="mt-4 inline-flex items-center gap-1.5 text-xs font-medium transition" style="color:#F77F00;">
                Gérer le document <i class="ti ti-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
</div>

{{-- Modal rejeter --}}
<div x-show="rejeterModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,.4);" @click.self="rejeterModal = false">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6" @click.stop>
        <h3 class="text-lg font-bold text-slate-800 mb-1">Rejeter le document</h3>
        <p class="text-sm text-slate-500 mb-4">Indiquez le motif de rejet.</p>
        <form method="POST" action="{{ route('documents.rejeter', $document) }}">
            @csrf
            <textarea name="motif_rejet" rows="3" required placeholder="Motif de rejet…"
                      class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 mb-4"></textarea>
            <div class="flex justify-end gap-2">
                <button type="button" @click="rejeterModal = false" class="px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200">Annuler</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white" style="background:#dc2626;">Rejeter</button>
            </div>
        </form>
    </div>
</div>

</div>

@endsection
