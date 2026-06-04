@extends('layouts.admin')

@section('title', $akun->username)

@section('breadcrumb')
<nav class="flex items-center gap-2 text-sm text-gray-600">
    <a href="{{ route('admin.akun.index') }}" class="hover:text-gray-900">Operasional</a>
    <span>/</span>
    <a href="{{ route('admin.akun.index') }}" class="hover:text-gray-900">Manajemen Akun</a>
    <span>/</span>
    <span>{{ $akun->username }}</span>
</nav>
@endsection

@section('content')
<!-- Page Header -->
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">{{ $akun->username }}</h1>
        <p class="text-sm text-gray-500 mt-1">Detail akun login</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.akun.edit', $akun->id_login) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition-colors">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
            </svg>
            Edit Username
        </a>
        <a href="{{ route('admin.akun.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Kembali
        </a>
    </div>
</div>

<!-- Detail Card -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Informasi Akun</h3>
    </div>
    <div class="p-6 space-y-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Username</p>
            <p class="text-sm text-gray-900 mt-1 font-mono text-[13px]">{{ $akun->username }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Role</p>
            <p class="text-sm text-gray-900 mt-1">{{ ucfirst($akun->role) }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Pemilik Akun</p>
            <p class="text-sm text-gray-900 mt-1">{{ $akun->nama_pemilik ?? '-' }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Dibuat</p>
            <p class="text-sm text-gray-900 mt-1">{{ $akun->created_at->format('d M Y H:i') }}</p>
        </div>
    </div>
</div>

<!-- Actions Card -->
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Aksi</h3>
    </div>
    <div class="p-6 space-y-3">
        <form action="{{ route('admin.akun.reset-password', $akun->id_login) }}" method="POST" class="inline-block">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition-colors"
                    onclick="return confirm('Reset password akun ini? Pengguna akan diminta membuat password baru saat login berikutnya.')">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.638 5.214m15.997 0A8.25 8.25 0 0012 3c-4.474 0-8.268 2.943-9.362 7a9.722 9.722 0 011.636 3.36m15.997 0c1.036-2.26 1.606-4.762 1.606-7.368m-15.997 0C3.678 3.75 2.5 5.765 2.5 8.25" />
                </svg>
                Reset Password
            </button>
        </form>
        <form action="{{ route('admin.akun.destroy', $akun->id_login) }}" method="POST" class="inline-block">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors"
                    onclick="return confirm('Yakin ingin menghapus akun ini?')">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 2.991a2.25 2.25 0 00-2.163-1.491h-6.798c-.97 0-1.8.54-2.163 1.491L2.012 5.5m15.848 1.676c-.356.067-.71.142-1.056.217m-1.056-.217l-.5 6.5a2.25 2.25 0 01-2.25 2.25H5.25a2.25 2.25 0 01-2.25-2.25l-.5-6.5m15.848 0A2.25 2.25 0 0021 7.773v10.706a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18.48V7.771a2.25 2.25 0 012.25-2.25m15.848 0c-.356.067-.71.142-1.056.217M3.12 5.973c.346.075.7.13 1.056.217" />
                </svg>
                Hapus Akun
            </button>
        </form>
    </div>
</div>

@endsection
