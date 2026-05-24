<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tahfidz Cluster - K-Means Clustering Santri</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            background-color: #f8fafc;
            /* slate-50 */
            font-family: 'Instrument Sans', sans-serif;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.02);
        }
    </style>
</head>

<body class="min-h-screen flex flex-col md:flex-row text-slate-800" x-data="{ mobileSidebarOpen: false, openLogoutModal: false }">

    <aside :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
        class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-slate-200 flex flex-col justify-between transition-transform duration-300 ease-in-out md:sticky md:top-0 md:translate-x-0 h-screen shrink-0">

        <!-- Top Sidebar Logo -->
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="w-12 h-12 object-contain rounded-lg">
                <div>
                    <span class="font-semibold text-sm text-slate-900 tracking-tight block leading-tight">TahfidzCluster</span>
                    <span class="text-xxs font-medium text-slate-400 block -mt-0.5">K-Means SPK Santri</span>
                </div>
            </div>

            <!-- Mobile Close Button -->
            <button @click="mobileSidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-600 focus:outline-none text-xs">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Sidebar Navigation Menu Items -->
        <nav class="flex-grow py-6 px-4 space-y-1.5 overflow-y-auto">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
                class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ Request::is('dashboard') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}">
                <span class="text-sm w-5 text-center"><i class="fa-solid fa-chart-pie"></i></span>
                <span>Dashboard</span>
            </a>

            <!-- Data Santri -->
            <a href="{{ route('santri.index') }}"
                class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ Request::is('santri*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}">
                <span class="text-sm w-5 text-center"><i class="fa-solid fa-graduation-cap"></i></span>
                <span>Data Santri</span>
            </a>

            <!-- Data Kriteria -->
            <a href="{{ route('kriteria.index') }}"
                class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ Request::is('kriteria*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}">
                <span class="text-sm w-5 text-center"><i class="fa-solid fa-sliders"></i></span>
                <span>Data Kriteria</span>
            </a>

            <!-- Data Nilai Santri -->
            <a href="{{ route('nilai.index') }}"
                class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ Request::is('nilai*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}">
                <span class="text-sm w-5 text-center"><i class="fa-solid fa-award"></i></span>
                <span>Data Nilai Santri</span>
            </a>

            <!-- Proses K-Means -->
            <a href="{{ url('hasil/proses') }}"
                class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ Request::is('hasil/proses') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}">
                <span class="text-sm w-5 text-center"><i class="fa-solid fa-bolt"></i></span>
                <span>Proses K-Means</span>
            </a>

            <!-- Hasil Clustering -->
            <a href="{{ route('hasil.index') }}"
                class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ Request::is('hasil') || Request::is('/') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}">
                <span class="text-sm w-5 text-center"><i class="fa-solid fa-chart-line"></i></span>
                <span>Hasil Clustering</span>
            </a>

            @if(Auth::user()->role === 'admin')
            <!-- Manajemen Akun -->
            <a href="{{ route('user.index') }}"
                class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ Request::is('user*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}">
                <span class="text-sm w-5 text-center"><i class="fa-solid fa-users-gear"></i></span>
                <span>Manajemen Akun</span>
            </a>
            @endif

            <!-- Pengaturan Akun -->
            <a href="{{ route('profile.edit') }}"
                class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ Request::is('profile*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}">
                <span class="text-sm w-5 text-center"><i class="fa-solid fa-user-gear"></i></span>
                <span>Pengaturan Akun</span>
            </a>
        </nav>

        <!-- Bottom Sidebar Profile Info & Logout -->
        <div class="p-4 border-t border-slate-100 space-y-3">
            <div class="flex items-center space-x-3 px-2">
                <div class="w-9 h-9 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center font-semibold text-xs text-slate-600 uppercase">
                    {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                </div>
                <div class="overflow-hidden">
                    <span class="block text-xs font-semibold text-slate-800 truncate">{{ Auth::user()->name ?? 'Administrator' }}</span>
                    <span class="block text-xxs font-medium text-emerald-600 capitalize">{{ Auth::user()->role ?? 'Admin' }}</span>
                </div>
            </div>

            <!-- Logout Button (POST Form) -->
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="w-full">
                @csrf
                <button type="button" @click="openLogoutModal = true" class="w-full flex items-center space-x-3 px-3.5 py-2 rounded-xl text-xs font-semibold text-rose-600 hover:bg-rose-50 transition">
                    <span class="text-sm w-5 text-center"><i class="fa-solid fa-right-from-bracket"></i></span>
                    <span>Keluar Sistem</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- CONTENT WRAPPER -->
    <div class="flex-grow flex flex-col min-h-screen overflow-x-hidden">

        <!-- Top Navbar Mobile Header -->
        <header class="md:hidden bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="w-8 h-8 object-contain rounded-lg">
                <span class="font-semibold text-xs text-slate-800 tracking-tight">TahfidzCluster</span>
            </div>

            <button @click="mobileSidebarOpen = true" class="p-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-600 text-xs font-semibold flex items-center space-x-1.5">
                <span><i class="fa-solid fa-bars"></i></span>
                <span>Menu</span>
            </button>
        </header>

        <!-- Toast Notifications -->
        <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            @if(session('success'))
            <div class="p-3.5 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-xl shadow-xs flex items-center justify-between" x-data="{ show: true }" x-show="show" x-transition>
                <div class="flex items-center space-x-2.5">
                    <span class="text-xs text-emerald-600"><i class="fa-solid fa-circle-check"></i></span>
                    <span class="text-xs font-medium">{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-400 hover:text-emerald-600 text-sm font-semibold">&times;</button>
            </div>
            @endif

            @if(session('error'))
            <div class="p-3.5 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded-r-xl shadow-xs flex items-center justify-between" x-data="{ show: true }" x-show="show" x-transition>
                <div class="flex items-center space-x-2.5">
                    <span class="text-xs text-rose-600"><i class="fa-solid fa-circle-exclamation"></i></span>
                    <span class="text-xs font-medium">{{ session('error') }}</span>
                </div>
                <button @click="show = false" class="text-rose-400 hover:text-rose-600 text-sm font-semibold">&times;</button>
            </div>
            @endif
        </div>

        <!-- Main Content View Area -->
        <main class="flex-grow py-6 px-4 sm:px-6 lg:px-8 max-w-7xl w-full mx-auto">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-slate-100 py-5 mt-auto">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between text-slate-400 text-xxs font-medium">
                <p>&copy; 2026 TahfidzCluster. SPK K-Means Clustering.</p>
                <div class="flex space-x-3 mt-1 sm:mt-0">
                    <span class="text-emerald-600">Emerald Light Edition</span>
                    <span>•</span>
                    <span>Standard Euclidean SPK</span>
                </div>
            </div>
        </footer>
    </div>

    <!-- LOGOUT CONFIRMATION MODAL -->
    <div x-show="openLogoutModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="openLogoutModal = false"></div>
            
            <div class="relative z-10 w-full max-w-sm bg-white rounded-2xl shadow-xl overflow-hidden text-left transition-all transform p-6 space-y-4">
                <div class="flex items-center space-x-3 text-rose-600">
                    <span class="text-xl flex items-center justify-center"><i class="fa-solid fa-circle-question"></i></span>
                    <h3 class="text-sm font-semibold text-slate-800">Konfirmasi Keluar</h3>
                </div>
                
                <p class="text-xs text-slate-500 font-medium">Apakah Anda yakin ingin keluar dari sistem TahfidzCluster?</p>
                
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="openLogoutModal = false" class="px-3.5 py-2 border border-slate-200 hover:bg-slate-50 rounded-xl text-xxs font-semibold text-slate-500">Batal</button>
                    <button type="button" @click="document.getElementById('logout-form').submit()" class="px-3.5 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xxs font-semibold shadow-xs">Keluar</button>
                </div>
            </div>
        </div>
    </div>

</body>

</html>