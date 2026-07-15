<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-800 tracking-tight">Ajukan Izin / Sakit</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 animate-card">
                <h3 class="text-lg font-bold text-slate-800 mb-1">Form Permohonan Ketidakhadiran</h3>
                <p class="text-xs text-slate-500 mb-6">Isi form berikut untuk mengajukan izin atau sakit. Koordinator akan mereview permohonan Anda.</p>

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                        <ul class="list-disc list-inside text-xs text-red-700 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('anggota.leave.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <!-- Jenis Ketidakhadiran -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Jenis Permohonan</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition {{ old('type') === 'izin' ? 'border-indigo-500 bg-indigo-50' : 'border-slate-200 hover:border-indigo-300' }}">
                                <input type="radio" name="type" value="izin" class="text-indigo-600" {{ old('type') === 'izin' ? 'checked' : '' }} required>
                                <div>
                                    <div class="font-bold text-slate-800 text-sm">📋 Izin</div>
                                    <div class="text-xs text-slate-500">Keperluan tertentu</div>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition {{ old('type') === 'sakit' ? 'border-rose-500 bg-rose-50' : 'border-slate-200 hover:border-rose-300' }}">
                                <input type="radio" name="type" value="sakit" class="text-rose-500" {{ old('type') === 'sakit' ? 'checked' : '' }}>
                                <div>
                                    <div class="font-bold text-slate-800 text-sm">🤒 Sakit</div>
                                    <div class="text-xs text-slate-500">Tidak sehat</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Tanggal -->
                    <div>
                        <label for="date" class="block text-sm font-bold text-slate-700 mb-2">Tanggal Tidak Hadir</label>
                        <input type="date" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}"
                               class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
                    </div>

                    <!-- Alasan -->
                    <div>
                        <label for="reason" class="block text-sm font-bold text-slate-700 mb-2">Alasan / Keterangan</label>
                        <textarea id="reason" name="reason" rows="4" placeholder="Jelaskan alasan ketidakhadiran Anda..."
                                  class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition resize-none">{{ old('reason') }}</textarea>
                    </div>

                    <!-- Lampiran -->
                    <div>
                        <label for="attachment" class="block text-sm font-bold text-slate-700 mb-2">
                            Lampiran Bukti <span class="text-slate-400 font-normal">(opsional)</span>
                        </label>
                        <input type="file" id="attachment" name="attachment" accept=".jpg,.jpeg,.png,.pdf"
                               class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-600 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition">
                        <p class="text-xs text-slate-400 mt-1">Format: JPG, PNG, atau PDF. Maks. 2 MB.</p>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('anggota.leave.index') }}" class="flex-1 text-center px-4 py-3 border border-slate-200 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 transition">
                            Batal
                        </a>
                        <button type="submit" class="flex-1 px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold uppercase tracking-widest transition shadow-md shadow-indigo-200 animate-button">
                            Ajukan Permohonan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
