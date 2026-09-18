{{--
    Panel documents réutilisable.
    Variables attendues : $entiteType (string), $entiteId (int), $liens (Collection<DocumentLien> avec 'document' chargé)
--}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100">

    {{-- En-tête --}}
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between" style="background:#f8f9fb;">
        <h2 class="text-sm font-semibold text-slate-700 flex items-center gap-2">
            <i class="ti ti-paperclip text-slate-400"></i>
            Documents
            <span class="inline-flex items-center justify-center min-w-[22px] h-5 px-1.5 rounded-full text-xs font-bold bg-slate-200 text-slate-600">
                {{ $liens->count() }}
            </span>
        </h2>
        <a href="{{ route('documents.create', ['entite_type' => $entiteType, 'entite_id' => $entiteId]) }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-white transition hover:opacity-90"
           style="background:#F77F00;">
            <i class="ti ti-plus text-xs"></i> Ajouter un document
        </a>
    </div>

    @if($liens->isEmpty())

    {{-- État vide --}}
    <div class="flex flex-col items-center justify-center py-10 text-slate-400">
        <div class="h-14 w-14 rounded-2xl bg-slate-100 flex items-center justify-center mb-3">
            <i class="ti ti-file-off text-3xl text-slate-300"></i>
        </div>
        <p class="text-sm font-medium text-slate-500">Aucun document associé</p>
        <p class="text-xs text-slate-400 mt-0.5">Téléversez le premier document de cet enregistrement.</p>
        <a href="{{ route('documents.create', ['entite_type' => $entiteType, 'entite_id' => $entiteId]) }}"
           class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold transition hover:opacity-80"
           style="color:#F77F00;">
            <i class="ti ti-upload text-xs"></i> Téléverser un document
        </a>
    </div>

    @else

    {{-- Liste des documents --}}
    <div class="divide-y divide-gray-50">
        @foreach($liens as $lien)
        @if($lien->document)
        @php
            $doc = $lien->document;
            $previewData = json_encode([
                'url'         => route('documents.preview', $doc),
                'nom'         => $doc->nom_original,
                'format'      => strtolower($doc->format ?? ''),
                'statutLabel' => $doc->statutEnum->label(),
                'statutBadge' => $doc->statutEnum->badge(),
                'downloadUrl' => route('documents.download', $doc),
                'ficheUrl'    => route('documents.show', $doc),
            ]);
        @endphp
        <div class="px-5 py-3 flex items-center gap-3 hover:bg-slate-50/60 transition-colors group">

            {{-- Icône fichier --}}
            <div class="h-10 w-10 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0">
                <i class="ti {{ $doc->icone }} text-xl text-slate-500"></i>
            </div>

            {{-- Informations --}}
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-slate-800 truncate">{{ $doc->nom_original }}</p>
                <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-semibold {{ $doc->typeEnum->badge() }}">
                        {{ $doc->typeEnum->label() }}
                    </span>
                    <span class="text-[10px] text-slate-400">{{ $doc->taille_humaine }}</span>
                    <span class="text-[10px] text-slate-400">{{ $doc->created_at?->format('d/m/Y') }}</span>
                </div>
            </div>

            {{-- Badge statut --}}
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold {{ $doc->statutEnum->badge() }} flex-shrink-0">
                <span class="h-1.5 w-1.5 rounded-full {{ $doc->statutEnum->dot() }} inline-block"></span>
                {{ $doc->statutEnum->label() }}
            </span>

            {{-- Actions --}}
            <div class="flex items-center gap-0.5 flex-shrink-0 opacity-60 group-hover:opacity-100 transition-opacity">
                {{-- Voir la fiche --}}
                <a href="{{ route('documents.show', $doc) }}"
                   title="Voir la fiche"
                   class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors">
                    <i class="ti ti-eye text-sm"></i>
                </a>
                {{-- Prévisualiser --}}
                <button type="button"
                        data-preview="{{ $previewData }}"
                        onclick="window.dispatchEvent(new CustomEvent('open-preview',{detail:JSON.parse(this.dataset.preview),bubbles:true}))"
                        title="Prévisualiser"
                        class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors">
                    <i class="ti ti-maximize text-sm"></i>
                </button>
                {{-- Télécharger --}}
                <a href="{{ route('documents.download', $doc) }}"
                   title="Télécharger"
                   class="p-1.5 rounded-lg text-slate-400 hover:text-green-600 hover:bg-green-50 transition-colors">
                    <i class="ti ti-download text-sm"></i>
                </a>
                {{-- Valider --}}
                @if($doc->peutEtreValide())
                <form method="POST" action="{{ route('documents.valider', $doc) }}" class="inline">
                    @csrf
                    <button type="submit" title="Valider le document"
                            class="p-1.5 rounded-lg text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 transition-colors">
                        <i class="ti ti-circle-check text-sm"></i>
                    </button>
                </form>
                @endif
                {{-- Supprimer --}}
                <form method="POST" action="{{ route('documents.destroy', $doc) }}" class="inline"
                      onsubmit="return confirm('Supprimer définitivement ce document ?')">
                    @csrf @method('DELETE')
                    <button type="submit" title="Supprimer"
                            class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                        <i class="ti ti-trash text-sm"></i>
                    </button>
                </form>
            </div>
        </div>
        @endif
        @endforeach
    </div>

    {{-- Pied de panel --}}
    <div class="px-5 py-2.5 border-t border-gray-100 flex items-center justify-between" style="background:#f8f9fb;">
        <span class="text-xs text-slate-400">
            {{ $liens->count() }} document{{ $liens->count() > 1 ? 's' : '' }} associé{{ $liens->count() > 1 ? 's' : '' }}
        </span>
        <a href="{{ route('documents.index', ['entite_type' => $entiteType]) }}"
           class="inline-flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-800 transition">
            <i class="ti ti-external-link text-xs"></i> Voir dans la GED
        </a>
    </div>

    @endif
</div>
