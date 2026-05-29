@extends('layouts.app', ['title' => 'Водители'])

@section('content')
<div class="toolbar">
    <h1 class="section-title" style="margin:0;">Водители</h1>
    @if(auth()->user()->isAdmin())
        <a class="btn" href="{{ route('drivers.create') }}">Добавить водителя</a>
    @endif
</div>
<div class="table-wrap">
<table>
    <thead>
        <tr>
            <th>ID</th><th>ФИО</th><th>Дата рождения</th><th>Email</th><th>Телефон</th><th>Транспорт</th>
            @if(auth()->user()->isAdmin())<th>Действия</th>@endif
        </tr>
    </thead>
    <tbody>
    @forelse($drivers as $driver)
        <tr>
            <td>{{ $driver->id }}</td>
            <td>{{ $driver->name }}</td>
            <td>{{ $driver->birth_date }}</td>
            <td>{{ $driver->email }}</td>
            <td>{{ $driver->phone }}</td>
            <td>{{ $driver->vehicle?->name ?: 'Не назначен' }}</td>
            @if(auth()->user()->isAdmin())
            <td class="actions">
                <a href="{{ route('drivers.edit', $driver) }}">Редактировать</a>
                <form method="POST" action="{{ route('drivers.destroy', $driver) }}">@csrf @method('DELETE')<button class="danger" onclick="return confirm('Удалить водителя?')">Удалить</button></form>
            </td>
            @endif
        </tr>
    @empty
        <tr><td colspan="7">Нет данных.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
{{ $drivers->links() }}
@endsection
