<x-guest-layout>
    @php
        $errorMsg = '';
        if ($errors->has('login')) {
            $errorMsg = 'NIM atau Email yang Anda masukkan salah / tidak terdaftar.';
        } elseif ($errors->has('password')) {
            $errorMsg = 'Password yang Anda masukkan salah. Silakan coba lagi.';
        } elseif ($errors->any()) {
            $errorMsg = 'Login gagal. Silakan lengkapi data Anda dengan benar.';
        }
    @endphp

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div x-data="{
        showErrorModal: {{ $errors->any() ? 'true' : 'false' }},
        errorMessage: '{{ $errorMsg }}',
        loginUser: '{{ old('login') }}',
        loginPass: '',
        validateSubmit(e) {
            if (!this.loginUser) {
                e.preventDefault();
                this.errorMessage = 'NIM / Email wajib diisi sebelum masuk!';
                this.showErrorModal = true;
                return;
            }
            if (!this.loginPass) {
                e.preventDefault();
                this.errorMessage = 'Password wajib diisi sebelum masuk!';
                this.showErrorModal = true;
                return;
            }
        }
    }">
        <form method="POST" action="{{ route('login') }}" @submit="validateSubmit($event)">
            @csrf

            <!-- NIM / Email -->
            <div>
                <x-input-label for="login" :value="__('NIM / Email')" />
                <input id="login" type="text" name="login" x-model="loginUser" autofocus autocomplete="username" style="width: 100%; border: 1px solid #e2e8f0; border-radius: 12px; padding: 10px 14px; font-size: 12px; color: #334155; background-color: #ffffff; outline: none; margin-top: 4px;" class="focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
            </div>

            <!-- Password -->
            <div class="mt-4" x-data="{ showPass: false }">
                <x-input-label for="password" :value="__('Password')" />

                <div style="position: relative; margin-top: 4px;">
                    <input :type="showPass ? 'text' : 'password'" id="password" name="password" x-model="loginPass" autocomplete="current-password" style="width: 100%; border: 1px solid #e2e8f0; border-radius: 12px; padding: 10px 40px 10px 14px; font-size: 12px; color: #334155; background-color: #ffffff; outline: none;" class="focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    
                    <!-- Eye Toggle Icon Button (Guaranteed to be inside the input box using absolute styles) -->
                    <button type="button" @click="showPass = !showPass" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); border: none; background: transparent; cursor: pointer; color: #94a3b8; outline: none; display: flex; align-items: center; justify-content: center; padding: 4px;">
                        <!-- Eye Open SVG -->
                        <svg x-show="!showPass" style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <!-- Eye Closed SVG -->
                        <svg x-show="showPass" style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.074m3.005-3.005A9.96 9.96 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-1.563 3.074m-4.5-4.5a3 3 0 11-4.243-4.243M9 9l6 6"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox"
                        class="rounded border-slate-300 text-emerald-600 shadow-sm focus:ring-emerald-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex flex-col gap-4 mt-6">
                <button type="submit" style="background-color: #059669; width: 100%; border-radius: 12px; padding: 10.5px; color: white; font-weight: bold; font-size: 12px; transition: background-color 0.2s;" class="hover:bg-emerald-700 shadow-sm">
                    {{ __('Log in') }}
                </button>

                @if (Route::has('password.request'))
                    <a class="text-sm text-slate-500 hover:text-slate-800 text-center focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 rounded-md transition"
                        href="{{ route('password.request') }}">
                        {{ __('Lupa password?') }}
                    </a>
                @endif
            </div>
        </form>

        <!-- Beautiful Floating Modal Validation Alert (Alpine.js) - Compact Version -->
        <div x-show="showErrorModal" class="fixed inset-0 z-[9999] overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" @keydown.escape.window="showErrorModal = false">
            <div class="bg-white rounded-2xl max-w-[280px] w-full overflow-hidden shadow-2xl border border-slate-150 p-4 space-y-3" @click.away="showErrorModal = false" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                <div class="flex items-center gap-2.5 text-rose-600">
                    <span class="p-2 bg-rose-50 rounded-xl">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </span>
                    <h3 class="text-xs font-bold text-slate-800">Gagal Masuk</h3>
                </div>
                
                <p class="text-[10px] text-slate-500 font-semibold leading-relaxed" x-text="errorMessage"></p>
                
                <div class="flex justify-end pt-1">
                    <button type="button" @click="showErrorModal = false" style="background-color: #dc2626;" class="px-4 py-1.5 text-white rounded-xl font-bold text-[9px] transition shadow-sm hover:bg-rose-700">
                        Mengerti
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
