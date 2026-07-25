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
                <div class="flex items-center justify-between">
                    <a href="{{ route('necessidade.nova') }}" class="brand-badge" aria-label="Rota Home">
                        <div class="brand-icon">R</div>
                        <div>
                            <span class="brand-text">Rota</span>
                            <span class="brand-tag ml-2">SERPRO</span>
                        </div>
                    </a>
                </div>

                <!-- Navegação Superior Horizontal -->
                <nav class="top-nav" aria-label="Navegação principal">
                    <a href="{{ route('necessidade.nova') }}" class="nav-pill @if(request()->routeIs('necessidade.nova')) active @endif">
                        <span>Nova necessidade</span>
                    </a>
                    <a href="{{ route('gestor.dashboard') }}" class="nav-pill @if(request()->routeIs('gestor.dashboard')) active @endif">
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('mural.votacao') }}" class="nav-pill @if(request()->routeIs('mural.votacao')) active @endif">
                        <span>Mural de votação</span>
                    </a>
                    <a href="{{ route('pesquisa.satisfacao') }}" class="nav-pill @if(request()->routeIs('pesquisa.satisfacao')) active @endif">
                        <span>Satisfação</span>
                    </a>
                    <a href="{{ route('ranking') }}" class="nav-pill @if(request()->routeIs('ranking')) active @endif">
                        <span>Ranking</span>
                    </a>
                </nav>
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
