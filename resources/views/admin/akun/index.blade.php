@extends('layouts.admin')

@section('title', 'Manajemen Akun')

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-600">
    <span>Operasional</span>
    <span>/</span>
    <span>Manajemen Akun</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Manajemen Akun</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola akun login semua pengguna</p>
    </div>
    <a href="{{ route('admin.akun.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Buat Akun
    </a>
</div>

<!-- Flash Messages -->
@if(session('success'))
<div class="mb-6 flex items-start gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-800 text-sm">
    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <span>{{ session('success') }}</span>
</div>
@endif

<!-- Tabs -->
<div class="mb-6 border-b border-gray-200">
    <div class="flex gap-8">
        <a href="{{ route('admin.akun.index') }}"
           class="px-1 py-3 text-sm font-medium border-b-2 {{ !request('role') ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-600 hover:text-gray-900' }}">
            Semua
        </a>
        <a href="{{ route('admin.akun.index', ['role' => 'petani']) }}"
           class="px-1 py-3 text-sm font-medium border-b-2 {{ request('role') === 'petani' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-600 hover:text-gray-900' }}">
            Petani ({{ $jumlahPerRole['petani'] ?? 0 }})
        </a>
        <a href="{{ route('admin.akun.index', ['role' => 'pengelola']) }}"
           class="px-1 py-3 text-sm font-medium border-b-2 {{ request('role') === 'pengelola' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-600 hover:text-gray-900' }}">
            Pengelola ({{ $jumlahPerRole['pengelola'] ?? 0 }})
        </a>
        <a href="{{ route('admin.akun.index', ['role' => 'admin']) }}"
           class="px-1 py-3 text-sm font-medium border-b-2 {{ request('role') === 'admin' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-600 hover:text-gray-900' }}">
            Admin ({{ $jumlahPerRole['admin'] ?? 0 }})
        </a>
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50">
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">No</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Username</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Role</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Pemilik Akun</th>
                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($akunList as $index => $akun)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $akunList->firstItem() + $index }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 font-mono text-[13px]">{{ $akun->username }}</td>
                    <td class="px-4 py-3 text-sm">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium
                            {{ $akun->role === 'admin' ? 'bg-indigo-50 text-indigo-700' : ($akun->role === 'pengelola' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700') }}">
                            {{ ucfirst($akun->role) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $akun->nama_pemilik ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.akun.show', $akun->id_login) }}"
                               class="p-1.5 text-gray-600 hover:text-indigo-600 transition-colors"
                               title="Lihat detail">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </a>
                            <a href="{{ route('admin.akun.edit', $akun->id_login) }}"
                               class="p-1.5 text-gray-600 hover:text-amber-600 transition-colors"
                               title="Edit">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </a>
                            <form action="{{ route('admin.akun.destroy', $akun->id_login) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Yakin ingin menghapus akun ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-gray-600 hover:text-red-600 transition-colors" title="Hapus">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 2.991a2.25 2.25 0 00-2.163-1.491h-6.798c-.97 0-1.8.54-2.163 1.491L2.012 5.5m15.848 1.676c-.356.067-.71.142-1.056.217m-1.056-.217l-.5 6.5a2.25 2.25 0 01-2.25 2.25H5.25a2.25 2.25 0 01-2.25-2.25l-.5-6.5m15.848 0A2.25 2.25 0 0021 7.773v10.706a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18.48V7.771a2.25 2.25 0 012.25-2.25m15.848 0c-.356.067-.71.142-1.056.217M3.12 5.973c.346.075.7.13 1.056.217" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">Tidak ada akun</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($akunList->count() > 0)
    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
        <p class="text-xs text-gray-500">
            Menampilkan {{ $akunList->firstItem() }}–{{ $akunList->lastItem() }} dari {{ $akunList->total() }} data
        </p>
        {{ $akunList->withQueryString()->links('vendor.pagination.tailwind') }}
    </div>
    @endif
</div>

@endsection
