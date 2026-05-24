@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200">
        <div>
            <h1 class="text-lg font-semibold tracking-tight text-slate-800">Pengaturan Akun</h1>
            <p class="text-slate-400 text-xs font-medium mt-0.5">Perbarui informasi profil dan ganti kata sandi pengaman akun Anda secara mandiri.</p>
        </div>
    </div>

    <!-- Error/Validation Messages -->
    @if($errors->any())
        <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded-r-xl shadow-xs space-y-1">
            <span class="block text-xs font-semibold"><i class="fa-solid fa-circle-exclamation mr-1.5"></i> Terjadi kesalahan validasi:</span>
            <ul class="list-disc list-inside text-xxs font-medium text-rose-600 pl-1.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Side: Profile Info Form -->
        <div class="lg:col-span-1 bg-white p-6 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center space-x-3 pb-4 border-b border-slate-100">
                    <span class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-semibold">
                        <i class="fa-solid fa-id-card"></i>
                    </span>
                    <div>
                        <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Informasi Profil</h2>
                        <p class="text-xxs font-medium text-slate-400">Ubah nama lengkap dan alamat email Anda.</p>
                    </div>
                </div>

                <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block text-xxs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 text-xs font-medium outline-none transition focus:bg-white focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500">
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-xxs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Alamat Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 text-xs font-medium outline-none transition focus:bg-white focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500">
                    </div>

                    <!-- Role Badge (ReadOnly) -->
                    <div>
                        <label class="block text-xxs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Peran Sistem (Role)</label>
                        <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xxs font-semibold uppercase tracking-wider select-none {{ $user->role === 'admin' ? 'bg-indigo-50 text-indigo-600 border border-indigo-100' : 'bg-emerald-50 text-emerald-600 border border-emerald-100' }}">
                            <i class="fa-solid {{ $user->role === 'admin' ? 'fa-user-shield' : 'fa-graduation-cap' }} mr-1.5 text-xs"></i> {{ $user->role }}
                        </span>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-semibold shadow-xs transition duration-150 active:scale-98">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="text-xxs text-slate-400 font-medium pt-6 border-t border-slate-100 mt-6 leading-relaxed">
                Perubahan pada email akan langsung memperbarui kredensial login Anda. Pastikan email aktif agar dapat terus mengakses sistem.
            </div>
        </div>

        <!-- Right Side: Change Password Form -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
            <div class="space-y-4">
                <div class="flex items-center space-x-3 pb-4 border-b border-slate-100">
                    <span class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-sm font-semibold">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <div>
                        <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Ubah Kata Sandi</h2>
                        <p class="text-xxs font-medium text-slate-400">Ganti kata sandi pengaman akun Anda secara berkala.</p>
                    </div>
                </div>

                <form action="{{ route('profile.password') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    @method('PUT')

                    <!-- Current Password Field -->
                    <div class="md:col-span-2">
                        <label for="current_password" class="block text-xxs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kata Sandi Saat Ini (Lama)</label>
                        <input type="password" id="current_password" name="current_password" required placeholder="••••••••"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 text-xs font-medium outline-none transition focus:bg-white focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500">
                    </div>

                    <!-- New Password Field -->
                    <div>
                        <label for="password" class="block text-xxs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kata Sandi Baru</label>
                        <input type="password" id="password" name="password" required placeholder="Minimal 8 karakter"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 text-xs font-medium outline-none transition focus:bg-white focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500">
                    </div>

                    <!-- Confirm New Password Field -->
                    <div>
                        <label for="password_confirmation" class="block text-xxs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi kata sandi baru"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 text-xs font-medium outline-none transition focus:bg-white focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500">
                    </div>

                    <div class="md:col-span-2 pt-4 flex justify-end">
                        <button type="submit" class="py-2.5 px-6 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-semibold shadow-xs transition duration-150 active:scale-98">
                            Ganti Kata Sandi
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</div>
@endsection
