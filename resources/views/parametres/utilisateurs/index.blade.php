@extends('layouts.app')
@section('title', 'Utilisateurs')

@section('content')

@include('parametres.partials.flash')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
            <span>Administration</span><i class="ti ti-chevron-right"></i><span>Utilisateurs</span>
        </div>
        <h1 class="text-2xl font-bold text-slate-800">Utilisateurs</h1>
        <p class="text-sm text-slate-500 mt-0.5">{{ $total }} comptes · {{ $admins }} administrateur(s)</p>
    </div>
    <a href="{{ route('parametres.utilisateurs.create') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white shadow-sm transition hover:opacity-90 flex-shrink-0" style="background:#F77F00;">
        <i class="ti ti-user-plus text-base"></i> Ajouter un utilisateur
    </a>
</div>

{{-- Filtres --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" action="{{ route('parametres.utilisateurs.index') }}">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 min-w-0">
                <div class="relative">
                    <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom ou e-mail…"
                           class="w-full pl-9 pr-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2">
                </div>
            </div>
            <select name="role" class="w-full sm:w-56 px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none">
                <option value="">Tous les rôles</option>
                @foreach($roles as $role)
                <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>{{ $rolesMeta[$role->name][0] ?? $role->name }}</option>
                @endforeach
            </select>
            <div class="flex gap-2 flex-shrink-0">
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:opacity-90" style="background:#1a2440;"><i class="ti ti-search text-sm"></i></button>
                @if(request()->hasAny(['search','role']))
                <a href="{{ route('parametres.utilisateurs.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200 transition"><i class="ti ti-x text-sm"></i></a>
                @endif
            </div>
        </div>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    @if($users->isEmpty())
    @include('parametres.partials.vide', ['label' => 'Aucun utilisateur trouvé'])
    @else
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-gray-100" style="background:#f8f9fb;">
                    <th class="px-5 py-3">Utilisateur</th>
                    <th class="px-5 py-3">Rôles</th>
                    <th class="px-5 py-3 hidden md:table-cell">Créé le</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($users as $user)
                <tr class="hover:bg-slate-50/70 group">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0" style="background:#1a2440;">
                                {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="font-semibold text-slate-800 flex items-center gap-2">
                                    {{ $user->name }}
                                    @if($user->id === auth()->id())<span class="text-[10px] font-medium px-1.5 py-0.5 rounded bg-orange-100 text-orange-600">vous</span>@endif
                                </div>
                                <div class="text-xs text-slate-500">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex flex-wrap gap-1">
                            @forelse($user->roles as $role)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $rolesMeta[$role->name][1] ?? 'bg-slate-100 text-slate-600' }}">{{ $rolesMeta[$role->name][0] ?? $role->name }}</span>
                            @empty
                            <span class="text-xs text-slate-300">Aucun rôle</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="px-5 py-3.5 hidden md:table-cell text-xs text-slate-500">{{ $user->created_at?->format('d/m/Y') }}</td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-end gap-1 opacity-60 group-hover:opacity-100 transition">
                            <a href="{{ route('parametres.utilisateurs.edit', $user) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50" title="Modifier"><i class="ti ti-pencil text-base"></i></a>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('parametres.utilisateurs.destroy', $user) }}" onsubmit="return confirm('Supprimer l\'utilisateur {{ addslashes($user->name) }} ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-red-600 hover:bg-red-50" title="Supprimer"><i class="ti ti-trash text-base"></i></button>
                            </form>
                            @else
                            <span class="p-1.5 text-slate-200" title="Compte courant"><i class="ti ti-lock text-base"></i></span>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="px-5 py-3.5 border-t border-gray-100" style="background:#f8f9fb;">{{ $users->links() }}</div>
    @endif
    @endif
</div>

@endsection
