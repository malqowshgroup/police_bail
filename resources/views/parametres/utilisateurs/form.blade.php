@php
    $isEdit = (bool) $user;
    $selectedRoles = collect(old('roles', $isEdit ? $user->roles->pluck('name')->all() : []));
@endphp

<div class="flex items-center justify-between gap-3 mb-6">
    <div>
        <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
            <a href="{{ route('parametres.utilisateurs.index') }}" class="hover:text-orange-500">Utilisateurs</a>
            <i class="ti ti-chevron-right"></i><span>{{ $titre }}</span>
        </div>
        <h1 class="text-2xl font-bold text-slate-800">{{ $titre }}</h1>
    </div>
    <a href="{{ route('parametres.utilisateurs.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200 transition"><i class="ti ti-arrow-left text-sm"></i> Retour</a>
</div>

@include('parametres.partials.flash')

<form method="POST" action="{{ $action }}" class="max-w-2xl">
@csrf
@if($isEdit) @method('PUT') @endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100" style="border-left: 4px solid #F77F00;">
        <h2 class="text-sm font-semibold text-slate-800">Identité & accès</h2>
    </div>

    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
        {{-- Nom --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nom complet <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}"
                   class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none focus:ring-2 {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
            @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        {{-- Email --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Adresse e-mail <span class="text-red-500">*</span></label>
            <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}"
                   class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none focus:ring-2 {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
            @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        {{-- Password --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Mot de passe @if(!$isEdit)<span class="text-red-500">*</span>@endif</label>
            <input type="password" name="password" autocomplete="new-password"
                   class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none focus:ring-2 {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
            @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            @if($isEdit)<p class="text-xs text-slate-400 mt-1">Laisser vide pour conserver le mot de passe actuel.</p>@endif
        </div>
        {{-- Password confirmation --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Confirmer le mot de passe</label>
            <input type="password" name="password_confirmation" autocomplete="new-password"
                   class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2">
        </div>
    </div>

    <div class="px-6 pb-6">
        <label class="block text-xs font-semibold text-slate-600 mb-2">Rôles <span class="text-red-500">*</span></label>
        @error('roles')<p class="mb-2 text-xs text-red-600">{{ $message }}</p>@enderror
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            @foreach($roles as $role)
            @php $meta = $rolesMeta[$role->name] ?? [$role->name, 'bg-slate-100 text-slate-600']; @endphp
            <label class="flex items-center gap-3 px-3 py-2.5 rounded-xl border cursor-pointer transition {{ $selectedRoles->contains($role->name) ? 'border-orange-300 bg-orange-50' : 'border-gray-200 hover:bg-slate-50' }}">
                <input type="checkbox" name="roles[]" value="{{ $role->name }}" {{ $selectedRoles->contains($role->name) ? 'checked' : '' }} class="w-4 h-4 rounded accent-orange-500">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $meta[1] }}">{{ $meta[0] }}</span>
            </label>
            @endforeach
        </div>
    </div>

    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
        <a href="{{ route('parametres.utilisateurs.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-white border border-gray-200 hover:bg-gray-100 transition">Annuler</a>
        <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:opacity-90" style="background:#F77F00;"><i class="ti ti-check mr-1.5"></i> Enregistrer</button>
    </div>
</div>
</form>
