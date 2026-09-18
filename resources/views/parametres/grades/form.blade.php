<div class="flex items-center justify-between gap-3 mb-6">
    <div>
        <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
            <a href="{{ route('parametres.grades.index') }}" class="hover:text-orange-500">Grades</a>
            <i class="ti ti-chevron-right"></i><span>{{ $titre }}</span>
        </div>
        <h1 class="text-2xl font-bold text-slate-800">{{ $titre }}</h1>
    </div>
    <a href="{{ route('parametres.grades.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200 transition"><i class="ti ti-arrow-left text-sm"></i> Retour</a>
</div>

<form method="POST" action="{{ $action }}" class="max-w-2xl">
@csrf
@if($grade) @method('PUT') @endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Libellé <span class="text-red-500">*</span></label>
            <input type="text" name="libelle" value="{{ old('libelle', $grade->libelle ?? '') }}" placeholder="Ex: Commissaire de police"
                   class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none focus:ring-2 {{ $errors->has('libelle') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
            @error('libelle')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Taux de bail (F CFA) <span class="text-red-500">*</span></label>
            <input type="number" name="taux_bail" value="{{ old('taux_bail', $grade->taux_bail ?? '') }}" min="0" step="1"
                   class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none focus:ring-2 {{ $errors->has('taux_bail') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
            @error('taux_bail')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            <p class="text-xs text-amber-600 mt-1"><i class="ti ti-info-circle"></i> Modifier ce taux n'affecte pas les contrats déjà créés (valeur figée à la création).</p>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Ordre d'affichage</label>
            <input type="number" name="ordre" value="{{ old('ordre', $grade->ordre ?? 0) }}" min="0"
                   class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2">
        </div>
        <div class="md:col-span-2">
            <label class="inline-flex items-center gap-3 cursor-pointer">
                <input type="hidden" name="actif" value="0">
                <input type="checkbox" name="actif" value="1" {{ old('actif', $grade->actif ?? true) ? 'checked' : '' }} class="w-4 h-4 rounded accent-orange-500">
                <span class="text-sm font-medium text-slate-700">Grade actif</span>
            </label>
        </div>
    </div>
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
        <a href="{{ route('parametres.grades.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-white border border-gray-200 hover:bg-gray-100 transition">Annuler</a>
        <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:opacity-90" style="background:#F77F00;"><i class="ti ti-check mr-1.5"></i> Enregistrer</button>
    </div>
</div>
</form>
