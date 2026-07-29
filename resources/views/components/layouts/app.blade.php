<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#4F46E5">
    <title>{{ $title ?? 'Rota — Gestão de Necessidades &amp; Satisfação' }}</title>

    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">

    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>

<body>

    <a href="#conteudo-principal" class="skip-link">Pular para o conteúdo principal</a>

    <div class="app-container">
        <!-- Menu Superior Fixo (Mobile e Desktop) -->
        <header class="top-header">
            <div class="top-header-inner">
                <a href="{{ route('welcome') }}" class="brand-badge" aria-label="Rota Home">
                    <div class="brand-icon">R</div>
                    <div>
                        <span class="brand-text">Rota</span>
                        <span class="brand-tag ml-2">SERPRO</span>
                    </div>
                </a>

                <!-- Navegação Superior Horizontal -->
                <nav class="top-nav" aria-label="Navegação principal">
                    <a href="{{ route('necessidade.nova') }}" class="nav-pill @if(request()->routeIs('necessidade.nova')) active @endif">
                        <span>Necessidade</span>
                    </a>
                    @if (auth()->user()?->isGestorOuAdmin())
                        <a href="{{ route('gestor.dashboard') }}" class="nav-pill @if(request()->routeIs('gestor.dashboard')) active @endif">
                            <span>Dashboard</span>
                        </a>
                    @endif
                    <a href="{{ route('mural.votacao') }}" class="nav-pill @if(request()->routeIs('mural.votacao')) active @endif">
                        <span>Votação</span>
                    </a>
                    <a href="{{ route('pesquisa.satisfacao') }}" class="nav-pill @if(request()->routeIs('pesquisa.satisfacao')) active @endif">
                        <span>Satisfação</span>
                    </a>
                    @if (auth()->user()?->isAdmin())
                        <a href="{{ route('ranking') }}" class="nav-pill @if(request()->routeIs('ranking')) active @endif">
                            <span>Ranking</span>
                        </a>
                    @endif
                </nav>

                @auth
                    <flux:dropdown align="end">
                        <button type="button" class="flex flex-col items-end justify-center text-xs text-slate-700 font-semibold whitespace-nowrap focus:outline-none group cursor-pointer">
                            <div class="flex items-center gap-1">
                                <span class="text-xs text-slate-900 font-bold leading-tight group-hover:text-indigo-600 transition-colors">{{ auth()->user()->name }}</span>
                                <flux:icon name="chevron-down" class="w-3 h-3 text-slate-400 group-hover:text-indigo-600 transition-colors" />
                            </div>
                            <flux:badge :color="auth()->user()->tip_role?->color() ?? 'indigo'" size="sm" class="mt-0.5">
                                {{ auth()->user()->tip_role?->label() ?? 'Usuário' }}
                            </flux:badge>
                        </button>

                        <flux:menu>
                            <flux:menu.item icon="user" :href="route('profile')" wire:navigate>Perfil</flux:menu.item>
                            <flux:menu.separator />
                            <form method="POST" action="{{ route('logout') }}" id="logout-form-header">
                                @csrf
                                <flux:menu.item icon="arrow-right-start-on-rectangle" onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();">
                                    Sair
                                </flux:menu.item>
                            </form>
                        </flux:menu>
                    </flux:dropdown>
                @else
                    <a href="{{ route('login') }}" class="nav-pill">
                        <span>Acesse</span>
                    </a>
                @endauth
            </div>
        </header>

        <!-- Conteúdo Principal -->
        <main id="conteudo-principal" class="main-content">
            {{ $slot }}
        </main>
    </div>

    @fluxScripts
</body>

</html>
