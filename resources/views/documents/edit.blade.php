@extends('layouts.app')
@section('title', 'Modifier le document')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Modifier le document</h1>
        <p class="text-sm text-slate-500 mt-0.5 truncate max-w-md">{{ $document->nom_original }}</p>
    </div>
    <a href="{{ route('documents.show', $document) }}"
       class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200 transition flex-shrink-0">
        <i class="ti ti-arrow-left text-sm"></i> Retour
    </a>
</div>

<form method="POST" action="{{ route('documents.update', $document) }}">
@csrf @method('PUT')

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden max-w-3xl">
    <div class="px-6 py-4 border-b border-gray-100" style="border-left: 4px solid #F77F00;">
        <h2 class="text-sm font-semibold text-slate-800">Métadonnées</h2>
        <p class="text-xs text-slate-500 mt-0.5">Le fichier ne peut pas être remplacé. Supprimez et re-téléversez si nécessaire.</p>
    </div>

    <div class="p-6 space-y-5">
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Fichier</label>
            <div class="flex items-center gap-3 rounded-xl bg-slate-50 px-4 py-3">
                <i class="ti {{ $document->icone }} text-2xl text-slate-400"></i>
                <div>
                    <p class="text-sm font-medium text-slate-700">{{ $document->nom_original }}</p>
                    <p class="text-xs text-slate-400">{{ $document->format }} · {{ $document->taille_humaine }}</p>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Type de document <span class="text-red-500">*</span></label>
            <select name="type_document" class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none bg-white {{ $errors->has('type_document') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                @foreach($types as $t)
                <option value="{{ $t['value'] }}" {{ old('type_document', $document->type_document) === $t['value'] ? 'selected' : '' }}>{{ $t['label'] }}</option>
                @endforeach
            </select>
            @error('type_document')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Observations</label>
            <textarea name="observations" rows="3" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2">{{ old('observations', $document->observations) }}</textarea>
        </div>
    </div>

    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
        <a href="{{ route('documents.show', $document) }}"
           class="w-full sm:w-auto text-center px-5 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-white border border-gray-200 hover:bg-gray-100 transition">
            Annuler
        </a>
        <button type="submit"
                class="w-full sm:w-auto px-6 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:opacity-90"
                style="background:#F77F00;">
            <i class="ti ti-check mr-1.5"></i> Enregistrer
        </button>
    </div>
</div>

</form>
@endsection
