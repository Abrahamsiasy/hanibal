<!DOCTYPE html>
<html lang="en" class="bg-[#0d0d0d]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0d0d0d] text-white min-h-screen font-sans antialiased flex flex-col">

    <div class="flex-1 flex flex-col items-center justify-center px-4 py-12">
        <a href="{{ route('home') }}" class="font-display font-black text-3xl uppercase tracking-widest text-white mb-10">
            {{ config('app.name') }}
        </a>

        @if (session('success'))
            <div class="w-full max-w-sm mb-4 bg-green-900/50 border border-green-700 text-green-300 rounded px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="w-full max-w-sm mb-4 bg-red-900/50 border border-red-700 text-red-300 rounded px-4 py-3 text-sm">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>

</body>
</html>
