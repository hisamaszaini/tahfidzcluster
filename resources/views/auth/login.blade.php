<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - TahfidzCluster</title>

    <!-- Vite CSS and JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Instrument Sans', sans-serif;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-0 md:p-6 lg:p-12">
    <!-- Main Card Container -->
    <div class="w-full max-w-6xl min-h-[600px] bg-white rounded-none md:rounded-3xl shadow-lg border border-slate-100 overflow-hidden flex flex-col md:flex-row">

        <div class="hidden md:flex w-1/2 bg-gradient-to-br from-emerald-600 to-teal-800 p-12 lg:p-16 flex-col justify-between text-white relative">
            <div class="absolute inset-0 bg-white/[0.02] backdrop-blur-xs pointer-events-none"></div>
            <div class="relative z-10 flex items-center space-x-2">
                <span class="text-xxs px-2.5 py-1 bg-white/20 rounded-full font-semibold tracking-wider uppercase"><i class="fa-solid fa-mosque mr-2"></i> TahfidzCluster - K-Means Clustering</span>
            </div>

            <div class="relative z-10 my-auto space-y-4 max-w-md">
                <div class="text-3xl font-semibold leading-snug tracking-tight">
                    Pengelompokan Karakteristik Tahfidz Santri Secara Objektif.
                </div>
                <p class="text-emerald-100 text-xs font-medium leading-relaxed">
                    Sistem klasifikasi cerdas menggunakan algoritma K-Means tanpa bobot untuk memetakan potensi dan kompetensi hafalan, murojaah, dan tahsin santri di pondok pesantren secara matematis dan presisi.
                </p>
            </div>

            <div class="relative z-10 grid grid-cols-3 gap-4 border-t border-white/10 pt-6">
                <div>
                    <span class="block text-xxs text-emerald-200 font-medium uppercase">Kriteria</span>
                    <span class="text-sm font-semibold mt-0.5">3 Dimensi</span>
                </div>
                <div>
                    <span class="block text-xxs text-emerald-200 font-medium uppercase">Akurasi</span>
                    <span class="text-sm font-semibold mt-0.5">Euclidean</span>
                </div>
                <div>
                    <span class="block text-xxs text-emerald-200 font-medium uppercase">Inisialisasi</span>
                    <span class="text-sm font-semibold mt-0.5">Objektif</span>
                </div>
            </div>
        </div>

        <!-- SECTION 2 -->
        <div class="w-full md:w-1/2 p-8 sm:p-12 lg:p-16 flex flex-col justify-between bg-white">
            <div class="flex flex-col items-center justify-center text-center mb-8">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="w-36 h-36 object-contain rounded-2xl mb-3 shadow-xs border border-slate-100/50">
            </div>

            <!-- Login Form Area -->
            <div class="my-auto space-y-6">
                <div>
                    <h2 class="text-xl font-semibold text-slate-800 tracking-tight">Selamat Datang</h2>
                    <p class="text-slate-400 text-xs font-medium mt-1">Silakan masuk menggunakan kredensial akun Anda untuk mengakses dashboard analisis.</p>
                </div>

                @if($errors->any())
                <div class="p-4 bg-rose-50 border-l-4 border-rose-500 rounded-r-lg text-rose-800 text-xs font-medium">
                    {{ $errors->first() }}
                </div>
                @endif

                @if(session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg text-emerald-800 text-xs font-medium">
                    {{ session('success') }}
                </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                    @csrf
                    <!-- Email Input -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Alamat Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="contoh@domain.com"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 text-xs font-medium outline-none transition focus:bg-white focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500">
                    </div>

                    <!-- Password Input -->
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <label for="password" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Kata Sandi</label>
                        </div>
                        <input type="password" id="password" name="password" required placeholder="••••••••"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 text-xs font-medium outline-none transition focus:bg-white focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500">
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember" class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                        <label for="remember" class="ml-2 text-xs font-medium text-slate-500">Ingat perangkat saya</label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-semibold shadow-md shadow-emerald-200 transition duration-200">
                        Masuk ke Dashboard
                    </button>
                </form>
            </div>

            <!-- Footer Text -->
            <div class="text-xs text-slate-400 font-medium mt-8 border-t border-slate-100 pt-4">
                Sistem Pendukung Keputusan &bull; Bidang Akademik Tahfidzul Qur'an
            </div>
        </div>

    </div>
</body>

</html>