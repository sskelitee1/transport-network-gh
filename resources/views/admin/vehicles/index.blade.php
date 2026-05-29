@extends('layouts.app', ['title' => 'Транспорт'])

@section('content')
<div class="toolbar">
    <h1 class="section-title" style="margin:0;">Транспорт</h1>
    @if(auth()->user()->isAdmin())
        <a class="btn" href="{{ route('vehicles.create') }}">Добавить транспорт</a>
    @endif
</div>
<div class="table-wrap">
<table>
    <thead>
        <tr>
            <th>ID</th><th>Название</th><th>Вместимость</th><th>Тип</th><th>Маршрут</th><th>Водитель</th>
            @if(auth()->user()->isAdmin())<th>Действия</th>@endif
        </tr>
    </thead>
    <tbody>
    @forelse($vehicles as $vehicle)
        <tr>
            <td>{{ $vehicle->id }}</td>
            <td>{{ $vehicle->name }}</td>
            <td>{{ $vehicle->capacity }}</td>
            <td>{{ $vehicle->type }}</td>
            <td>{{ $vehicle->line?->code ?: 'Не привязан' }}</td>
            <td>{{ $vehicle->driver?->name ?: 'Не назначен' }}</td>
            @if(auth()->user()->isAdmin())
            <td class="actions">
                <a href="{{ route('vehicles.edit', $vehicle) }}">Редактировать</a>
                <form method="POST" action="{{ route('vehicles.destroy', $vehicle) }}">@csrf @method('DELETE')<button class="danger" onclick="return confirm('Удалить транспорт?')">Удалить</button></form>
            </td>
            @endif
        </tr>
    @empty
        <tr><td colspan="7">Нет данных.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
{{ $vehicles->links() }}
@endsection
