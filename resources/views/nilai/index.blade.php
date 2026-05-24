@extends('layouts.app')

@section('content')
<div x-data="{ 
        openAddModal: false, 
        openEditModal: false, 
        openDeleteModal: false,
        deleteActionUrl: '',
        openBulkDeleteModal: false,
        editNilai: {},
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
            <h1 class="text-lg font-semibold tracking-tight text-slate-800">Data Nilai Kriteria Santri</h1>
            <p class="text-slate-400 text-xs font-medium mt-0.5">Kelola dan input nilai kriteria Hafalan (C1), Murojaah (C2), dan Tahsin (C3) santri secara teratur.</p>
        </div>

        <button @click="openAddModal = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-semibold text-xs shadow-sm transition flex items-center space-x-2">
            <span><i class="fa-solid fa-plus"></i></span>
            <span>Input Nilai Baru</span>
        </button>
    </div>

    <!-- Search, Sort & Bulk Action Area -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Search Form -->
        <form action="{{ route('nilai.index') }}" method="GET" class="w-full md:w-96 flex items-center bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 transition focus-within:ring-2 focus-within:ring-emerald-500/10 focus-within:border-emerald-500">
            <span class="text-slate-400 mr-2 text-xs"><i class="fa-solid fa-magnifying-glass"></i></span>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama santri..." class="bg-transparent border-none outline-none text-xs text-slate-700 w-full focus:ring-0">
            <input type="hidden" name="sort_by" value="{{ $sortBy }}">
            <input type="hidden" name="sort_dir" value="{{ $sortDir }}">
            @if($search)
            <a href="{{ route('nilai.index') }}" class="text-xxs text-slate-400 hover:text-slate-600 font-semibold px-2 py-0.5 bg-slate-200 rounded-md">Reset</a>
            @endif
        </form>

        <!-- Dynamic Bulk Action Bar -->
        <div x-show="selectedIds.length > 0" style="display: none;" class="flex items-center space-x-3 w-full md:w-auto bg-rose-50 border border-rose-100 px-4 py-2 rounded-xl" x-transition>
            <span class="text-xxs font-semibold text-rose-700"><span x-text="selectedIds.length"></span> baris nilai dipilih</span>
            <form id="bulk-delete-form" action="{{ url('nilai/destroy-bulk') }}" method="POST" class="inline">
                @csrf
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                <button type="button" @click="openBulkDeleteModal = true" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-500 text-white rounded-lg text-xxs font-semibold shadow-sm transition">
                    Hapus Nilai Terpilih
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
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-600 text-xs font-semibold uppercase tracking-wider">
                        <!-- Bulk checkbox -->
                        <th class="px-6 py-4 text-center w-12">
                            <input type="checkbox" :checked="selectAll" @click="toggleSelectAll" class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500 cursor-pointer">
                        </th>

                        <!-- Sortable Headers -->
                        <th class="px-6 py-4">
                            <a href="{{ route('nilai.index', ['search' => $search, 'sort_by' => 'nama', 'sort_dir' => $sortBy == 'nama' && $sortDir == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center space-x-1 hover:text-slate-700">
                                <span>Nama Santri</span>
                                <span>{!! $sortBy == 'nama' ? ($sortDir == 'asc' ? '↑' : '↓') : '↕' !!}</span>
                            </a>
                        </th>

                        <th class="px-6 py-4 text-center bg-emerald-50/20 text-emerald-800">
                            <a href="{{ route('nilai.index', ['search' => $search, 'sort_by' => 'C1', 'sort_dir' => $sortBy == 'C1' && $sortDir == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center justify-center space-x-1 hover:text-emerald-900">
                                <span>C1 (Hafalan)</span>
                                <span>{!! $sortBy == 'C1' ? ($sortDir == 'asc' ? '↑' : '↓') : '↕' !!}</span>
                            </a>
                        </th>

                        <th class="px-6 py-4 text-center bg-emerald-50/20 text-emerald-800">
                            <a href="{{ route('nilai.index', ['search' => $search, 'sort_by' => 'C2', 'sort_dir' => $sortBy == 'C2' && $sortDir == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center justify-center space-x-1 hover:text-emerald-900">
                                <span>C2 (Murojaah)</span>
                                <span>{!! $sortBy == 'C2' ? ($sortDir == 'asc' ? '↑' : '↓') : '↕' !!}</span>
                            </a>
                        </th>

                        <th class="px-6 py-4 text-center bg-emerald-50/20 text-emerald-800">
                            <a href="{{ route('nilai.index', ['search' => $search, 'sort_by' => 'C3', 'sort_dir' => $sortBy == 'C3' && $sortDir == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center justify-center space-x-1 hover:text-emerald-900">
                                <span>C3 (Tahsin)</span>
                                <span>{!! $sortBy == 'C3' ? ($sortDir == 'asc' ? '↑' : '↓') : '↕' !!}</span>
                            </a>
                        </th>

                        <th class="px-6 py-4 text-center">Status Kelengkapan</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 text-xs text-slate-600 font-medium">
                    @forelse($santris as $santri)
                    @php
                    $hafalan = null;
                    $murojaah = null;
                    $tahsin = null;
                    foreach($santri->nilai as $nilai) {
                    if($nilai->kriteria_id == 1) $hafalan = $nilai->nilai;
                    if($nilai->kriteria_id == 2) $murojaah = $nilai->nilai;
                    if($nilai->kriteria_id == 3) $tahsin = $nilai->nilai;
                    }
                    $isComplete = !is_null($hafalan) && !is_null($murojaah) && !is_null($tahsin);
                    @endphp
                    <tr class="hover:bg-slate-50/40 transition-colors" :class="selectedIds.includes('{{ $santri->id }}') ? 'bg-emerald-50/10' : ''">
                        <!-- Checkbox -->
                        <td class="px-6 py-4 text-center">
                            <input type="checkbox" :checked="selectedIds.includes('{{ $santri->id }}')" @click="toggleSelect('{{ $santri->id }}')" class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500 cursor-pointer">
                        </td>

                        <!-- Nama -->
                        <td class="px-6 py-4 font-semibold text-slate-800">
                            <div>{{ $santri->nama }}</div>
                            <div class="text-xxs text-slate-400 font-normal mt-0.5">Gender: {{ $santri->jenis_kelamin }}</div>
                        </td>

                        <!-- Scores -->
                        <td class="px-6 py-4 text-center bg-emerald-50/10 text-emerald-700 font-semibold text-sm">{{ $hafalan ?? '-' }}</td>
                        <td class="px-6 py-4 text-center bg-emerald-50/10 text-emerald-700 font-semibold text-sm">{{ $murojaah ?? '-' }}</td>
                        <td class="px-6 py-4 text-center bg-emerald-50/10 text-emerald-700 font-semibold text-sm">{{ $tahsin ?? '-' }}</td>

                        <!-- Status -->
                        <td class="px-6 py-4 text-center">
                            @if($isComplete)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xxs font-semibold bg-emerald-50 text-emerald-700">
                                Lengkap
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xxs font-semibold bg-amber-50 text-amber-700">
                                Belum Lengkap
                            </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center space-x-2">
                                @if($isComplete)
                                <button @click="
                                            editNilai = {
                                                santri_id: '{{ $santri->id }}',
                                                nama: '{{ addslashes($santri->nama) }}',
                                                nilai_hafalan: '{{ $hafalan }}',
                                                nilai_murojaah: '{{ $murojaah }}',
                                                nilai_tahsin: '{{ $tahsin }}'
                                            };
                                            openEditModal = true;
                                        "
                                    class="w-8 h-8 flex items-center justify-center rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-600 hover:text-emerald-700 transition"
                                    title="Ubah Skor">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </button>

                                <button type="button"
                                    @click="deleteActionUrl = '{{ route('nilai.destroy', $santri->id) }}'; openDeleteModal = true;"
                                    class="w-8 h-8 flex items-center justify-center rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-700 transition"
                                    title="Kosongkan Skor">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                                @else
                                <button @click="
                                            editNilai = {
                                                santri_id: '{{ $santri->id }}',
                                                nama: '{{ addslashes($santri->nama) }}',
                                                nilai_hafalan: '0',
                                                nilai_murojaah: '0',
                                                nilai_tahsin: '0'
                                            };
                                            openEditModal = true;
                                        "
                                    class="w-8 h-8 flex items-center justify-center rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-600 hover:text-emerald-700 transition"
                                    title="Input Nilai Baru">
                                    <i class="fa-solid fa-plus text-xs"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                            <div class="text-3xl text-slate-300 mb-2"><i class="fa-solid fa-award"></i></div>
                            <div class="text-xs font-semibold">Data nilai kriteria tidak ditemukan</div>
                            <div class="text-xxs font-medium text-slate-400">Silakan input skor kriteria untuk santri Anda.</div>
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

    <div x-show="openAddModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs" @click="openAddModal = false"></div>

            <div class="relative z-10 w-full max-w-lg bg-white rounded-2xl shadow-xl overflow-hidden text-left transition-all transform">
                <div class="bg-emerald-600 px-6 py-4 text-white flex justify-between items-center">
                    <h3 class="text-xs font-semibold">Input Skor Kriteria Santri</h3>
                    <button @click="openAddModal = false" class="text-white hover:text-emerald-100 transition" title="Tutup">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <form action="{{ route('nilai.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf

                    <!-- Dropdown Santri yang Belum Dinilai -->
                    <div>
                        <label class="block text-xxs font-semibold text-slate-500 uppercase tracking-wider mb-1">Pilih Santri</label>
                        <select name="santri_id" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-medium outline-none bg-white focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500">
                            <option value="">-- Pilih Santri yang Belum Dinilai --</option>
                            @foreach($availableSantri as $s)
                            <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->jenis_kelamin }})</option>
                            @endforeach
                        </select>
                        <span class="block text-xxs text-slate-400 mt-1 font-medium">Hanya memunculkan santri yang belum diinputkan skor kriterianya.</span>
                    </div>

                    <!-- NILAI INPUT -->
                    <div class="bg-slate-50 p-4 rounded-xl space-y-3">
                        <span class="block text-xxs font-semibold text-slate-700 uppercase tracking-wide">Input Skor Kriteria (0 - 100)</span>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xxs font-semibold text-slate-500 mb-1 text-center">C1: Hafalan</label>
                                <input type="number" name="nilai_hafalan" min="0" max="100" required class="w-full px-2 py-2 border border-slate-200 rounded-lg text-xs font-semibold bg-white text-center">
                            </div>
                            <div>
                                <label class="block text-xxs font-semibold text-slate-500 mb-1 text-center">C2: Murojaah</label>
                                <input type="number" name="nilai_murojaah" min="0" max="100" required class="w-full px-2 py-2 border border-slate-200 rounded-lg text-xs font-semibold bg-white text-center">
                            </div>
                            <div>
                                <label class="block text-xxs font-semibold text-slate-500 mb-1 text-center">C3: Tahsin</label>
                                <input type="number" name="nilai_tahsin" min="0" max="100" required class="w-full px-2 py-2 border border-slate-200 rounded-lg text-xs font-semibold bg-white text-center">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="openAddModal = false" class="px-3.5 py-2 border border-slate-200 hover:bg-slate-50 rounded-xl text-xxs font-semibold text-slate-500">Batal</button>
                        <button type="submit" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xxs font-semibold shadow-xs">Simpan Nilai</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div x-show="openEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="openEditModal = false"></div>

            <div class="relative z-10 w-full max-w-lg bg-white rounded-2xl shadow-xl overflow-hidden text-left transition-all transform">
                <div class="bg-emerald-600 px-6 py-4 text-white flex justify-between items-center">
                    <h3 class="text-xs font-semibold">Ubah Skor Kriteria Santri</h3>
                    <button @click="openEditModal = false" class="text-white hover:text-emerald-100 transition" title="Tutup">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <form :action="`{{ url('nilai') }}/${editNilai.santri_id}`" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xxs font-semibold text-slate-500 uppercase tracking-wider mb-1">Nama Santri</label>
                        <input type="text" disabled x-model="editNilai.nama" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs font-semibold bg-slate-50 text-slate-500">
                    </div>

                    <!-- NILAI INPUT -->
                    <div class="bg-slate-50 p-4 rounded-xl space-y-3">
                        <span class="block text-xxs font-semibold text-slate-700 uppercase tracking-wide">Ubah Skor Kriteria (0 - 100)</span>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xxs font-semibold text-slate-500 mb-1 text-center">C1: Hafalan</label>
                                <input type="number" name="nilai_hafalan" x-model="editNilai.nilai_hafalan" min="0" max="100" required class="w-full px-2 py-2 border border-slate-200 rounded-lg text-xs font-semibold bg-white text-center">
                            </div>
                            <div>
                                <label class="block text-xxs font-semibold text-slate-500 mb-1 text-center">C2: Murojaah</label>
                                <input type="number" name="nilai_murojaah" x-model="editNilai.nilai_murojaah" min="0" max="100" required class="w-full px-2 py-2 border border-slate-200 rounded-lg text-xs font-semibold bg-white text-center">
                            </div>
                            <div>
                                <label class="block text-xxs font-semibold text-slate-500 mb-1 text-center">C3: Tahsin</label>
                                <input type="number" name="nilai_tahsin" x-model="editNilai.nilai_tahsin" min="0" max="100" required class="w-full px-2 py-2 border border-slate-200 rounded-lg text-xs font-semibold bg-white text-center">
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
                    <h3 class="text-sm font-semibold text-slate-800">Konfirmasi Hapus</h3>
                </div>

                <p class="text-xs text-slate-500 font-medium">Apakah Anda yakin ingin menghapus seluruh skor kriteria santri ini? Tindakan ini tidak dapat dibatalkan.</p>

                <form :action="deleteActionUrl" method="POST" class="flex justify-end space-x-2 pt-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDeleteModal = false" class="px-3.5 py-2 border border-slate-200 hover:bg-slate-50 rounded-xl text-xxs font-semibold text-slate-500">Batal</button>
                    <button type="submit" class="px-3.5 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xxs font-semibold shadow-xs">Hapus Skor</button>
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

                <p class="text-xs text-slate-500 font-medium">Apakah Anda yakin ingin menghapus <span class="font-bold text-slate-700" x-text="selectedIds.length"></span> baris nilai terpilih? Tindakan ini tidak dapat dibatalkan.</p>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="openBulkDeleteModal = false" class="px-3.5 py-2 border border-slate-200 hover:bg-slate-50 rounded-xl text-xxs font-semibold text-slate-500">Batal</button>
                    <button type="button" @click="document.getElementById('bulk-delete-form').submit()" class="px-3.5 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xxs font-semibold shadow-xs">Hapus Semua</button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection