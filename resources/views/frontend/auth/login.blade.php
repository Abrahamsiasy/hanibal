@extends('frontend.layouts.guest')

@section('title', 'Login')

@section('content')
    <div class="w-full max-w-sm">
        <div class="bg-[#1a1a1a] border border-[#2a2a2a] rounded-xl p-8">
            <h1 class="font-display font-black text-2xl uppercase tracking-wide text-white mb-1">Welcome Back</h1>
            <p class="text-zinc-500 text-sm mb-6">Enter your phone number to continue betting.</p>

            <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1.5">
                        Phone Number
                    </label>
                    <input type="text" name="phone" id="phone"
                           value="{{ old('phone') }}"
                           placeholder="09XXXXXXXX"
                           class="w-full bg-[#111] border @error('phone') border-red-600 @else border-[#2a2a2a] @enderror text-white placeholder-zinc-600 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-red-600 transition-colors"
                           required>
                    @error('phone')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1.5">
                        Password
                    </label>
                    <input type="password" name="password" id="password"
                           class="w-full bg-[#111] border @error('password') border-red-600 @else border-[#2a2a2a] @enderror text-white rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-red-600 transition-colors"
                           required>
                    @error('password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember" value="1"
                           class="w-4 h-4 accent-red-600 rounded">
                    <label for="remember" class="text-xs text-zinc-400">Remember me</label>
                </div>

                <button type="submit"
                        class="w-full bg-red-600 hover:bg-red-500 text-white font-display font-black uppercase tracking-widest py-3 rounded-lg transition-colors mt-2">
                    Login
                </button>
            </form>

            <p class="text-center text-sm text-zinc-500 mt-5">
                No account?
                <a href="{{ route('register') }}" class="text-red-500 hover:text-red-400 font-semibold transition-colors">
                    Register now
                </a>
            </p>
        </div>
    </div>
@endsection
