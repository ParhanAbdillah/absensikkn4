<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-800 tracking-tight">
            Kelola Anggota KKN
        </h2>
    </x-slot>

    <div x-data="userIndex()" class="py-6">
        <div class="max-w-7xl mx-auto space-y-5">

            {{-- Breadcrumb --}}
            <div class="flex items-center gap-2 text-xs text-slate-400 font-semibold">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span>Dashboard</span>
                <span class="text-slate-300">/</span>
                <span class="text-emerald-600 font-bold">Kelola Anggota</span>
            </div>

            {{-- Main Card --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                {{-- Table Toolbar --}}
                <div class="p-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">

                    {{-- Left: Show entries + search --}}
                    <div class="flex flex-wrap items-center gap-3">
                        {{-- Tampilkan entries --}}
                        <div class="flex items-center gap-2 text-sm text-slate-500 font-semibold">
                            <span class="hidden sm:inline">Tampilkan</span>
                            <select x-model="perPage" @change="currentPage = 1"
                                    class="border border-slate-200 rounded-lg pl-2.5 pr-8 py-1.5 text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-white transition">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                            <span class="hidden sm:inline">data</span>
                        </div>

                        {{-- Search --}}
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"></path></svg>
                            </div>
                            <input x-model="search" @input="currentPage = 1" type="text"
                                   placeholder="Cari nama, NIM, email, divisi, kelas..."
                                   class="pl-9 pr-4 py-2 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none w-64 transition">
                        </div>

                        {{-- Filter Role --}}
                        <select x-model="filterRole" @change="currentPage = 1"
                                class="border border-slate-200 rounded-xl pl-3 pr-10 py-2 text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-white transition">
                            <option value="">Semua Peran</option>
                            <option value="anggota">Anggota</option>
                            <option value="sekretaris">Sekretaris</option>
                            <option value="koordinator">Koordinator</option>
                            <option value="dpl">DPL</option>
                        </select>
                    </div>

                    {{-- Right: Tambah Anggota button --}}
                    <button @click="$dispatch('open-modal', 'modal-tambah-user'); resetAddSignature();"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold text-sm rounded-xl transition-all shadow-md shadow-emerald-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Anggota
                    </button>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-5 py-3.5 text-left">
                                    <button @click="sortBy('name')" class="flex items-center gap-2 text-[11px] font-bold text-slate-500 uppercase tracking-wider hover:text-emerald-600 transition group">
                                        Nama
                                        <div class="flex flex-col items-center">
                                            <svg class="w-2 h-2" :class="sortCol === 'name' && sortDir === 'asc' ? 'text-emerald-600' : 'text-slate-300 group-hover:text-slate-400'" fill="currentColor" viewBox="0 0 320 512"><path d="M182.6 137.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-9.2 9.2-11.9 22.9-6.9 34.9s16.6 19.8 29.6 19.8H288c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-128-128z"/></svg>
                                            <svg class="w-2 h-2 mt-0.5" :class="sortCol === 'name' && sortDir === 'desc' ? 'text-emerald-600' : 'text-slate-300 group-hover:text-slate-400'" fill="currentColor" viewBox="0 0 320 512"><path d="M137.4 374.6c12.5 12.5 32.8 12.5 45.3 0l128-128c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8L32 192c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l128 128z"/></svg>
                                        </div>
                                    </button>
                                </th>
                                <th class="px-5 py-3.5 text-left">
                                    <button @click="sortBy('nim')" class="flex items-center gap-2 text-[11px] font-bold text-slate-500 uppercase tracking-wider hover:text-emerald-600 transition group">
                                        NIM / Identitas
                                        <div class="flex flex-col items-center">
                                            <svg class="w-2 h-2" :class="sortCol === 'nim' && sortDir === 'asc' ? 'text-emerald-600' : 'text-slate-300 group-hover:text-slate-400'" fill="currentColor" viewBox="0 0 320 512"><path d="M182.6 137.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-9.2 9.2-11.9 22.9-6.9 34.9s16.6 19.8 29.6 19.8H288c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-128-128z"/></svg>
                                            <svg class="w-2 h-2 mt-0.5" :class="sortCol === 'nim' && sortDir === 'desc' ? 'text-emerald-600' : 'text-slate-300 group-hover:text-slate-400'" fill="currentColor" viewBox="0 0 320 512"><path d="M137.4 374.6c12.5 12.5 32.8 12.5 45.3 0l128-128c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8L32 192c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l128 128z"/></svg>
                                        </div>
                                    </button>
                                </th>
                                <th class="px-5 py-3.5 text-left">
                                    <button @click="sortBy('divisi')" class="flex items-center gap-2 text-[11px] font-bold text-slate-500 uppercase tracking-wider hover:text-emerald-600 transition group">
                                        Divisi
                                        <div class="flex flex-col items-center">
                                            <svg class="w-2 h-2" :class="sortCol === 'divisi' && sortDir === 'asc' ? 'text-emerald-600' : 'text-slate-300 group-hover:text-slate-400'" fill="currentColor" viewBox="0 0 320 512"><path d="M182.6 137.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-9.2 9.2-11.9 22.9-6.9 34.9s16.6 19.8 29.6 19.8H288c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-128-128z"/></svg>
                                            <svg class="w-2 h-2 mt-0.5" :class="sortCol === 'divisi' && sortDir === 'desc' ? 'text-emerald-600' : 'text-slate-300 group-hover:text-slate-400'" fill="currentColor" viewBox="0 0 320 512"><path d="M137.4 374.6c12.5 12.5 32.8 12.5 45.3 0l128-128c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8L32 192c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l128 128z"/></svg>
                                        </div>
                                    </button>
                                </th>
                                <th class="px-5 py-3.5 text-left">
                                    <button @click="sortBy('class')" class="flex items-center gap-2 text-[11px] font-bold text-slate-500 uppercase tracking-wider hover:text-emerald-600 transition group">
                                        Kelas
                                        <div class="flex flex-col items-center">
                                            <svg class="w-2 h-2" :class="sortCol === 'class' && sortDir === 'asc' ? 'text-emerald-600' : 'text-slate-300 group-hover:text-slate-400'" fill="currentColor" viewBox="0 0 320 512"><path d="M182.6 137.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-9.2 9.2-11.9 22.9-6.9 34.9s16.6 19.8 29.6 19.8H288c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-128-128z"/></svg>
                                            <svg class="w-2 h-2 mt-0.5" :class="sortCol === 'class' && sortDir === 'desc' ? 'text-emerald-600' : 'text-slate-300 group-hover:text-slate-400'" fill="currentColor" viewBox="0 0 320 512"><path d="M137.4 374.6c12.5 12.5 32.8 12.5 45.3 0l128-128c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8L32 192c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l128 128z"/></svg>
                                        </div>
                                    </button>
                                </th>
                                <th class="px-5 py-3.5 text-left">
                                    <button @click="sortBy('email')" class="flex items-center gap-2 text-[11px] font-bold text-slate-500 uppercase tracking-wider hover:text-emerald-600 transition group">
                                        Email
                                        <div class="flex flex-col items-center">
                                            <svg class="w-2 h-2" :class="sortCol === 'email' && sortDir === 'asc' ? 'text-emerald-600' : 'text-slate-300 group-hover:text-slate-400'" fill="currentColor" viewBox="0 0 320 512"><path d="M182.6 137.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-9.2 9.2-11.9 22.9-6.9 34.9s16.6 19.8 29.6 19.8H288c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-128-128z"/></svg>
                                            <svg class="w-2 h-2 mt-0.5" :class="sortCol === 'email' && sortDir === 'desc' ? 'text-emerald-600' : 'text-slate-300 group-hover:text-slate-400'" fill="currentColor" viewBox="0 0 320 512"><path d="M137.4 374.6c12.5 12.5 32.8 12.5 45.3 0l128-128c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8L32 192c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l128 128z"/></svg>
                                        </div>
                                    </button>
                                </th>
                                <th class="px-5 py-3.5 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">No. WhatsApp</th>
                                <th class="px-5 py-3.5 text-left">
                                    <button @click="sortBy('role')" class="flex items-center gap-2 text-[11px] font-bold text-slate-500 uppercase tracking-wider hover:text-emerald-600 transition group">
                                        Peran
                                        <div class="flex flex-col items-center">
                                            <svg class="w-2 h-2" :class="sortCol === 'role' && sortDir === 'asc' ? 'text-emerald-600' : 'text-slate-300 group-hover:text-slate-400'" fill="currentColor" viewBox="0 0 320 512"><path d="M182.6 137.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-9.2 9.2-11.9 22.9-6.9 34.9s16.6 19.8 29.6 19.8H288c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-128-128z"/></svg>
                                            <svg class="w-2 h-2 mt-0.5" :class="sortCol === 'role' && sortDir === 'desc' ? 'text-emerald-600' : 'text-slate-300 group-hover:text-slate-400'" fill="currentColor" viewBox="0 0 320 512"><path d="M137.4 374.6c12.5 12.5 32.8 12.5 45.3 0l128-128c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8L32 192c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l128 128z"/></svg>
                                        </div>
                                    </button>
                                </th>
                                <th class="px-5 py-3.5 text-center text-[11px] font-bold text-slate-500 uppercase tracking-wider">Tanda Tangan</th>
                                <th class="px-5 py-3.5 text-center text-[11px] font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="u in paginatedUsers" :key="u.id">
                                <tr class="hover:bg-slate-50/70 transition-colors duration-150">
                                    {{-- Nama --}}
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full flex items-center justify-center font-extrabold text-sm flex-shrink-0 text-white"
                                                 :style="`background-color: ${avatarColor(u.name)}`"
                                                 x-text="(u.name || '?').charAt(0).toUpperCase()">
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-slate-800" x-text="u.name"></div>
                                                <div class="text-[11px] text-slate-400 font-mono" x-text="u.nim || '—'"></div>
                                            </div>
                                        </div>
                                    </td>
                                    {{-- NIM --}}
                                    <td class="px-5 py-3.5 font-mono text-xs text-slate-500" x-text="u.nim || '—'"></td>
                                    {{-- Divisi --}}
                                    <td class="px-5 py-3.5 text-sm font-semibold text-slate-600" x-text="u.divisi || '—'"></td>
                                    {{-- Kelas --}}
                                    <td class="px-5 py-3.5 text-sm font-semibold text-slate-600" x-text="u.class || '—'"></td>
                                    {{-- Email --}}
                                    <td class="px-5 py-3.5">
                                        <span class="text-sm text-slate-600" x-text="u.email"></span>
                                    </td>
                                    {{-- Phone --}}
                                    <td class="px-5 py-3.5 text-sm text-slate-500" x-text="u.phone || '—'"></td>
                                    {{-- Role --}}
                                    <td class="px-5 py-3.5">
                                        <span class="inline-flex px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full"
                                              :class="{
                                                  'bg-emerald-100 text-emerald-700 border border-emerald-200': u.role === 'koordinator',
                                                  'bg-purple-100 text-purple-700 border border-purple-200': u.role === 'sekretaris',
                                                  'bg-blue-100 text-blue-700 border border-blue-200': u.role === 'dpl',
                                                  'bg-slate-100 text-slate-600 border border-slate-200': u.role === 'anggota'
                                              }"
                                              x-text="u.role">
                                        </span>
                                    </td>
                                    {{-- Tanda Tangan --}}
                                    <td class="px-5 py-3.5 text-center">
                                        <template x-if="u.signature_url">
                                            <div class="inline-flex items-center gap-1">
                                                <button @click="openPreviewModal(u)" title="Lihat Tanda Tangan" class="group relative px-2 py-1 border border-slate-200 rounded-lg hover:border-emerald-500 bg-slate-50 hover:bg-emerald-50/50 transition">
                                                    <img :src="u.signature_url" class="h-7 w-14 object-contain" alt="TTD">
                                                    <div class="absolute inset-0 bg-slate-900/10 group-hover:bg-emerald-600/20 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                                        <svg class="w-3.5 h-3.5 text-emerald-700 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    </div>
                                                </button>
                                            </div>
                                        </template>
                                        <template x-if="!u.signature_url">
                                            <span class="inline-flex items-center text-[10px] font-semibold text-slate-400 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                                Belum Ada
                                            </span>
                                        </template>
                                    </td>
                                    {{-- Actions --}}
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center justify-center gap-2">
                                            <button @click="openEditModal(u)"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-xs rounded-lg transition border border-emerald-200">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                Edit
                                            </button>
                                            <template x-if="{{ Auth::id() }} !== u.id">
                                                <button @click="confirmDelete(u)"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 font-bold text-xs rounded-lg transition border border-red-200">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    Hapus
                                                </button>
                                            </template>
                                            <template x-if="{{ Auth::id() }} === u.id">
                                                <span class="inline-flex items-center px-3 py-1.5 bg-slate-50 text-slate-400 font-bold text-xs rounded-lg border border-slate-200">
                                                    Aktif
                                                </span>
                                            </template>
                                        </div>
                                    </td>
                                </tr>
                            </template>

                            {{-- Empty state --}}
                            <template x-if="filteredUsers.length === 0">
                                <tr>
                                    <td colspan="9" class="py-16 text-center">
                                        <div class="flex flex-col items-center gap-3 text-slate-300">
                                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            <p class="text-sm font-semibold text-slate-400">Tidak ada data anggota yang sesuai.</p>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Table Footer: info + pagination --}}
                <div class="px-5 py-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 bg-slate-50/50">
                    {{-- Info --}}
                    <p class="text-xs text-slate-500 font-semibold" x-text="paginationInfo()"></p>

                    {{-- Pagination --}}
                    <div class="flex items-center gap-1.5" x-show="totalPages > 1">
                        <button @click="currentPage = Math.max(1, currentPage - 1)"
                                :disabled="currentPage === 1"
                                :class="currentPage === 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300'"
                                class="px-3 py-1.5 text-xs font-bold text-slate-500 border border-slate-200 rounded-lg transition">
                            ← Prev
                        </button>
                        <template x-for="page in pagesArray" :key="page">
                            <button @click="currentPage = page"
                                    :class="currentPage === page
                                        ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm'
                                        : 'text-slate-600 border-slate-200 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300'"
                                    class="w-8 h-8 text-xs font-bold border rounded-lg transition"
                                    x-text="page">
                            </button>
                        </template>
                        <button @click="currentPage = Math.min(totalPages, currentPage + 1)"
                                :disabled="currentPage === totalPages"
                                :class="currentPage === totalPages ? 'opacity-40 cursor-not-allowed' : 'hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300'"
                                class="px-3 py-1.5 text-xs font-bold text-slate-500 border border-slate-200 rounded-lg transition">
                            Next →
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hidden delete form --}}
        <form id="deleteForm" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>



        {{-- ====== MODAL TAMBAH ANGGOTA ====== --}}
        <x-modal name="modal-tambah-user" :show="false" focusable>
            <form action="{{ route('koordinator.users.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-emerald-100 rounded-xl text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 leading-none mb-1">Tambah Anggota KKN</h3>
                        <p class="text-xs text-slate-400 font-semibold">Isi data lengkap anggota kelompok baru</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <x-input-label for="name" value="Nama Lengkap *" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required autofocus placeholder="Contoh: Ahmad Fauzi" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="nim" value="NIM / NRP" />
                            <x-text-input id="nim" class="block mt-1 w-full" type="text" name="nim" placeholder="2201010001" />
                        </div>
                        <div>
                            <x-input-label for="divisi" value="Divisi" />
                            <x-text-input id="divisi" class="block mt-1 w-full" type="text" name="divisi" placeholder="Contoh: Humas" />
                        </div>
                        <div>
                            <x-input-label for="class" value="Kelas (Jurusan)" />
                            <x-text-input id="class" class="block mt-1 w-full" type="text" name="class" placeholder="Contoh: MKP" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="email" value="Email *" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" required placeholder="anggota@kkn.local" />
                        </div>
                        <div>
                            <x-input-label for="phone" value="No. WhatsApp *" />
                            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" placeholder="08xxxxxxxxxx" required />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="password" value="Password Awal *" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required placeholder="Min. 8 karakter" />
                        </div>
                        <div>
                            <x-input-label for="role" value="Peran *" />
                            <select id="role" name="role" class="border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl shadow-sm block mt-1 w-full text-sm" required>
                                <option value="anggota">Anggota (Mahasiswa)</option>
                                <option value="sekretaris">Sekretaris</option>
                                <option value="koordinator">Koordinator</option>
                                <option value="dpl">DPL (Dosen)</option>
                            </select>
                        </div>
                    </div>

                    {{-- Signature Input (Canvas / File Upload) --}}
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <x-input-label value="Tanda Tangan Anggota" />
                            <div class="flex items-center bg-slate-100 p-0.5 rounded-lg">
                                <button type="button" @click="signatureModeAdd = 'draw'" :class="signatureModeAdd === 'draw' ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="px-2.5 py-1 text-[11px] font-bold rounded-md transition">
                                    Gambar TTD
                                </button>
                                <button type="button" @click="signatureModeAdd = 'file'" :class="signatureModeAdd === 'file' ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="px-2.5 py-1 text-[11px] font-bold rounded-md transition">
                                    Upload File
                                </button>
                            </div>
                        </div>

                        {{-- Draw Canvas Mode --}}
                        <div x-show="signatureModeAdd === 'draw'">
                            <div class="relative w-full h-36 border border-slate-200 rounded-xl bg-slate-50 overflow-hidden mt-1">
                                <canvas id="signatureCanvasAdd" class="w-full h-full cursor-crosshair touch-none"></canvas>
                                <button type="button" id="clearSigBtnAdd" class="absolute bottom-2 right-2 px-2.5 py-1 bg-rose-500 hover:bg-rose-600 active:scale-95 text-white text-[10px] font-bold rounded-lg transition shadow-sm">
                                    Hapus TTD
                                </button>
                            </div>
                            <input type="hidden" name="signature" id="signatureInputAdd">
                            <p class="text-[10px] text-slate-400 mt-1 font-semibold">Gunakan mouse atau layar sentuh untuk menggambar tanda tangan di atas.</p>
                        </div>

                        {{-- File Upload Mode --}}
                        <div x-show="signatureModeAdd === 'file'" class="mt-1">
                            <label class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-slate-200 hover:border-emerald-400 rounded-xl cursor-pointer bg-slate-50 hover:bg-emerald-50/20 transition group">
                                <template x-if="!filePreviewUrlAdd">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-2 text-slate-400 group-hover:text-emerald-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <p class="text-xs font-bold text-slate-600 group-hover:text-emerald-700">Klik untuk unggah file gambar TTD</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">PNG, JPG, JPEG, WEBP (Maks. 2MB)</p>
                                    </div>
                                </template>
                                <template x-if="filePreviewUrlAdd">
                                    <div class="relative w-full h-full flex items-center justify-center p-2">
                                        <img :src="filePreviewUrlAdd" class="max-h-full max-w-full object-contain" alt="Preview File Upload">
                                        <span class="absolute bottom-2 right-2 px-2 py-0.5 bg-emerald-600 text-white text-[10px] font-bold rounded">Terpilih</span>
                                    </div>
                                </template>
                                <input type="file" name="signature_file" accept="image/*" @change="handleFileUpload($event, 'add')" class="hidden" />
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100 flex justify-end gap-3">
                    <x-secondary-button @click="$dispatch('close')">Batal</x-secondary-button>
                    <x-primary-button class="bg-emerald-600 hover:bg-emerald-700 gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Anggota
                    </x-primary-button>
                </div>
            </form>
        </x-modal>

        {{-- ====== MODAL EDIT ANGGOTA ====== --}}
        <x-modal name="modal-edit-user" :show="false" focusable>
            <form :action="'/koordinator/users/' + editData.id" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="remove_signature" :value="removeSignature ? '1' : '0'">

                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-blue-100 rounded-xl text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 leading-none mb-1">Edit Data Anggota</h3>
                        <p class="text-xs text-slate-400 font-semibold">Ubah data anggota kelompok KKN</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <x-input-label for="edit_name" value="Nama Lengkap *" />
                        <x-text-input id="edit_name" class="block mt-1 w-full" type="text" name="name" x-model="editData.name" required />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="edit_nim" value="NIM / NRP" />
                            <x-text-input id="edit_nim" class="block mt-1 w-full" type="text" name="nim" x-model="editData.nim" />
                        </div>
                        <div>
                            <x-input-label for="edit_divisi" value="Divisi" />
                            <x-text-input id="edit_divisi" class="block mt-1 w-full" type="text" name="divisi" x-model="editData.divisi" />
                        </div>
                        <div>
                            <x-input-label for="edit_class" value="Kelas (Jurusan)" />
                            <x-text-input id="edit_class" class="block mt-1 w-full" type="text" name="class" x-model="editData.class" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="edit_email" value="Email *" />
                            <x-text-input id="edit_email" class="block mt-1 w-full" type="email" name="email" x-model="editData.email" required />
                        </div>
                        <div>
                            <x-input-label for="edit_phone" value="No. WhatsApp *" />
                            <x-text-input id="edit_phone" class="block mt-1 w-full" type="text" name="phone" x-model="editData.phone" required />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="edit_password" value="Password Baru" />
                            <x-text-input id="edit_password" class="block mt-1 w-full" type="password" name="password" placeholder="Kosongkan jika tidak diubah" />
                        </div>
                        <div>
                            <x-input-label for="edit_role" value="Peran *" />
                            <select id="edit_role" name="role" class="border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl shadow-sm block mt-1 w-full text-sm" x-model="editData.role" required>
                                <option value="anggota">Anggota (Mahasiswa)</option>
                                <option value="sekretaris">Sekretaris</option>
                                <option value="koordinator">Koordinator</option>
                                <option value="dpl">DPL (Dosen)</option>
                            </select>
                        </div>
                    </div>

                    {{-- Existing Signature Preview & Delete Action --}}
                    <div class="mt-4 pt-3 border-t border-slate-100">
                        <div class="flex items-center justify-between mb-2">
                            <x-input-label value="Tanda Tangan Saat Ini" />
                            <template x-if="editData.signature_url && !removeSignature">
                                <button type="button" @click="removeSignature = true" class="text-xs font-bold text-rose-600 hover:text-rose-700 inline-flex items-center gap-1 px-2.5 py-1 bg-rose-50 rounded-lg border border-rose-200 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Hapus TTD
                                </button>
                            </template>
                            <template x-if="removeSignature">
                                <button type="button" @click="removeSignature = false" class="text-xs font-bold text-slate-600 hover:text-slate-700 underline">
                                    Batal Hapus TTD
                                </button>
                            </template>
                        </div>

                        <template x-if="editData.signature_url && !removeSignature">
                            <div class="p-3 border border-slate-200 rounded-xl bg-slate-50 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <img :src="editData.signature_url" class="h-16 max-w-[180px] object-contain bg-white p-1 rounded border border-slate-200 shadow-sm" alt="Signature">
                                    <span class="text-xs font-medium text-slate-500">Tanda tangan tersimpan</span>
                                </div>
                                <button type="button" @click="openPreviewModal(editData)" class="px-3 py-1.5 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-lg transition border border-slate-200 shadow-sm">
                                    Preview Zoom
                                </button>
                            </div>
                        </template>

                        <template x-if="!editData.signature_url && !removeSignature">
                            <div class="p-3 border border-dashed border-slate-200 rounded-xl bg-slate-50 text-xs text-slate-400 font-semibold text-center">
                                Belum ada tanda tangan tersimpan.
                            </div>
                        </template>

                        <template x-if="removeSignature">
                            <div class="p-3 border border-rose-200 bg-rose-50 rounded-xl text-xs text-rose-600 font-bold flex items-center gap-2">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Tanda tangan akan dihapus saat disimpan.
                            </div>
                        </template>
                    </div>

                    {{-- Signature Edit Mode (Draw or Upload) --}}
                    <div class="mt-4">
                        <div class="flex items-center justify-between mb-1">
                            <x-input-label value="Ubah/Tambahkan Tanda Tangan" />
                            <div class="flex items-center bg-slate-100 p-0.5 rounded-lg">
                                <button type="button" @click="signatureModeEdit = 'draw'" :class="signatureModeEdit === 'draw' ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="px-2.5 py-1 text-[11px] font-bold rounded-md transition">
                                    Gambar TTD
                                </button>
                                <button type="button" @click="signatureModeEdit = 'file'" :class="signatureModeEdit === 'file' ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="px-2.5 py-1 text-[11px] font-bold rounded-md transition">
                                    Upload File
                                </button>
                            </div>
                        </div>

                        {{-- Mode 1: Draw Canvas --}}
                        <div x-show="signatureModeEdit === 'draw'">
                            <div class="relative w-full h-36 border border-slate-200 rounded-xl bg-slate-50 overflow-hidden mt-1">
                                <canvas id="signatureCanvasEdit" class="w-full h-full cursor-crosshair touch-none"></canvas>
                                <button type="button" id="clearSigBtnEdit" class="absolute bottom-2 right-2 px-2.5 py-1 bg-rose-500 hover:bg-rose-600 active:scale-95 text-white text-[10px] font-bold rounded-lg transition shadow-sm">
                                    Hapus Coretan
                                </button>
                            </div>
                            <input type="hidden" name="signature" id="signatureInputEdit">
                            <p class="text-[10px] text-slate-400 mt-1 font-semibold">Gunakan mouse atau layar sentuh untuk menggambar tanda tangan di atas.</p>
                        </div>

                        {{-- Mode 2: File Upload --}}
                        <div x-show="signatureModeEdit === 'file'" class="mt-1">
                            <label class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-slate-200 hover:border-emerald-400 rounded-xl cursor-pointer bg-slate-50 hover:bg-emerald-50/20 transition group">
                                <template x-if="!filePreviewUrlEdit">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-2 text-slate-400 group-hover:text-emerald-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <p class="text-xs font-bold text-slate-600 group-hover:text-emerald-700">Klik untuk unggah file gambar TTD</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">PNG, JPG, JPEG, WEBP (Maks. 2MB)</p>
                                    </div>
                                </template>
                                <template x-if="filePreviewUrlEdit">
                                    <div class="relative w-full h-full flex items-center justify-center p-2">
                                        <img :src="filePreviewUrlEdit" class="max-h-full max-w-full object-contain" alt="Preview File Upload">
                                        <span class="absolute bottom-2 right-2 px-2 py-0.5 bg-emerald-600 text-white text-[10px] font-bold rounded">Terpilih</span>
                                    </div>
                                </template>
                                <input type="file" name="signature_file" accept="image/*" @change="handleFileUpload($event, 'edit')" class="hidden" />
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100 flex justify-end gap-3">
                    <x-secondary-button @click="$dispatch('close')">Batal</x-secondary-button>
                    <x-primary-button class="bg-emerald-600 hover:bg-emerald-700 gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Perubahan
                    </x-primary-button>
                </div>
            </form>
        </x-modal>

        {{-- ====== MODAL PREVIEW TANDA TANGAN ====== --}}
        <x-modal name="modal-preview-signature" :show="false" focusable class="!z-[60]">
            <div class="p-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-emerald-100 rounded-xl text-emerald-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900 leading-none" x-text="previewData.name"></h3>
                            <p class="text-xs text-slate-400 font-semibold mt-1">
                                NIM: <span x-text="previewData.nim || '—'"></span> • Peran: <span class="capitalize" x-text="previewData.role"></span>
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="$dispatch('close')" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                {{-- Signature Display Box --}}
                <div class="flex flex-col items-center justify-center p-6 bg-slate-50 border border-slate-200 rounded-2xl relative min-h-[200px]">
                    <template x-if="previewData.signature_url">
                        <img :src="previewData.signature_url" class="max-h-52 max-w-full object-contain filter drop-shadow bg-white p-3 rounded-xl border border-slate-200" alt="Tanda Tangan">
                    </template>
                    <template x-if="!previewData.signature_url">
                        <div class="text-center text-slate-400 py-8">
                            <svg class="w-12 h-12 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <p class="text-sm font-semibold">Tanda tangan belum diunggah.</p>
                        </div>
                    </template>
                </div>

                {{-- Actions --}}
                <div class="mt-6 pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                    <template x-if="previewData.signature_url">
                        <button type="button" @click="deleteSignatureDirect(previewData)"
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs rounded-xl border border-rose-200 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Hapus Tanda Tangan
                        </button>
                    </template>
                    <div class="flex items-center gap-3 ml-auto">
                        <x-secondary-button @click="$dispatch('close')">Tutup</x-secondary-button>
                        <button type="button" @click="$dispatch('close'); openEditModal(previewData);"
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Edit Tanda Tangan
                        </button>
                    </div>
                </div>
            </div>
        </x-modal>

    </div>{{-- end x-data --}}

    <script>
        function userIndex() {
            const allUsers = @json($users);

            return {
                allUsers,
                search: '',
                filterRole: '',
                sortCol: 'name',
                sortDir: 'asc',
                perPage: 10,
                currentPage: 1,
                editData: { id: '', name: '', email: '', nim: '', phone: '', role: 'anggota', divisi: '', class: '', signature_url: '' },
                previewData: { id: '', name: '', nim: '', role: '', signature_url: '' },
                removeSignature: false,
                signatureModeAdd: 'draw',
                signatureModeEdit: 'draw',
                filePreviewUrlAdd: null,
                filePreviewUrlEdit: null,

                get filteredUsers() {
                    let data = this.allUsers.filter(u => {
                        const q = this.search.toLowerCase();
                        const matchSearch = !q ||
                            (u.name && u.name.toLowerCase().includes(q)) ||
                            (u.nim  && u.nim.toLowerCase().includes(q))  ||
                            (u.email && u.email.toLowerCase().includes(q)) ||
                            (u.phone && u.phone.toLowerCase().includes(q)) ||
                            (u.divisi && u.divisi.toLowerCase().includes(q)) ||
                            (u.class && u.class.toLowerCase().includes(q));
                        const matchRole = !this.filterRole || u.role === this.filterRole;
                        return matchSearch && matchRole;
                    });

                    data.sort((a, b) => {
                        const va = (a[this.sortCol] || '').toString().toLowerCase();
                        const vb = (b[this.sortCol] || '').toString().toLowerCase();
                        if (va < vb) return this.sortDir === 'asc' ? -1 : 1;
                        if (va > vb) return this.sortDir === 'asc' ? 1 : -1;
                        return 0;
                    });

                    return data;
                },

                get totalPages() {
                    return Math.max(1, Math.ceil(this.filteredUsers.length / this.perPage));
                },

                get pagesArray() {
                    const pages = [];
                    const max = Math.min(this.totalPages, 7);
                    let start = Math.max(1, this.currentPage - 3);
                    let end   = Math.min(this.totalPages, start + max - 1);
                    start = Math.max(1, end - max + 1);
                    for (let i = start; i <= end; i++) pages.push(i);
                    return pages;
                },

                get paginatedUsers() {
                    const start = (this.currentPage - 1) * this.perPage;
                    return this.filteredUsers.slice(start, start + this.perPage);
                },

                sortBy(col) {
                    if (this.sortCol === col) {
                        this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
                    } else {
                        this.sortCol = col;
                        this.sortDir = 'asc';
                    }
                    this.currentPage = 1;
                },

                paginationInfo() {
                    const total = this.filteredUsers.length;
                    if (total === 0) return 'Tidak ada data yang ditemukan';
                    const from = (this.currentPage - 1) * this.perPage + 1;
                    const to   = Math.min(this.currentPage * this.perPage, total);
                    return `Menampilkan ${from}–${to} dari ${total} data`;
                },

                openPreviewModal(u) {
                    this.previewData = { ...u };
                    this.$dispatch('open-modal', 'modal-preview-signature');
                },

                openEditModal(data) {
                    this.editData = { ...data };
                    this.removeSignature = false;
                    this.signatureModeEdit = 'draw';
                    this.filePreviewUrlEdit = null;
                    this.$dispatch('open-modal', 'modal-edit-user');
                    document.getElementById('signatureInputEdit').value = '';
                    const canvas = document.getElementById('signatureCanvasEdit');
                    if (canvas) {
                        const ctx = canvas.getContext('2d');
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                    }
                },

                handleFileUpload(event, target) {
                    const file = event.target.files[0];
                    if (file) {
                        const url = URL.createObjectURL(file);
                        if (target === 'add') {
                            this.filePreviewUrlAdd = url;
                        } else {
                            this.filePreviewUrlEdit = url;
                            this.removeSignature = false;
                        }
                    }
                },

                deleteSignatureDirect(u) {
                    if (confirm(`Hapus tanda tangan milik "${u.name}"?\nTindakan ini tidak dapat dibatalkan.`)) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/koordinator/users/${u.id}`;

                        const csrf = document.createElement('input');
                        csrf.type = 'hidden';
                        csrf.name = '_token';
                        csrf.value = '{{ csrf_token() }}';
                        form.appendChild(csrf);

                        const method = document.createElement('input');
                        method.type = 'hidden';
                        method.name = '_method';
                        method.value = 'PUT';
                        form.appendChild(method);

                        const name = document.createElement('input');
                        name.type = 'hidden';
                        name.name = 'name';
                        name.value = u.name;
                        form.appendChild(name);

                        const email = document.createElement('input');
                        email.type = 'hidden';
                        email.name = 'email';
                        email.value = u.email;
                        form.appendChild(email);

                        const role = document.createElement('input');
                        role.type = 'hidden';
                        role.name = 'role';
                        role.value = u.role;
                        form.appendChild(role);

                        const removeSig = document.createElement('input');
                        removeSig.type = 'hidden';
                        removeSig.name = 'remove_signature';
                        removeSig.value = '1';
                        form.appendChild(removeSig);

                        document.body.appendChild(form);
                        form.submit();
                    }
                },

                confirmDelete(u) {
                    if (confirm(`Hapus anggota "${u.name}"?\nTindakan ini tidak dapat dibatalkan.`)) {
                        const form = document.getElementById('deleteForm');
                        form.action = `/koordinator/users/${u.id}`;
                        form.submit();
                    }
                },

                avatarColor(name) {
                    if (!name) return '#94a3b8';
                    const colors = [
                        '#10b981','#3b82f6','#8b5cf6','#f59e0b',
                        '#ef4444','#06b6d4','#ec4899','#6366f1'
                    ];
                    let hash = 0;
                    for (let i = 0; i < name.length; i++) hash = name.charCodeAt(i) + ((hash << 5) - hash);
                    return colors[Math.abs(hash) % colors.length];
                }
            };
        }

        document.addEventListener('DOMContentLoaded', () => {
            initSignaturePad('signatureCanvasAdd', 'signatureInputAdd', 'clearSigBtnAdd');
            initSignaturePad('signatureCanvasEdit', 'signatureInputEdit', 'clearSigBtnEdit');
        });

        function initSignaturePad(canvasId, inputId, clearBtnId) {
            const canvas = document.getElementById(canvasId);
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            const input = document.getElementById(inputId);
            const clearBtn = document.getElementById(clearBtnId);

            function resizeCanvas() {
                const rect = canvas.getBoundingClientRect();
                if (rect.width > 0 && rect.height > 0) {
                    const tempCanvas = document.createElement('canvas');
                    tempCanvas.width = canvas.width;
                    tempCanvas.height = canvas.height;
                    const tempCtx = tempCanvas.getContext('2d');
                    tempCtx.drawImage(canvas, 0, 0);

                    canvas.width = rect.width;
                    canvas.height = rect.height;

                    ctx.strokeStyle = '#1e293b';
                    ctx.lineWidth = 3;
                    ctx.lineCap = 'round';
                    ctx.lineJoin = 'round';

                    ctx.drawImage(tempCanvas, 0, 0);
                }
            }

            const resizeObserver = new ResizeObserver((entries) => {
                for (let entry of entries) {
                    if (entry.contentRect.width > 0 && entry.contentRect.height > 0) {
                        resizeCanvas();
                    }
                }
            });
            resizeObserver.observe(canvas.parentElement);

            window.addEventListener('resize', resizeCanvas);

            let drawing = false;
            let lastX = 0;
            let lastY = 0;

            function getPos(e) {
                const rect = canvas.getBoundingClientRect();
                let clientX = e.clientX;
                let clientY = e.clientY;
                if (e.touches && e.touches.length > 0) {
                    clientX = e.touches[0].clientX;
                    clientY = e.touches[0].clientY;
                }
                return {
                    x: clientX - rect.left,
                    y: clientY - rect.top
                };
            }

            function startDrawing(e) {
                drawing = true;
                const pos = getPos(e);
                lastX = pos.x;
                lastY = pos.y;
                e.preventDefault();
            }

            function draw(e) {
                if (!drawing) return;
                const pos = getPos(e);
                ctx.beginPath();
                ctx.moveTo(lastX, lastY);
                ctx.lineTo(pos.x, pos.y);
                ctx.stroke();
                lastX = pos.x;
                lastY = pos.y;
                e.preventDefault();
            }

            function stopDrawing() {
                if (!drawing) return;
                drawing = false;
                input.value = canvas.toDataURL('image/png');
            }

            canvas.addEventListener('mousedown', startDrawing);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseup', stopDrawing);
            canvas.addEventListener('mouseout', stopDrawing);

            canvas.addEventListener('touchstart', startDrawing, { passive: false });
            canvas.addEventListener('touchmove', draw, { passive: false });
            canvas.addEventListener('touchend', stopDrawing);

            clearBtn.addEventListener('click', () => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                input.value = '';
            });
        }

        function resetAddSignature() {
            document.getElementById('signatureInputAdd').value = '';
            const canvas = document.getElementById('signatureCanvasAdd');
            if (canvas) {
                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);
            }
        }
    </script>
</x-app-layout>
