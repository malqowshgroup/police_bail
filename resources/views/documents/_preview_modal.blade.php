{{--
    Modale de prévisualisation universelle.
    Écoute l'événement window "open-preview" émis par $dispatch('open-preview', { url, nom, format, statutLabel, statutBadge, downloadUrl, ficheUrl }).
    Inclure une seule fois dans layouts/app.blade.php.
--}}
<div x-data="{
        open: false,
        url: '',
        nom: '',
        format: '',
        sLabel: '',
        sBadge: '',
        dlUrl: '',
        ficheUrl: '',
        isImage() {
            return ['jpg','jpeg','png','gif','webp','tif','tiff','bmp','svg'].includes(this.format);
        },
        isPdf() { return this.format === 'pdf'; },
        openPreview(d) {
            this.url      = d.url         || '';
            this.nom      = d.nom         || '';
            this.format   = (d.format     || '').toLowerCase();
            this.sLabel   = d.statutLabel || '';
            this.sBadge   = d.statutBadge || '';
            this.dlUrl    = d.downloadUrl || '';
            this.ficheUrl = d.ficheUrl    || '';
            this.open     = true;
        },
        closePreview() { this.open = false; }
     }"
     x-on:open-preview.window="openPreview($event.detail)"
     @keydown.escape.window="if(open) closePreview()"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-[200] flex items-center justify-center p-3 sm:p-6"
     style="background:rgba(15,23,42,0.82);"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    {{-- Fond cliquable pour fermer --}}
    <div class="absolute inset-0" @click="closePreview()"></div>

    {{-- Panneau --}}
    <div class="relative z-10 bg-white rounded-2xl shadow-2xl flex flex-col w-full max-w-5xl"
         style="max-height:93vh;"
         @click.stop
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">

        {{-- En-tête --}}
        <div class="flex items-center gap-3 px-5 py-3.5 border-b border-gray-100 flex-shrink-0"
             style="background:#f8f9fb; border-radius:1rem 1rem 0 0;">

            <div class="h-9 w-9 rounded-xl bg-slate-200 flex items-center justify-center flex-shrink-0">
                <i class="ti ti-paperclip text-slate-500"></i>
            </div>

            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-slate-800 truncate" x-text="nom"></p>
                <p class="text-[10px] font-mono text-slate-400 uppercase tracking-widest" x-text="format"></p>
            </div>

            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold flex-shrink-0"
                  :class="sBadge"
                  x-text="sLabel"
                  x-show="sLabel"></span>

            <div class="flex items-center gap-1.5 flex-shrink-0 ml-1">
                <a :href="dlUrl"
                   x-show="dlUrl"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-white transition hover:opacity-90"
                   style="background:#1a2440;">
                    <i class="ti ti-download text-xs"></i>
                    <span class="hidden sm:inline">Télécharger</span>
                </a>
                <a :href="ficheUrl"
                   x-show="ficheUrl"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-medium text-slate-600 bg-gray-100 hover:bg-gray-200 transition">
                    <i class="ti ti-external-link text-xs"></i>
                    <span class="hidden sm:inline">Fiche</span>
                </a>
                <button @click="closePreview()"
                        title="Fermer (Échap)"
                        class="p-1.5 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-gray-100 transition">
                    <i class="ti ti-x text-xl"></i>
                </button>
            </div>
        </div>

        {{-- Zone de prévisualisation --}}
        <div class="flex-1 overflow-hidden bg-slate-100 flex flex-col" style="min-height:320px;">

            {{-- Image --}}
            <template x-if="open && isImage()">
                <div class="w-full h-full flex items-center justify-center p-6" style="min-height:320px;">
                    <img :src="url"
                         :alt="nom"
                         class="max-w-full object-contain shadow-lg rounded-xl"
                         style="max-height:75vh;">
                </div>
            </template>

            {{-- PDF --}}
            <template x-if="open && isPdf()">
                <iframe :src="url"
                        :title="nom"
                        class="w-full border-0 block"
                        style="height:75vh; min-height:320px;"></iframe>
            </template>

            {{-- Format non prévisualisable --}}
            <template x-if="open && !isImage() && !isPdf()">
                <div class="flex flex-col items-center justify-center py-16 px-8 text-center" style="min-height:320px;">
                    <div class="h-24 w-24 rounded-3xl bg-white flex items-center justify-center mb-5 shadow-sm">
                        <i class="ti ti-file text-5xl text-slate-300"></i>
                    </div>
                    <p class="text-lg font-semibold text-slate-700 mb-1">Prévisualisation non disponible</p>
                    <p class="text-sm text-slate-400 mb-2">
                        Le format
                        <span class="font-mono font-bold px-1.5 py-0.5 rounded-md bg-slate-200 text-slate-600"
                              x-text="format.toUpperCase()"></span>
                        ne peut pas être affiché directement dans le navigateur.
                    </p>
                    <p class="text-xs text-slate-400 mb-6">Téléchargez le fichier pour le consulter avec l'application appropriée.</p>
                    <a :href="dlUrl"
                       x-show="dlUrl"
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-white transition hover:opacity-90"
                       style="background:#F77F00;">
                        <i class="ti ti-download text-sm"></i> Télécharger le fichier
                    </a>
                </div>
            </template>
        </div>
    </div>
</div>
