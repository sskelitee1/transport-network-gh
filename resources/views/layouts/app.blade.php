<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Городская транспортная сеть' }}</title>
    <style>
        :root {
            --blue: #0a3b84;
            --blue-dark: #06295c;
            --text: #253041;
            --danger: #b42318;
            --success: #1b7f3c;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, Helvetica, sans-serif; background: #d9d8d6; color: var(--text); }
        a { color: var(--blue); text-decoration: none; }
        .top-wave { background: url('{{ asset('assets/images/base-17.jpg') }}') repeat-x top center; height: 69px; }
        .site-header { background: #ececec; border-bottom: 4px solid var(--blue-dark); }
        .header-inner {
            max-width: 1180px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between;
            padding: 16px 20px; gap: 16px;
        }
        .logo-box { display: flex; align-items: center; gap: 16px; }
        .logo-box img { max-height: 54px; }
        .logo-title { font-size: 22px; font-weight: 700; color: var(--blue); text-transform: uppercase; }
        .auth-box { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
        .role-badge { display: inline-block; padding: 4px 10px; border-radius: 999px; background: #e8eef8; color: var(--blue-dark); font-size: 12px; font-weight: 700; text-transform: uppercase; }
        .logout-form button, .btn {
            background: linear-gradient(#f4f17e, #f0d317); border: 1px solid #c9b72f; border-radius: 6px;
            padding: 10px 16px; color: var(--blue-dark); font-weight: bold; cursor: pointer;
        }
        .btn-secondary { background: linear-gradient(#d2def4, #8ea9dd); border-color: #6582bf; }
        .page { max-width: 1180px; margin: 0 auto; display: grid; grid-template-columns: 220px 1fr; gap: 24px; padding: 20px; }
        .sidebar { background: #f0f0ef; border: 1px solid #bdb7af; box-shadow: 0 0 8px rgba(0,0,0,.15); padding: 14px; align-self: start; }
        .sidebar h3 { margin: 0 0 12px; background: #84817d; color: #fff; padding: 8px 10px; border-left: 5px solid var(--blue); font-size: 14px; }
        .menu { list-style: none; padding: 0; margin: 0; }
        .menu li + li { margin-top: 8px; }
        .menu a { display: block; padding: 10px 12px; background: #fff; border: 1px solid #d3d1cc; font-weight: bold; }
        .menu a:hover, .menu a.active { background: var(--blue); color: #fff; }
        .content { background: #fff; border: 1px solid #bdb7af; box-shadow: 0 0 8px rgba(0,0,0,.15); padding: 24px; min-height: 640px; }
        .hero-banner { width: 100%; border: 1px solid #bdb7af; margin-bottom: 20px; }
        .section-title { margin: 0 0 18px; color: var(--blue); font-size: 28px; }
        .toolbar { display: flex; justify-content: space-between; gap: 16px; align-items: center; margin-bottom: 16px; }
        .grid-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 22px; }
        .card { background: linear-gradient(180deg, #f8f9fb 0%, #edf1f8 100%); border: 1px solid #d6dce8; border-radius: 10px; padding: 18px; }
        .card strong { display: block; font-size: 34px; color: var(--blue-dark); }
        .card span { color: #5d697c; font-size: 14px; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #d8d8d8; padding: 12px 10px; text-align: left; vertical-align: top; }
        th { background: #f1f4f9; color: var(--blue-dark); }
        .actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .actions a, .actions button { padding: 7px 10px; border: 1px solid #c8d2e5; background: #f7f9fd; color: var(--blue-dark); border-radius: 6px; cursor: pointer; }
        .actions .danger { color: #fff; background: var(--danger); border-color: var(--danger); }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        .field { display: flex; flex-direction: column; gap: 6px; }
        .field.full { grid-column: 1 / -1; }
        label { font-weight: bold; color: var(--blue-dark); }
        input, select, textarea { width: 100%; padding: 11px 12px; border: 1px solid #bfc8d6; border-radius: 6px; background: #fff; }
        textarea { min-height: 110px; resize: vertical; }
        .hint, .text-muted { color: #6d7784; font-size: 13px; }
        .alert { padding: 12px 14px; border-radius: 6px; margin-bottom: 16px; font-weight: bold; }
        .alert-success { background: #e7f6eb; color: var(--success); }
        .alert-error { background: #fdecea; color: var(--danger); }
        .error-text { color: var(--danger); font-size: 13px; }
        .avatar-preview { width: 64px; height: 64px; object-fit: cover; border-radius: 8px; border: 1px solid #d0d0d0; }
        .footer { margin-top: 26px; background: #d9d8d6 url('{{ asset('assets/images/footer-20.jpg') }}') no-repeat center top; background-size: 100% auto; height: 42px; border-top: 1px solid #bdb7af; }
        nav[role='navigation'] { margin-top: 20px; }
        @media (max-width: 900px) {
            .page { grid-template-columns: 1fr; }
            .form-grid { grid-template-columns: 1fr; }
            .header-inner { flex-direction: column; align-items: flex-start; }
            .footer { height: 28px; background-size: cover; }
        }
    </style>
</head>
<body>
    <div class="top-wave"></div>
    <header class="site-header">
        <div class="header-inner">
            <div class="logo-box">
                <img src="{{ asset('assets/images/logo-3.jpg') }}" alt="Логотип">
                <div class="logo-title">Городская транспортная сеть</div>
            </div>
            @auth
                <div class="auth-box">
                    <div><strong>{{ auth()->user()->isAdmin() ? 'Админ' : 'Пользователь' }}:</strong> {{ auth()->user()->name }}</div>
                    <span class="role-badge">{{ auth()->user()->role }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="logout-form">
                        @csrf
                        <button type="submit">Выход</button>
                    </form>
                </div>
            @endauth
        </div>
    </header>

    @auth
    <div class="page">
        <aside class="sidebar">
            <h3>{{ auth()->user()->isAdmin() ? 'Управление' : 'Просмотр данных' }}</h3>
            <ul class="menu">
                <li><a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Главная</a></li>
                <li><a class="{{ request()->routeIs('lines.*') ? 'active' : '' }}" href="{{ route('lines.index') }}">Маршруты</a></li>
                <li><a class="{{ request()->routeIs('stations.*') ? 'active' : '' }}" href="{{ route('stations.index') }}">Остановки</a></li>
                <li><a class="{{ request()->routeIs('vehicles.*') ? 'active' : '' }}" href="{{ route('vehicles.index') }}">Транспорт</a></li>
                <li><a class="{{ request()->routeIs('drivers.*') ? 'active' : '' }}" href="{{ route('drivers.index') }}">Водители</a></li>
                @if(auth()->user()->isAdmin())
                    <li><a class="{{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">Пользователи</a></li>
                @endif
            </ul>
        </aside>
        <main class="content">
            <img class="hero-banner" src="{{ asset('assets/images/HEADER.jpg') }}" alt="Транспортная сеть">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif
            @yield('content')
        </main>
    </div>
    @else
        @yield('content')
    @endauth
    <div class="footer"></div>
</body>
</html>
