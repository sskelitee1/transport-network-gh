@extends('layouts.app', ['title' => 'Маршруты'])

@section('content')
<div class="toolbar">
    <h1 class="section-title" style="margin:0;">Маршруты</h1>
    @if(auth()->user()->isAdmin())
        <a class="btn" href="{{ route('lines.create') }}">Добавить маршрут</a>
    @endif
</div>
<div class="table-wrap">
<table>
    <thead>
        <tr>
            <th>ID</th><th>Код</th><th>Время</th><th>Тип</th><th>Карта</th><th>Остановки</th><th>Транспорт</th>
            @if(auth()->user()->isAdmin())<th>Действия</th>@endif
        </tr>
    </thead>
    <tbody>
    @forelse($lines as $line)
        <tr>
            <td>{{ $line->id }}</td>
            <td>{{ $line->code }}</td>
            <td>{{ $line->start_time_operation }} - {{ $line->end_time_operation }}</td>
            <td>{{ $line->type }}</td>
            <td>{{ $line->map ?: '—' }}</td>
            <td>{{ $line->stations_count }}</td>
            <td>{{ $line->vehicles_count }}</td>
            @if(auth()->user()->isAdmin())
            <td class="actions">
                <a href="{{ route('lines.edit', $line) }}">Редактировать</a>
                <form method="POST" action="{{ route('lines.destroy', $line) }}">@csrf @method('DELETE')<button class="danger" onclick="return confirm('Удалить маршрут?')">Удалить</button></form>
            </td>
            @endif
        </tr>
    @empty
        <tr><td colspan="8">Нет данных.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
{{ $lines->links() }}
@endsection
