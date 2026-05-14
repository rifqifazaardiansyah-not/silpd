@extends('layouts.app')

@section('title', 'Login - ' . config('silpd.nama_aplikasi'))

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="row w-100">
        <div class="col-md-6 col-lg-4 mx-auto">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold">{{ config('silpd.nama_aplikasi') }}</h3>
                        <p class="text-muted small">{{ config('silpd.nama_lengkap') }}</p>
                    </div>

                    @if ($errors->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ $errors->first('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.post') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" 
                                   placeholder="Masukkan email" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" 
                                   placeholder="Masukkan password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg fw-semibold">Login</button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <p class="text-center text-muted small mb-0">
                        Akun dibuat oleh Admin Desa. Hubungi admin jika Anda belum memiliki akun.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
