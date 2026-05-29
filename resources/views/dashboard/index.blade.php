@extends('layouts.app', ['title' => 'Главная'])

@section('content')
<h1 class="section-title">Добро пожаловать в систему</h1>
@if(auth()->user()->isAdmin())
    <p class="text-muted">Панель администратора для управления маршрутами, остановками, транспортом, водителями и пользователями.</p>
@else
    <p class="text-muted">Панель пользователя для просмотра маршрутов, остановок, транспорта и водителей без доступа к изменению данных.</p>
@endif

<div class="grid-cards">
    <div class="card"><strong>{{ $stats['lines'] }}</strong><span>Маршрутов</span></div>
    <div class="card"><strong>{{ $stats['stations'] }}</strong><span>Остановок</span></div>
    <div class="card"><strong>{{ $stats['vehicles'] }}</strong><span>Транспортных средств</span></div>
    <div class="card"><strong>{{ $stats['drivers'] }}</strong><span>Водителей</span></div>
</div>

<div class="grid-cards" style="grid-template-columns:repeat(auto-fit,minmax(320px,1fr));">
    <div class="card">
        <h3 style="margin-top:0;color:#06295c;">Последние маршруты</h3>
        <ul>
            @forelse($latestLines as $line)
                <li>{{ $line->code }} — {{ $line->type }}</li>
            @empty
                <li>Пока нет данных.</li>
            @endforelse
        </ul>
    </div>
    <div class="card">
        <h3 style="margin-top:0;color:#06295c;">Последние водители</h3>
        <ul>
            @forelse($latestDrivers as $driver)
                <li>{{ $driver->name }} @if($driver->vehicle) — {{ $driver->vehicle->name }} @endif</li>
            @empty
                <li>Пока нет данных.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
