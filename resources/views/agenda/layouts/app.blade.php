<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Agenda Muncks - POLIFERRO')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <style>
        @media (max-width: 767px) {
            .touch-target { min-height: 44px; min-width: 44px; display: inline-flex; align-items: center; justify-content: center; }
            .nav-mobile { flex-wrap: wrap; gap: 0.25rem; }
            .alert-mobile { width: 100%; }
        }
        @media (max-width: 639px) {
            nav.bg-blue-900 { box-shadow: none; }
            nav.bg-blue-900 > .max-w-7xl > div {
                padding-top: 0.75rem;
                padding-bottom: 0.5rem;
                gap: 0.75rem;
            }
            nav.bg-blue-900 .nav-mobile {
                display: flex;
                gap: 0.625rem;
                justify-content: flex-start;
            }
            nav.bg-blue-900 .nav-mobile > a {
                padding: 0.5rem 0.75rem;
                background: rgba(255,255,255,0.08);
                border-radius: 0.375rem;
                text-align: center;
                flex: 1;
            }
            nav.bg-blue-900 .nav-mobile > form { margin-left: auto; }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">
    @auth
    <nav class="bg-blue-900 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 sm:gap-0 py-3 sm:py-0 sm:h-16">
                <div class="flex items-center gap-2 w-full sm:w-auto justify-between sm:justify-start">
                    <span class="text-lg sm:text-xl font-bold">🏗️ Agenda Muncks</span>
                    <span class="text-xs sm:text-sm bg-blue-700 px-2 py-1 rounded whitespace-nowrap">{{ Auth::user()->name }}</span>
                </div>
                <div class="flex items-center gap-2 sm:gap-4 w-full sm:w-auto justify-end nav-mobile">
                    <a href="/agenda/dashboard" class="hover:text-blue-200 text-xs sm:text-sm touch-target px-2 sm:px-0">Painel</a>
                    <a href="/agenda/calendario" class="hover:text-blue-200 text-xs sm:text-sm touch-target px-2 sm:px-0">Calendário</a>
                    <form method="POST" action="/agenda/logout">
                        @csrf
                        <button class="bg-red-600 hover:bg-red-700 px-3 py-1.5 rounded text-xs sm:text-sm touch-target">Sair</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    <main class="max-w-7xl mx-auto px-4 py-6">
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm">
            {{ session('success') }}
        </div>
        @endif

        @php
            $conflitoMsg = $errors->first('conflito');
        @endphp

        @if($errors->any())
            @if($conflitoMsg)
                @foreach($errors->all() as $error)
                    @if($error !== $conflitoMsg)
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                        <p>{{ $error }}</p>
                    </div>
                    @endif
                @endforeach
            @else
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif
        @endif

        @yield('content')
    </main>

    @if($conflitoMsg)
    <div x-data="{ show: true }" x-show="show" class="fixed inset-0 z-50 flex items-start sm:items-center justify-center bg-black bg-opacity-50 pt-10 sm:pt-0">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-2 sm:mx-4 p-4 sm:p-6 border-t-4 border-red-600">
            <div class="text-center">
                <div class="text-4xl sm:text-5xl mb-4">⚠️</div>
                <p class="text-red-800 font-bold text-sm sm:text-base leading-relaxed">{{ $conflitoMsg }}</p>
                <button @click="show = false" class="mt-6 bg-red-600 hover:bg-red-700 text-white px-6 py-3 sm:py-2.5 rounded text-sm font-semibold touch-target">
                    Ok
                </button>
            </div>
        </div>
    </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @stack('scripts')
</body>
</html>
