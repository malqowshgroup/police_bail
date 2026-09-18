@php
    $badges = ['bg-slate-100 text-slate-600','bg-green-100 text-green-700','bg-amber-100 text-amber-700','bg-blue-100 text-blue-700','bg-indigo-100 text-indigo-700','bg-red-100 text-red-700','bg-orange-100 text-orange-700','bg-cyan-100 text-cyan-700'];
    $dots = ['bg-slate-400','bg-green-500','bg-amber-500','bg-blue-500','bg-indigo-500','bg-red-500','bg-orange-500','bg-cyan-500'];
    $isEdit = (bool) $nomenclature;
@endphp

<div class="flex items-center justify-between gap-3 mb-6">
    <div>
        <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
            <a href="{{ route('parametres.nomenclatures.index', ['categorie' => $categorie]) }}" class="hover:text-orange-500">Statuts &amp; types</a>
            <i class="ti ti-chevron-right"></i><span>{{ $titre }}</span>
        </div>
        <h1 class="text-2xl font-bold text-slate-800">{{ $titre }}</h1>
    </div>
    <a href="{{ route('parametres.nomenclatures.index', ['categorie' => $categorie]) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200 transition"><i class="ti ti-arrow-left text-sm"></i> Retour</a>
</div>

<form method="POST" action="{{ $action }}" class="max-w-2xl"
      x-data="{ libelle: '{{ addslashes(old('libelle', $nomenclature->libelle ?? '')) }}', badge: '{{ old('couleur_badge', $nomenclature->couleur_badge ?? 'bg-slate-100 text-slate-600') }}', dot: '{{ old('couleur_dot', $nomenclature->couleur_dot ?? '') }}' }">
@csrf
@if($isEdit) @method('PUT') @endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 space-y-5">

        {{-- Aperçu --}}
        <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-4">
            <span class="text-xs text-slate-400 font-medium uppercase tracking-wider">Aperçu</span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold" :class="badge || 'bg-slate-100 text-slate-600'">
                <span class="h-1.5 w-1.5 rounded-full inline-block" :class="dot" x-show="dot"></span>
                <span x-text="libelle || 'Libellé…'"></span>
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            {{-- Catégorie --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Catégorie</label>
                @if($isEdit)
                <div class="w-full px-3 py-2.5 text-sm border border-gray-100 rounded-xl bg-slate-50 text-slate-700">{{ $categories[$nomenclature->categorie] ?? $nomenclature->categorie }}</div>
                @else
                <select name="categorie" class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none bg-white {{ $errors->has('categorie') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                    @foreach($categories as $key => $lib)
                    <option value="{{ $key }}" {{ old('categorie', $categorie) === $key ? 'selected' : '' }}>{{ $lib }}</option>
                    @endforeach
                </select>
                @error('categorie')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                @endif
            </div>

            {{-- Code --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Code @if(!$isEdit)<span class="text-red-500">*</span>@endif</label>
                @if($isEdit)
                <div class="w-full px-3 py-2.5 text-sm border border-gray-100 rounded-xl bg-slate-50 text-slate-500 font-mono">{{ $nomenclature->code }}</div>
                <p class="text-xs text-slate-400 mt-1">Le code n'est pas modifiable.</p>
                @else
                <input type="text" name="code" value="{{ old('code') }}" placeholder="ex: en_revision"
                       class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none focus:ring-2 font-mono {{ $errors->has('code') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                @error('code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                @endif
            </div>

            {{-- Libellé --}}
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Libellé <span class="text-red-500">*</span></label>
                <input type="text" name="libelle" x-model="libelle" value="{{ old('libelle', $nomenclature->libelle ?? '') }}"
                       class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none focus:ring-2 {{ $errors->has('libelle') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                @error('libelle')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Couleur badge --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Couleur du badge</label>
                <select name="couleur_badge" x-model="badge" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none">
                    @foreach($badges as $b)<option value="{{ $b }}">{{ $b }}</option>@endforeach
                </select>
            </div>

            {{-- Couleur pastille --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Couleur de pastille</label>
                <select name="couleur_dot" x-model="dot" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none">
                    <option value="">— Aucune —</option>
                    @foreach($dots as $d)<option value="{{ $d }}">{{ $d }}</option>@endforeach
                </select>
            </div>

            {{-- Ordre --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Ordre d'affichage</label>
                <input type="number" name="ordre" value="{{ old('ordre', $nomenclature->ordre ?? 0) }}" min="0"
                       class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2">
            </div>

            {{-- Actif --}}
            <div class="flex items-end pb-2.5">
                <label class="inline-flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="actif" value="0">
                    <input type="checkbox" name="actif" value="1" {{ old('actif', $nomenclature->actif ?? true) ? 'checked' : '' }} class="w-4 h-4 rounded accent-orange-500">
                    <span class="text-sm font-medium text-slate-700">Active</span>
                </label>
            </div>
        </div>
    </div>
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
        <a href="{{ route('parametres.nomenclatures.index', ['categorie' => $categorie]) }}" class="px-5 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-white border border-gray-200 hover:bg-gray-100 transition">Annuler</a>
        <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:opacity-90" style="background:#F77F00;"><i class="ti ti-check mr-1.5"></i> Enregistrer</button>
    </div>
</div>
</form>
