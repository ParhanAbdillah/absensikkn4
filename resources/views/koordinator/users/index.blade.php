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
                                   placeholder="Cari nama, NIM, email, divisi..."
                                   class="pl-9 pr-4 py-2 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none w-64 transition">
                        </div>

                        {{-- Filter Role --}}
                        <select x-model="filterRole" @change="currentPage = 1"
                                class="border border-slate-200 rounded-xl pl-3 pr-10 py-2 text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-white transition">
                            <option value="">Semua Peran</option>
                            <option value="anggota">Anggota</option>
                            <option value="koordinator">Koordinator</option>
                            <option value="dpl">DPL</option>
                        </select>
                    </div>

                    {{-- Right: Tambah Anggota button --}}
                    <button @click="$dispatch('open-modal', 'modal-tambah-user')"
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
                                                  'bg-blue-100 text-blue-700 border border-blue-200': u.role === 'dpl',
                                                  'bg-slate-100 text-slate-600 border border-slate-200': u.role === 'anggota'
                                              }"
                                              x-text="u.role">
                                        </span>
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
                                    <td colspan="7" class="py-16 text-center">
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
            <form action="{{ route('koordinator.users.store') }}" method="POST" class="p-6">
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
                                <option value="koordinator">Koordinator</option>
                                <option value="dpl">DPL (Dosen)</option>
                            </select>
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
            <form :action="'/koordinator/users/' + editData.id" method="POST" class="p-6">
                @csrf
                @method('PUT')
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
                                <option value="koordinator">Koordinator</option>
                                <option value="dpl">DPL (Dosen)</option>
                            </select>
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
                editData: { id: '', name: '', email: '', nim: '', phone: '', role: 'anggota', divisi: '' },

                get filteredUsers() {
                    let data = this.allUsers.filter(u => {
                        const q = this.search.toLowerCase();
                        const matchSearch = !q ||
                            (u.name && u.name.toLowerCase().includes(q)) ||
                            (u.nim  && u.nim.toLowerCase().includes(q))  ||
                            (u.email && u.email.toLowerCase().includes(q)) ||
                            (u.phone && u.phone.toLowerCase().includes(q)) ||
                            (u.divisi && u.divisi.toLowerCase().includes(q));
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

                openEditModal(data) {
                    this.editData = { ...data };
                    this.$dispatch('open-modal', 'modal-edit-user');
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
    </script>
</x-app-layout>
