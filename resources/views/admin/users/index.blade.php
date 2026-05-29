@extends('layouts.app', ['title' => 'Пользователи'])

@section('content')
<div class="toolbar">
    <h1 class="section-title" style="margin:0;">Пользователи</h1>
    <a class="btn" href="{{ route('users.create') }}">Добавить пользователя</a>
</div>
<div class="table-wrap">
<table>
    <thead>
        <tr>
            <th>ID</th><th>Имя</th><th>Логин</th><th>Email</th><th>Роль</th><th>Действия</th>
        </tr>
    </thead>
    <tbody>
    @forelse($users as $user)
        <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->login }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->role }}</td>
            <td class="actions">
                <a href="{{ route('users.edit', $user) }}">Редактировать</a>
                @if($user->login !== 'admin')
                    <form method="POST" action="{{ route('users.destroy', $user) }}">@csrf @method('DELETE')<button class="danger" onclick="return confirm('Удалить пользователя?')">Удалить</button></form>
                @endif
            </td>
        </tr>
    @empty
        <tr><td colspan="6">Нет данных.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
{{ $users->links() }}
@endsection
