@extends('layouts.app', ['title' => 'Остановки'])

@section('content')
<div class="toolbar">
    <h1 class="section-title" style="margin:0;">Остановки</h1>
    @if(auth()->user()->isAdmin())
        <a class="btn" href="{{ route('stations.create') }}">Добавить остановку</a>
    @endif
</div>
<div class="table-wrap">
<table>
    <thead>
        <tr>
            <th>ID</th><th>Название</th><th>Позиция</th><th>Маршрут</th>
            @if(auth()->user()->isAdmin())<th>Действия</th>@endif
        </tr>
    </thead>
    <tbody>
    @forelse($stations as $station)
        <tr>
            <td>{{ $station->id }}</td>
            <td>{{ $station->name }}</td>
            <td>{{ $station->position_station ?: '—' }}</td>
            <td>{{ $station->line?->code ?: 'Не привязана' }}</td>
            @if(auth()->user()->isAdmin())
            <td class="actions">
                <a href="{{ route('stations.edit', $station) }}">Редактировать</a>
                <form method="POST" action="{{ route('stations.destroy', $station) }}">@csrf @method('DELETE')<button class="danger" onclick="return confirm('Удалить остановку?')">Удалить</button></form>
            </td>
            @endif
        </tr>
    @empty
        <tr><td colspan="5">Нет данных.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
{{ $stations->links() }}
@endsection
