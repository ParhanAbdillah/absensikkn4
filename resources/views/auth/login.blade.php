<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- NIM / Email -->
        <div>
            <x-input-label for="login" :value="__('NIM / Email')" />
            <x-text-input id="login" class="block mt-1 w-full" type="text" name="login" :value="old('login')" required
                autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('login')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
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
            <x-primary-button class="w-full justify-center">
                {{ __('Log in') }}
            </x-primary-button>

            @if (Route::has('password.request'))
                <a class="text-sm text-slate-500 hover:text-slate-800 text-center focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 rounded-md transition"
                    href="{{ route('password.request') }}">
                    {{ __('Lupa password?') }}
                </a>
            @endif
        </div>
    </form>
</x-guest-layout>
