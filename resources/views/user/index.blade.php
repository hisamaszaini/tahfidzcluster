@extends('layouts.app')

@section('content')
<div x-data="{ 
        openAddModal: false, 
        openEditModal: false, 
        openDeleteModal: false,
        deleteActionUrl: '',
        editUser: {
            id: '',
            name: '',
            email: '',
            role: ''
        }
    }" 
    class="space-y-6">

    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200">
        <div>
            <h1 class="text-lg font-semibold tracking-tight text-slate-800">Manajemen Pengguna (Akun)</h1>
            <p class="text-slate-400 text-xs font-medium mt-0.5">Kelola data administrator dan pembimbing (musyrif) yang memiliki akses masuk ke sistem.</p>
        </div>
        
        <!-- Create Button -->
        <button @click="openAddModal = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-semibold text-xs shadow-sm transition flex items-center space-x-2 w-full sm:w-auto justify-center">
            <span><i class="fa-solid fa-plus"></i></span>
            <span>Tambah Pengguna</span>
        </button>
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

    <!-- Search, Sort & Info Area -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Search Form -->
        <form action="{{ route('user.index') }}" method="GET" class="w-full md:w-96 flex items-center bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 transition focus-within:ring-2 focus-within:ring-emerald-500/10 focus-within:border-emerald-500">
            <span class="text-slate-400 mr-2 text-xs"><i class="fa-solid fa-magnifying-glass"></i></span>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau email pengguna..." class="bg-transparent border-none outline-none text-xs text-slate-700 w-full focus:ring-0">
            <input type="hidden" name="sort_by" value="{{ $sortBy }}">
            <input type="hidden" name="sort_dir" value="{{ $sortDir }}">
            @if($search)
                <a href="{{ route('user.index') }}" class="text-xxs text-slate-400 hover:text-slate-600 font-semibold px-2 py-0.5 bg-slate-200 rounded-md">Reset</a>
            @endif
        </form>

        <div class="text-xxs text-slate-400 font-medium">
            Menampilkan <span class="text-slate-700 font-semibold">{{ $users->firstItem() ?? 0 }}</span> - <span class="text-slate-700 font-semibold">{{ $users->lastItem() ?? 0 }}</span> dari <span class="text-slate-700 font-semibold">{{ $users->total() }}</span> pengguna
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-600 text-xs font-medium uppercase tracking-wider">
                        
                        <!-- Sortable Headers -->
                        <th class="px-6 py-4">
                            <a href="{{ route('user.index', ['search' => $search, 'sort_by' => 'name', 'sort_dir' => $sortBy == 'name' && $sortDir == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center space-x-1 hover:text-slate-700">
                                <span>Nama Lengkap</span>
                                <span>{!! $sortBy == 'name' ? ($sortDir == 'asc' ? '↑' : '↓') : '↕' !!}</span>
                            </a>
                        </th>
                        
                        <th class="px-6 py-4">
                            <a href="{{ route('user.index', ['search' => $search, 'sort_by' => 'email', 'sort_dir' => $sortBy == 'email' && $sortDir == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center space-x-1 hover:text-slate-700">
                                <span>Alamat Email</span>
                                <span>{!! $sortBy == 'email' ? ($sortDir == 'asc' ? '↑' : '↓') : '↕' !!}</span>
                            </a>
                        </th>

                        <th class="px-6 py-4 text-center">
                            <a href="{{ route('user.index', ['search' => $search, 'sort_by' => 'role', 'sort_dir' => $sortBy == 'role' && $sortDir == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center space-x-1 hover:text-slate-700 justify-center">
                                <span>Peran</span>
                                <span>{!! $sortBy == 'role' ? ($sortDir == 'asc' ? '↑' : '↓') : '↕' !!}</span>
                            </a>
                        </th>

                        <th class="px-6 py-4">
                            <a href="{{ route('user.index', ['search' => $search, 'sort_by' => 'created_at', 'sort_dir' => $sortBy == 'created_at' && $sortDir == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center space-x-1 hover:text-slate-700">
                                <span>Tanggal Terdaftar</span>
                                <span>{!! $sortBy == 'created_at' ? ($sortDir == 'asc' ? '↑' : '↓') : '↕' !!}</span>
                            </a>
                        </th>
                        
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                
                <tbody class="divide-y divide-slate-100 text-xs text-slate-600 font-medium">
                    @forelse($users as $usr)
                        <tr class="hover:bg-slate-50/40 transition-colors">
                            
                            <!-- Nama -->
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center font-semibold text-xs text-slate-600 uppercase">
                                        {{ substr($usr->name, 0, 1) }}
                                    </div>
                                    <span class="font-semibold text-slate-800">{{ $usr->name }}</span>
                                    @if(Auth::user()->id === $usr->id)
                                        <span class="px-2 py-0.5 bg-slate-100 text-slate-400 rounded-md text-xxs font-medium">Anda</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Email -->
                            <td class="px-6 py-4 font-medium text-slate-500">{{ $usr->email }}</td>
                            
                            <!-- Role -->
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-xl text-xxs font-semibold uppercase tracking-wider {{ $usr->role == 'admin' ? 'bg-indigo-50 text-indigo-600 border border-indigo-100' : 'bg-emerald-50 text-emerald-600 border border-emerald-100' }}">
                                    {{ $usr->role }}
                                </span>
                            </td>

                            <!-- Created At -->
                            <td class="px-6 py-4 font-medium text-slate-400">
                                {{ \Carbon\Carbon::parse($usr->created_at)->translatedFormat('d F Y, H:i') }}
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <button @click="
                                        editUser = {
                                            id: '{{ $usr->id }}',
                                            name: '{{ addslashes($usr->name) }}',
                                            email: '{{ $usr->email }}',
                                            role: '{{ $usr->role }}'
                                        };
                                        openEditModal = true;
                                    " 
                                    class="w-8 h-8 flex items-center justify-center rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-600 hover:text-emerald-700 transition"
                                    title="Edit Pengguna">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>
                                    
                                    @if(Auth::user()->id !== $usr->id)
                                        <button type="button" 
                                                @click="deleteActionUrl = '{{ route('user.destroy', $usr->id) }}'; openDeleteModal = true;"
                                                class="w-8 h-8 flex items-center justify-center rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-700 transition"
                                                title="Hapus Pengguna">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    @else
                                        <span class="w-8 h-8 flex items-center justify-center text-slate-300 cursor-not-allowed" title="Tidak dapat menghapus diri sendiri">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                <div class="text-3xl text-slate-300 mb-2"><i class="fa-solid fa-users-gear"></i></div>
                                <div class="text-xs font-semibold">Data pengguna tidak ditemukan</div>
                                <div class="text-xxs font-medium text-slate-400">Silakan masukkan data atau ubah kriteria pencarian Anda.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $users->links('partials.pagination') }}
            </div>
        @endif
    </div>

    <!-- MODAL: ADD USER -->
    <div x-show="openAddModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="openAddModal = false"></div>
            
            <div class="relative z-10 w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden text-left transition-all transform">
                <div class="bg-emerald-600 px-6 py-4 text-white flex justify-between items-center">
                    <h3 class="text-xs font-semibold">Tambah Pengguna Baru</h3>
                    <button @click="openAddModal = false" class="text-white hover:text-emerald-100 transition" title="Tutup">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <form action="{{ route('user.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xxs font-semibold text-slate-500 uppercase tracking-wider mb-1">Nama Lengkap</label>
                        <input type="text" name="name" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xxs font-semibold text-slate-500 uppercase tracking-wider mb-1">Alamat Email</label>
                        <input type="email" name="email" required placeholder="email@tahfidz.com" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xxs font-semibold text-slate-500 uppercase tracking-wider mb-1">Peran (Role)</label>
                        <select name="role" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none bg-white focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500">
                            <option value="admin">Administrator (Utama)</option>
                            <option value="musyrif">Musyrif (Pembimbing)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xxs font-semibold text-slate-500 uppercase tracking-wider mb-1">Kata Sandi</label>
                        <input type="password" name="password" required placeholder="Minimal 8 karakter" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xxs font-semibold text-slate-500 uppercase tracking-wider mb-1">Konfirmasi Kata Sandi</label>
                        <input type="password" name="password_confirmation" required placeholder="Ulangi kata sandi" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500">
                    </div>

                    <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="openAddModal = false" class="px-3.5 py-2 border border-slate-200 hover:bg-slate-50 rounded-xl text-xxs font-semibold text-slate-500">Batal</button>
                        <button type="submit" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xxs font-semibold shadow-xs">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: EDIT USER -->
    <div x-show="openEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs" @click="openEditModal = false"></div>
            
            <div class="relative z-10 w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden text-left transition-all transform">
                <div class="bg-emerald-600 px-6 py-4 text-white flex justify-between items-center">
                    <h3 class="text-xs font-semibold">Ubah Data Pengguna</h3>
                    <button @click="openEditModal = false" class="text-white hover:text-emerald-100 transition" title="Tutup">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <form :action="`{{ url('user') }}/${editUser.id}`" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xxs font-semibold text-slate-500 uppercase tracking-wider mb-1">Nama Lengkap</label>
                        <input type="text" name="name" x-model="editUser.name" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xxs font-semibold text-slate-500 uppercase tracking-wider mb-1">Alamat Email</label>
                        <input type="email" name="email" x-model="editUser.email" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xxs font-semibold text-slate-500 uppercase tracking-wider mb-1">Peran (Role)</label>
                        <select name="role" x-model="editUser.role" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none bg-white focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500">
                            <option value="admin">Administrator (Utama)</option>
                            <option value="musyrif">Musyrif (Pembimbing)</option>
                        </select>
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 space-y-2">
                        <span class="block text-xxs font-bold text-slate-600 uppercase tracking-wider"><i class="fa-solid fa-key mr-1 text-emerald-600"></i> Ganti Kata Sandi (Opsional)</span>
                        <p class="text-xxs text-slate-400 leading-tight">Kosongkan jika Anda tidak berniat merubah kata sandi pengguna ini.</p>
                        
                        <div class="space-y-2 pt-1">
                            <div>
                                <label class="block text-xxs font-semibold text-slate-400 mb-1">Kata Sandi Baru</label>
                                <input type="password" name="password" placeholder="Kosongkan jika tidak diubah" class="w-full px-3 py-2 border border-slate-200 bg-white rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500">
                            </div>
                            <div>
                                <label class="block text-xxs font-semibold text-slate-400 mb-1">Ulangi Kata Sandi Baru</label>
                                <input type="password" name="password_confirmation" placeholder="Ulangi jika tidak diubah" class="w-full px-3 py-2 border border-slate-200 bg-white rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="openEditModal = false" class="px-3.5 py-2 border border-slate-200 hover:bg-slate-50 rounded-xl text-xxs font-semibold text-slate-500">Batal</button>
                        <button type="submit" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xxs font-semibold shadow-xs">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- DELETE CONFIRMATION MODAL -->
    <div x-show="openDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="openDeleteModal = false"></div>
            
            <div class="relative z-10 w-full max-w-sm bg-white rounded-2xl shadow-xl overflow-hidden text-left transition-all transform p-6 space-y-4">
                <div class="flex items-center space-x-3 text-rose-600">
                    <span class="text-xl flex items-center justify-center"><i class="fa-solid fa-triangle-exclamation"></i></span>
                    <h3 class="text-sm font-semibold text-slate-800">Hapus Akun Pengguna</h3>
                </div>
                
                <p class="text-xs text-slate-500 font-medium">Apakah Anda yakin ingin menghapus akun pengguna ini? Seluruh hak akses pengguna akan segera dinonaktifkan secara permanen.</p>
                
                <form :action="deleteActionUrl" method="POST" class="flex justify-end space-x-2 pt-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDeleteModal = false" class="px-3.5 py-2 border border-slate-200 hover:bg-slate-50 rounded-xl text-xxs font-semibold text-slate-500">Batal</button>
                    <button type="submit" class="px-3.5 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xxs font-semibold shadow-xs">Hapus Akun</button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
