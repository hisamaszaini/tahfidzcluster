@extends('layouts.app')

@section('content')
<div x-data="{ 
        openAddModal: false, 
        openEditModal: false, 
        openImportModal: false,
        openDeleteModal: false,
        deleteActionUrl: '',
        openBulkDeleteModal: false,
        editSantri: {},
        selectedIds: [],
        selectAll: false,
        toggleSelectAll() {
            this.selectAll = !this.selectAll;
            if (this.selectAll) {
                this.selectedIds = [@foreach($santris as $santri)'{{ $santri->id }}',@endforeach];
            } else {
                this.selectedIds = [];
            }
        },
        toggleSelect(id) {
            if (this.selectedIds.includes(id)) {
                this.selectedIds = this.selectedIds.filter(item => item !== id);
                this.selectAll = false;
            } else {
                this.selectedIds.push(id);
                if (this.selectedIds.length === {{ $santris->count() }}) {
                    this.selectAll = true;
                }
            }
        }
    }" 
    class="space-y-6">

    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200">
        <div>
            <h1 class="text-lg font-semibold tracking-tight text-slate-800">Data Master Santri</h1>
            <p class="text-slate-400 text-xs font-medium mt-0.5">Kelola seluruh data profil santri dan skor kriteria (Hafalan, Murojaah, Tahsin).</p>
        </div>
        
        <div class="flex flex-wrap gap-2 w-full sm:w-auto">
            <!-- Import Button -->
            <button @click="openImportModal = true" class="px-4 py-2 border border-slate-200 hover:bg-slate-50 rounded-xl font-semibold text-xs text-slate-600 transition flex items-center space-x-2">
                <span><i class="fa-solid fa-file-import"></i></span>
                <span>Impor CSV</span>
            </button>
            
            <!-- Create Button -->
            <button @click="openAddModal = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-semibold text-xs shadow-sm transition flex items-center space-x-2">
                <span><i class="fa-solid fa-plus"></i></span>
                <span>Tambah Santri</span>
            </button>
        </div>
    </div>

    <!-- Search, Sort & Bulk Action Area -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Search Form -->
        <form action="{{ route('santri.index') }}" method="GET" class="w-full md:w-96 flex items-center bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 transition focus-within:ring-2 focus-within:ring-emerald-500/10 focus-within:border-emerald-500">
            <span class="text-slate-400 mr-2 text-xs"><i class="fa-solid fa-magnifying-glass"></i></span>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau alamat santri..." class="bg-transparent border-none outline-none text-xs text-slate-700 w-full focus:ring-0">
            <input type="hidden" name="sort_by" value="{{ $sortBy }}">
            <input type="hidden" name="sort_dir" value="{{ $sortDir }}">
            @if($search)
                <a href="{{ route('santri.index') }}" class="text-xxs text-slate-400 hover:text-slate-600 font-semibold px-2 py-0.5 bg-slate-200 rounded-md">Reset</a>
            @endif
        </form>

        <!-- Dynamic Bulk Action Bar (AlpineJS) -->
        <div x-show="selectedIds.length > 0" style="display: none;" class="flex items-center space-x-3 w-full md:w-auto bg-rose-50 border border-rose-100 px-4 py-2 rounded-xl" x-transition>
            <span class="text-xxs font-semibold text-rose-700"><span x-text="selectedIds.length"></span> santri dipilih</span>
            <form id="bulk-delete-form" action="{{ url('santri/destroy-bulk') }}" method="POST" class="inline">
                @csrf
                <!-- Dynamic hidden inputs for bulk IDs -->
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                <button type="button" @click="openBulkDeleteModal = true" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-500 text-white rounded-lg text-xxs font-semibold shadow-sm transition">
                    Hapus Terpilih
                </button>
            </form>
        </div>

        <div x-show="selectedIds.length === 0" class="text-xxs text-slate-400 font-medium">
            Menampilkan <span class="text-slate-700 font-semibold">{{ $santris->firstItem() ?? 0 }}</span> - <span class="text-slate-700 font-semibold">{{ $santris->lastItem() ?? 0 }}</span> dari <span class="text-slate-700 font-semibold">{{ $santris->total() }}</span> santri
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-600 text-xs font-medium uppercase tracking-wider">
                        <!-- Bulk select column -->
                        <th class="px-6 py-4 text-center w-12">
                            <input type="checkbox" :checked="selectAll" @click="toggleSelectAll" class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500 cursor-pointer">
                        </th>
                        
                        <!-- Sortable Headers -->
                        <th class="px-6 py-4">
                            <a href="{{ route('santri.index', ['search' => $search, 'sort_by' => 'nama', 'sort_dir' => $sortBy == 'nama' && $sortDir == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center space-x-1 hover:text-slate-700">
                                <span>Nama Santri</span>
                                <span>{!! $sortBy == 'nama' ? ($sortDir == 'asc' ? '↑' : '↓') : '↕' !!}</span>
                            </a>
                        </th>
                        <th class="px-6 py-4 text-center">Gender</th>
                        <th class="px-6 py-4">
                            <a href="{{ route('santri.index', ['search' => $search, 'sort_by' => 'tanggal_lahir', 'sort_dir' => $sortBy == 'tanggal_lahir' && $sortDir == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center space-x-1 hover:text-slate-700">
                                <span>Tanggal Lahir</span>
                                <span>{!! $sortBy == 'tanggal_lahir' ? ($sortDir == 'asc' ? '↑' : '↓') : '↕' !!}</span>
                            </a>
                        </th>
                        <th class="px-6 py-4">
                            <a href="{{ route('santri.index', ['search' => $search, 'sort_by' => 'alamat', 'sort_dir' => $sortBy == 'alamat' && $sortDir == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center space-x-1 hover:text-slate-700">
                                <span>Alamat</span>
                                <span>{!! $sortBy == 'alamat' ? ($sortDir == 'asc' ? '↑' : '↓') : '↕' !!}</span>
                            </a>
                        </th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                
                <tbody class="divide-y divide-slate-100 text-xs text-slate-600 font-medium">
                    @forelse($santris as $santri)
                        @php
                            $hafalan = 0;
                            $murojaah = 0;
                            $tahsin = 0;
                            foreach($santri->nilai as $nilai) {
                                if($nilai->kriteria_id == 1) $hafalan = $nilai->nilai;
                                if($nilai->kriteria_id == 2) $murojaah = $nilai->nilai;
                                if($nilai->kriteria_id == 3) $tahsin = $nilai->nilai;
                            }
                        @endphp
                        <tr class="hover:bg-slate-50/40 transition-colors" :class="selectedIds.includes('{{ $santri->id }}') ? 'bg-emerald-50/10' : ''">
                            <!-- Checkbox Column -->
                            <td class="px-6 py-4 text-center">
                                <input type="checkbox" :checked="selectedIds.includes('{{ $santri->id }}')" @click="toggleSelect('{{ $santri->id }}')" class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500 cursor-pointer">
                            </td>

                            <!-- Nama -->
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $santri->nama }}</td>
                            
                            <!-- Gender -->
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-0.5 rounded-lg text-xxs font-semibold {{ $santri->jenis_kelamin == 'L' ? 'bg-sky-50 text-sky-600' : 'bg-pink-50 text-pink-600' }}">
                                    {{ $santri->jenis_kelamin }}
                                </span>
                            </td>

                            <!-- Tanggal Lahir -->
                            <td class="px-6 py-4 font-medium text-slate-500">
                                {{ \Carbon\Carbon::parse($santri->tanggal_lahir)->translatedFormat('d F Y') }}
                            </td>

                            <!-- Alamat -->
                            <td class="px-6 py-4 font-normal text-slate-400 max-w-xs truncate">{{ $santri->alamat }}</td>

                            <!-- Actions -->
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <button @click="
                                        editSantri = {
                                            id: '{{ $santri->id }}',
                                            nama: '{{ addslashes($santri->nama) }}',
                                            jenis_kelamin: '{{ $santri->jenis_kelamin }}',
                                            tanggal_lahir: '{{ $santri->tanggal_lahir }}',
                                            alamat: '{{ addslashes($santri->alamat) }}',
                                            nilai_hafalan: '{{ $hafalan }}',
                                            nilai_murojaah: '{{ $murojaah }}',
                                            nilai_tahsin: '{{ $tahsin }}'
                                        };
                                        openEditModal = true;
                                    " 
                                    class="w-8 h-8 flex items-center justify-center rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-600 hover:text-emerald-700 transition"
                                    title="Edit Santri">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>
                                    
                                    <button type="button" 
                                            @click="deleteActionUrl = '{{ route('santri.destroy', $santri->id) }}'; openDeleteModal = true;"
                                            class="w-8 h-8 flex items-center justify-center rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-700 transition"
                                            title="Hapus Santri">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <div class="text-3xl text-slate-300 mb-2"><i class="fa-solid fa-graduation-cap"></i></div>
                                <div class="text-xs font-semibold">Data santri tidak ditemukan</div>
                                <div class="text-xxs font-medium text-slate-400">Silakan masukkan data atau impor dari file CSV.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($santris->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $santris->links('partials.pagination') }}
            </div>
        @endif
    </div>

    <!-- MODAL: ADD SANTRI -->
    <div x-show="openAddModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="openAddModal = false"></div>
            
            <div class="relative z-10 w-full max-w-lg bg-white rounded-2xl shadow-xl overflow-hidden text-left transition-all transform">
                <div class="bg-emerald-600 px-6 py-4 text-white flex justify-between items-center">
                    <h3 class="text-xs font-semibold">Tambah Profil Santri Baru</h3>
                    <button @click="openAddModal = false" class="text-white hover:text-emerald-100 transition" title="Tutup">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <form action="{{ route('santri.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xxs font-semibold text-slate-500 uppercase tracking-wider mb-1">Nama Lengkap</label>
                        <input type="text" name="nama" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xxs font-semibold text-slate-500 uppercase tracking-wider mb-1">Jenis Kelamin</label>
                            <select name="jenis_kelamin" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none bg-white focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500">
                                <option value="L">Laki-laki (L)</option>
                                <option value="P">Perempuan (P)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xxs font-semibold text-slate-500 uppercase tracking-wider mb-1">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xxs font-semibold text-slate-500 uppercase tracking-wider mb-1">Alamat Rumah</label>
                        <textarea name="alamat" required rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500"></textarea>
                    </div>

                    <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="openAddModal = false" class="px-3.5 py-2 border border-slate-200 hover:bg-slate-50 rounded-xl text-xxs font-semibold text-slate-500">Batal</button>
                        <button type="submit" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xxs font-semibold shadow-xs">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: EDIT SANTRI -->
    <div x-show="openEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="openEditModal = false"></div>
            
            <div class="relative z-10 w-full max-w-lg bg-white rounded-2xl shadow-xl overflow-hidden text-left transition-all transform">
                <div class="bg-emerald-600 px-6 py-4 text-white flex justify-between items-center">
                    <h3 class="text-xs font-semibold">Ubah Profil & Nilai Santri</h3>
                    <button @click="openEditModal = false" class="text-white hover:text-emerald-100 transition" title="Tutup">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <form :action="`{{ url('santri') }}/${editSantri.id}`" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xxs font-semibold text-slate-500 uppercase tracking-wider mb-1">Nama Lengkap</label>
                        <input type="text" name="nama" x-model="editSantri.nama" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xxs font-semibold text-slate-500 uppercase tracking-wider mb-1">Jenis Kelamin</label>
                            <select name="jenis_kelamin" x-model="editSantri.jenis_kelamin" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none bg-white focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500">
                                <option value="L">Laki-laki (L)</option>
                                <option value="P">Perempuan (P)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xxs font-semibold text-slate-500 uppercase tracking-wider mb-1">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" x-model="editSantri.tanggal_lahir" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xxs font-semibold text-slate-500 uppercase tracking-wider mb-1">Alamat Rumah</label>
                        <textarea name="alamat" x-model="editSantri.alamat" required rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500"></textarea>
                    </div>

                    <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="openEditModal = false" class="px-3.5 py-2 border border-slate-200 hover:bg-slate-50 rounded-xl text-xxs font-semibold text-slate-500">Batal</button>
                        <button type="submit" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xxs font-semibold shadow-xs">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: IMPORT CSV -->
    <div x-show="openImportModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="openImportModal = false"></div>
            
            <div class="relative z-10 w-full max-w-lg bg-white rounded-2xl shadow-xl overflow-hidden text-left transition-all transform" x-data="{ importTab: 'file' }">
                <div class="bg-emerald-600 px-6 py-4 text-white flex justify-between items-center">
                    <h3 class="text-xs font-semibold">Impor Data Santri Massal</h3>
                    <button @click="openImportModal = false" class="text-white hover:text-emerald-100 transition" title="Tutup">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <!-- Tab selectors -->
                <div class="flex border-b border-slate-100 text-xxs font-semibold">
                    <button @click="importTab = 'file'" :class="importTab === 'file' ? 'border-emerald-500 text-emerald-600 border-b-2 font-semibold' : 'text-slate-400 hover:text-slate-600'" class="w-1/2 py-3 text-center flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-file-arrow-up"></i> Unggah File CSV
                    </button>
                    <button @click="importTab = 'text'" :class="importTab === 'text' ? 'border-emerald-500 text-emerald-600 border-b-2 font-semibold' : 'text-slate-400 hover:text-slate-600'" class="w-1/2 py-3 text-center flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-keyboard"></i> Tempel Teks CSV
                    </button>
                </div>

                <form action="{{ url('santri/import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                    @csrf
                    
                    <!-- File upload tab -->
                    <div x-show="importTab === 'file'" class="space-y-2">
                        <label class="block text-xxs font-semibold text-slate-500 uppercase tracking-wider">Pilih File CSV (.csv / .txt)</label>
                        <input type="file" name="csv_file" class="w-full text-xs text-slate-600 border border-slate-200 rounded-xl p-2 bg-slate-50 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xxs file:font-semibold file:bg-emerald-50 file:text-emerald-700 file:cursor-pointer">
                    </div>

                    <!-- Textarea paste tab -->
                    <div x-show="importTab === 'text'" style="display: none;" class="space-y-2">
                        <label class="block text-xxs font-semibold text-slate-500 uppercase tracking-wider">Tempel Konten CSV Mentah</label>
                        <textarea name="csv_text" rows="5" placeholder="nama,jenis_kelamin,tanggal_lahir,alamat,nilai_hafalan,nilai_murojaah,nilai_tahsin&#10;Muhammad Ali,L,2009-08-12,Jakarta,80,75,90&#10;Fatimah Azzahra,P,2010-02-14,Solo,95,90,92" 
                                  class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-mono outline-none focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500"></textarea>
                    </div>

                    <!-- Guidelines -->
                    <div class="bg-slate-50 p-3.5 rounded-xl space-y-2">
                        <span class="block text-xxs font-bold text-slate-600 uppercase tracking-wide flex items-center gap-1.5"><i class="fa-solid fa-circle-info text-emerald-600"></i> Ketentuan Format Kolom CSV:</span>
                        <ul class="text-xxs text-slate-400 font-medium list-disc list-inside space-y-1">
                            <li>Format kolom harus urut: <code class="text-emerald-700 font-semibold bg-emerald-50 px-1 rounded">nama,jenis_kelamin,tanggal_lahir,alamat,nilai_hafalan,nilai_murojaah,nilai_tahsin</code></li>
                            <li>Kolom <code class="text-slate-700">jenis_kelamin</code> bernilai <code class="text-slate-700">L</code> atau <code class="text-slate-700">P</code>.</li>
                            <li>Kolom <code class="text-slate-700">tanggal_lahir</code> berformat <code class="text-slate-700">YYYY-MM-DD</code> (misal: <code class="text-slate-700">2010-05-15</code>).</li>
                            <li>Nilai kriteria berkisar antara <code class="text-slate-700">0</code> sampai <code class="text-slate-700">100</code>.</li>
                        </ul>
                    </div>

                    <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="openImportModal = false" class="px-3.5 py-2 border border-slate-200 hover:bg-slate-50 rounded-xl text-xxs font-semibold text-slate-500">Batal</button>
                        <button type="submit" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xxs font-semibold shadow-xs">Impor Sekarang</button>
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
                    <h3 class="text-sm font-semibold text-slate-800">Konfirmasi Hapus</h3>
                </div>
                
                <p class="text-xs text-slate-500 font-medium">Apakah Anda yakin ingin menghapus data santri ini beserta nilai akademiknya? Tindakan ini tidak dapat dibatalkan.</p>
                
                <form :action="deleteActionUrl" method="POST" class="flex justify-end space-x-2 pt-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDeleteModal = false" class="px-3.5 py-2 border border-slate-200 hover:bg-slate-50 rounded-xl text-xxs font-semibold text-slate-500">Batal</button>
                    <button type="submit" class="px-3.5 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xxs font-semibold shadow-xs">Hapus Data</button>
                </form>
            </div>
        </div>
    </div>

    <!-- BULK DELETE CONFIRMATION MODAL -->
    <div x-show="openBulkDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="openBulkDeleteModal = false"></div>
            
            <div class="relative z-10 w-full max-w-sm bg-white rounded-2xl shadow-xl overflow-hidden text-left transition-all transform p-6 space-y-4">
                <div class="flex items-center space-x-3 text-rose-600">
                    <span class="text-xl flex items-center justify-center"><i class="fa-solid fa-triangle-exclamation"></i></span>
                    <h3 class="text-sm font-semibold text-slate-800">Hapus Terpilih</h3>
                </div>
                
                <p class="text-xs text-slate-500 font-medium">Apakah Anda yakin ingin menghapus <span class="font-bold text-slate-700" x-text="selectedIds.length"></span> santri terpilih beserta seluruh data nilai mereka? Tindakan ini tidak dapat dibatalkan.</p>
                
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="openBulkDeleteModal = false" class="px-3.5 py-2 border border-slate-200 hover:bg-slate-50 rounded-xl text-xxs font-semibold text-slate-500">Batal</button>
                    <button type="button" @click="document.getElementById('bulk-delete-form').submit()" class="px-3.5 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xxs font-semibold shadow-xs">Hapus Semua</button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
