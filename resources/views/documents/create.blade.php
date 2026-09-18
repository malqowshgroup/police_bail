@extends('layouts.app')
@section('title', 'Téléverser un document')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Téléverser un document</h1>
        <p class="text-sm text-slate-500 mt-0.5">PDF, JPG, PNG ou TIFF — 10 Mo maximum</p>
    </div>
    <a href="{{ route('documents.index') }}"
       class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200 transition flex-shrink-0">
        <i class="ti ti-arrow-left text-sm"></i> Retour à la liste
    </a>
</div>

<form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" x-data="docForm()">
@csrf

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden max-w-3xl">
    <div class="px-6 py-4 border-b border-gray-100" style="border-left: 4px solid #F77F00;">
        <h2 class="text-sm font-semibold text-slate-800">Fichier & métadonnées</h2>
        <p class="text-xs text-slate-500 mt-0.5">Les champs marqués <span class="text-red-500">*</span> sont obligatoires.</p>
    </div>

    <div class="p-6 space-y-5">

        {{-- Zone fichier --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Fichier <span class="text-red-500">*</span></label>
            <label class="flex flex-col items-center justify-center gap-2 px-6 py-8 border-2 border-dashed rounded-xl cursor-pointer transition hover:bg-slate-50 {{ $errors->has('fichier') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                <i class="ti ti-cloud-upload text-3xl text-slate-400"></i>
                <span class="text-sm text-slate-500" x-show="!fileName">Cliquez pour choisir un fichier</span>
                <span class="text-sm font-semibold text-slate-700" x-show="fileName" x-text="fileName" x-cloak></span>
                <span class="text-xs text-slate-400">PDF, JPG, PNG, TIFF · 10 Mo max</span>
                <input type="file" name="fichier" class="hidden" accept=".pdf,.jpg,.jpeg,.png,.tiff,.tif" @change="fileName = $event.target.files[0]?.name || ''">
            </label>
            @error('fichier')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Type --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Type de document <span class="text-red-500">*</span></label>
            <select name="type_document" class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none bg-white {{ $errors->has('type_document') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                <option value="">— Choisir un type —</option>
                @foreach($types as $t)
                <option value="{{ $t['value'] }}" {{ old('type_document') === $t['value'] ? 'selected' : '' }}>{{ $t['label'] }}</option>
                @endforeach
            </select>
            @error('type_document')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Rattachement (optionnel) --}}
        <div class="rounded-xl bg-slate-50 p-4">
            <p class="text-xs font-semibold text-slate-600 mb-3"><i class="ti ti-link mr-1 text-slate-400"></i> Rattacher à un élément (optionnel)</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Type d'élément</label>
                    <select name="entite_type" x-model="entiteType" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none">
                        <option value="">— Aucun —</option>
                        @foreach($entites as $key => $lib)
                        <option value="{{ $key }}">{{ $lib }}</option>
                        @endforeach
                    </select>
                </div>
                <div x-show="entiteType" x-cloak>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Élément</label>
                    <select name="entite_id" class="w-full px-3 py-2.5 text-sm border rounded-xl bg-white focus:outline-none {{ $errors->has('entite_id') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                        <option value="">— Sélectionner —</option>
                        <template x-for="item in options" :key="item.id">
                            <option :value="item.id" x-text="item.label" :selected="item.id == preId"></option>
                        </template>
                    </select>
                    @error('entite_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Observations --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Observations</label>
            <textarea name="observations" rows="2" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2">{{ old('observations') }}</textarea>
        </div>

    </div>

    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
        <a href="{{ route('documents.index') }}"
           class="w-full sm:w-auto text-center px-5 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-white border border-gray-200 hover:bg-gray-100 transition">
            Annuler
        </a>
        <button type="submit"
                class="w-full sm:w-auto px-6 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:opacity-90"
                style="background:#F77F00;">
            <i class="ti ti-upload mr-1.5"></i> Téléverser
        </button>
    </div>
</div>

</form>

@push('scripts')
<script>
function docForm() {
    return {
        fileName: '',
        entiteType: '{{ old('entite_type', $preEntiteType) }}',
        preId: {{ $preEntiteId ?? 'null' }},
        map: @json($entitesParType),
        get options() {
            return this.entiteType ? (this.map[this.entiteType] || []) : [];
        },
    };
}
</script>
@endpush

@endsection
