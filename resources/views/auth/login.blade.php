<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Login - SILPD</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        body {
            background-image: url('/images/rice-field.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .gradient-overlay {
            background: linear-gradient(135deg, rgba(15, 41, 38, 0.66) 0%, rgba(15, 41, 38, 0.66) 85%);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .input-field {
            transition: all 0.3s ease;
        }

        .input-field:focus {
            transform: translateY(-2px);
        }

        /* Animasi untuk sisi kiri (dari atas ke bawah) */
        @keyframes slideDownFade {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Animasi untuk sisi kanan (dari bawah ke atas) */
        @keyframes slideUpFade {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Animasi untuk elemen individual */
        .animate-slide-down {
            animation: slideDownFade 0.8s ease-out forwards;
            opacity: 0;
        }

        .animate-slide-up {
            animation: slideUpFade 0.8s ease-out forwards;
            opacity: 0;
        }

        /* Delay untuk setiap elemen */
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-500 { animation-delay: 0.5s; }
        .delay-600 { animation-delay: 0.6s; }
        .delay-700 { animation-delay: 0.7s; }

        @media (max-width: 768px) {
            .left-brand {
                display: none;
            }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="gradient-overlay absolute inset-0"></div>
    
    <div class="relative z-10 w-full max-w-6xl flex gap-4">
        <!-- Left Section: Branding -->
        <div class="left-brand hidden md:flex w-1/2 flex-col items-center justify-center px-12 py-16 text-white">
            <div class="text-center max-w-md">
                <!-- Logo -->
                <div class="mb-8 flex justify-center animate-slide-down delay-100">
                    <div class="w-24 h-24 bg-teal-600/20 backdrop-blur-md rounded-2xl flex items-center justify-center shadow-lg">
                        <span class="text-6xl">🌾</span>
                    </div>
                </div>

                <!-- Title -->
                <h1 class="text-5xl font-bold mb-6 tracking-widest animate-slide-down delay-200">
                    SILPD
                </h1>

                <!-- Tagline -->
                <p class="text-lg leading-relaxed opacity-95 mb-8 animate-slide-down delay-300">
                    Sistem Informasi Lumbung Padi Desa
                </p>

                <div class="w-20 h-1 bg-white/40 mx-auto mb-8 animate-slide-down delay-400"></div>

                <p class="text-sm leading-relaxed opacity-80 animate-slide-down delay-500">
                    Pengelolaan data pertanian secara modern, mudah, dan terstruktur untuk kemajuan desa
                </p>
            </div>
        </div>

        <!-- Right Section: Form -->
        <div class="w-full md:w-1/2 flex items-center justify-center px-4">
            <div class="glass-effect w-full max-w-md rounded-2xl shadow-2xl p-8 md:p-10">
                <!-- Form Header -->
                <div class="mb-8 animate-slide-up delay-100">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">
                        Masuk Akun
                    </h2>
                    <p class="text-gray-600 text-sm">
                        Silakan masuk untuk melanjutkan ke sistem
                    </p>
                </div>

                <!-- Error Messages -->
                @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg animate-slide-up delay-200">
                    <div class="space-y-1">
                        @foreach($errors->all() as $error)
                        <div class="text-sm text-red-700 flex items-start gap-2">
                            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ $error }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Login Form -->
                <form action="{{ route('login') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Username Field -->
                    <div class="animate-slide-up delay-300">
                        <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">
                            Username
                        </label>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            value="{{ old('username') }}"
                            placeholder="Masukkan username"
                            autocomplete="username"
                            class="input-field w-full px-4 py-3 text-sm border-2 border-gray-200 rounded-xl focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-200"
                            required
                        >
                    </div>

                    <!-- Password Field -->
                    <div class="animate-slide-up delay-400">
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Masukkan password"
                                autocomplete="current-password"
                                class="input-field w-full px-4 py-3 text-sm border-2 border-gray-200 rounded-xl pr-12 focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-200"
                                required
                            >
                            <button
                                type="button"
                                onclick="togglePasswordVisibility()"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                            >
                                <svg id="eyeIcon" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full px-4 py-3.5 bg-teal-700 hover:bg-teal-800 text-white font-semibold rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-lg mt-6 animate-slide-up delay-500"
                    >
                        Masuk
                    </button>
                </form>

                <!-- Back to Home Button -->
                <div class="mt-6 text-center animate-slide-up delay-600">
                    <a href="http://silpd.test/" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-teal-700 font-medium transition-colors duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Beranda
                    </a>
                </div>

                <!-- Footer Note -->
                <div class="mt-8 pt-6 border-t border-gray-200 text-center animate-slide-up delay-700">
                    <p class="text-xs text-gray-500">
                        Akun dibuat oleh Admin Desa
                    </p>
                </div>
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

    <script>
        // ===== HALAMAN LOGIN: TOMBOL BACK → KE LANDING PAGE =====
        // Masalah: history browser masih menyimpan entri-entri palsu dari pushState dashboard.
        // Solusi: saat di halaman login, tangkap event popstate (tombol back/forward) dan
        //         langsung replace ke landing page — memotong semua entri palsu sekaligus.
        (function () {
            // Tandai entry login di history agar kita tahu posisi kita
            history.replaceState({ page: 'login' }, '', window.location.href);

            window.addEventListener('popstate', function (e) {
                // User menekan tombol back/forward di halaman login → redirect ke landing page
                window.location.replace('http://silpd.test/');
            });
        })();
    </script>
</body>
</html>
