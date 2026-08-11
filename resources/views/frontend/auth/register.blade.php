@extends('frontend.layouts.guest')

@section('title', 'Register')

@section('content')
    <div class="w-full max-w-sm">
        <div class="bg-[#1a1a1a] border border-[#2a2a2a] rounded-xl p-8">
            <h1 class="font-display font-black text-2xl uppercase tracking-wide text-white mb-1">Join the Fight</h1>
            <p class="text-zinc-500 text-sm mb-6">Create your account and start betting on live fight nights.</p>

            <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1.5">
                        Full Name
                    </label>
                    <input type="text" name="name" id="name"
                           value="{{ old('name') }}"
                           placeholder="Your name"
                           class="w-full bg-[#111] border @error('name') border-red-600 @else border-[#2a2a2a] @enderror text-white placeholder-zinc-600 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-red-600 transition-colors"
                           required>
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

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
                    <label for="city_id" class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1.5">
                        Your City
                    </label>
                    <select name="city_id" id="city_id"
                            class="w-full bg-[#111] border @error('city_id') border-red-600 @else border-[#2a2a2a] @enderror text-white rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-red-600 transition-colors appearance-none"
                            required>
                        <option value="">Select city</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}" @selected(old('city_id') == $city->id)>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('city_id')
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

                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1.5">
                        Confirm Password
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="w-full bg-[#111] border border-[#2a2a2a] text-white rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-red-600 transition-colors"
                           required>
                </div>

                <button type="submit"
                        class="w-full bg-red-600 hover:bg-red-500 text-white font-display font-black uppercase tracking-widest py-3 rounded-lg transition-colors mt-2">
                    Create Account
                </button>
            </form>

            <p class="text-center text-sm text-zinc-500 mt-5">
                Already have an account?
                <a href="{{ route('login') }}" class="text-red-500 hover:text-red-400 font-semibold transition-colors">
                    Login
                </a>
            </p>
        </div>
    </div>
@endsection
