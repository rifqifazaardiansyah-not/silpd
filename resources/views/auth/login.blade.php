<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SILPD</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            font-family: 'Plus Jakarta Sans', 'Poppins', system-ui, -apple-system, sans-serif;
        }

        /* Gradient background untuk bagian kiri */
        .gradient-left {
            background: linear-gradient(135deg, #1E3A8A 0%, #14B8A6 100%);
        }

        /* Tombol dengan gradient */
        .btn-gradient {
            background: linear-gradient(135deg, #1E3A8A 0%, #14B8A6 100%);
            transition: all 0.3s ease;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(30, 58, 138, 0.3);
        }

        .btn-gradient:active {
            transform: translateY(0);
        }

        /* Input styling */
        .input-field {
            border: 1.5px solid #E5E7EB;
            transition: all 0.3s ease;
            background-color: #F8FAFC;
            height: 45px;
        }

        .input-field:focus {
            border-color: #14B8A6;
            box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.1);
            outline: none;
            background-color: #fff;
        }

        .input-field::placeholder {
            color: #9CA3AF;
        }

        /* Shadow lembut */
        .soft-shadow {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07), 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        /* Responsive: Sembunyikan bagian kiri pada mobile */
        @media (max-width: 768px) {
            .left-section {
                display: none;
            }

            .login-container {
                width: 100%;
            }
        }

        /* Animasi smooth */
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-slide-in {
            animation: slideInRight 0.6s ease-out;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex p-4 gap-4">
        <!-- Bagian Kiri: Branding -->
        <div class="left-section hidden md:flex w-full md:w-1/2 gradient-left flex-col items-center justify-center px-8 py-12 rounded-3xl soft-shadow">
            <div class="text-center text-white max-w-md">
                <!-- Logo -->
                <div class="mb-8 flex justify-center">
                    <div class="w-24 h-24 bg-white rounded-3xl flex items-center justify-center soft-shadow">
                        <span class="text-5xl">🌾</span>
                    </div>
                </div>

                <!-- Judul SILPD -->
                <h1 class="text-5xl font-bold tracking-widest mb-6" style="letter-spacing: 0.2em;">
                    SILPD
                </h1>

                <!-- Tagline -->
                <p class="text-lg font-light leading-relaxed opacity-95">
                    Sistem Informasi Lumbung Padi Desa untuk pengelolaan data pertanian secara lebih mudah dan terstruktur.
                </p>
            </div>
        </div>

        <!-- Bagian Kanan: Form Login -->
        <div class="login-container w-full md:w-1/2 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12 animate-slide-in bg-white rounded-3xl soft-shadow">
            <div class="w-full max-w-md">
                <!-- Header Form -->
                <div class="mb-10">
                    <h2 class="text-4xl font-bold text-gray-900 mb-2">
                        Masuk Akun
                    </h2>
                    <p class="text-gray-600 text-base">
                        Silakan masuk untuk melanjutkan ke sistem
                    </p>
                </div>

                <!-- Error Messages -->
                @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="space-y-2">
                        @foreach($errors->all() as $error)
                        <li class="text-sm text-red-700 flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ $error }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Form -->
                <form action="{{ route('login') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="username" class="block text-sm font-semibold text-gray-900 mb-2">
                            Email
                        </label>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            value="{{ old('username') }}"
                            placeholder="Masukkan email"
                            autocomplete="off"
                            class="input-field w-full px-4 text-sm rounded-xl text-gray-900 transition-all"
                            required
                        >
                        @error('username')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-900 mb-2">
                            Kata Sandi
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Masukkan kata sandi"
                                class="input-field w-full px-4 text-sm rounded-xl text-gray-900 pr-12 transition-all"
                                required
                            >
                            <button
                                type="button"
                                onclick="togglePasswordVisibility()"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                                title="Tampilkan/sembunyikan password"
                            >
                                <svg id="eyeIcon" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tombol Masuk -->
                    <button
                        type="submit"
                        class="btn-gradient w-full px-4 py-3 text-white font-bold rounded-xl soft-shadow mt-8 mb-4 text-base"
                    >
                        Masuk
                    </button>
                </form>

                <!-- Forgot Password Link (jika route tersedia) -->
                @if(Route::has('password.request'))
                <div class="text-center">
                    <a href="{{ route('password.request') }}" class="text-sm text-teal-600 hover:text-teal-700 font-medium transition-colors">
                        Lupa kata sandi?
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
            }
        }
    </script>
</body>
</html>
