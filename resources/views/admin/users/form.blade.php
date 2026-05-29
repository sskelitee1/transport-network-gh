@extends('layouts.app', ['title' => $user->exists ? 'Редактировать пользователя' : 'Добавить пользователя'])

@section('content')
<h1 class="section-title">{{ $user->exists ? 'Редактировать пользователя' : 'Добавить пользователя' }}</h1>
<form method="POST" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}">
    @csrf
    @if($user->exists) @method('PUT') @endif
    <div class="form-grid">
        <div class="field"><label>Имя</label><input name="name" value="{{ old('name', $user->name) }}" required>@error('name')<div class="error-text">{{ $message }}</div>@enderror</div>
        <div class="field"><label>Пол</label><select name="gender"><option value="">Не указан</option><option value="M" @selected(old('gender', $user->gender)==='M')>M</option><option value="F" @selected(old('gender', $user->gender)==='F')>F</option></select>@error('gender')<div class="error-text">{{ $message }}</div>@enderror</div>
        <div class="field"><label>Дата рождения</label><input type="date" name="birth_date" value="{{ old('birth_date', optional($user->birth_date)->format('Y-m-d')) }}">@error('birth_date')<div class="error-text">{{ $message }}</div>@enderror</div>
        <div class="field"><label>Email</label><input type="email" name="email" value="{{ old('email', $user->email) }}" required>@error('email')<div class="error-text">{{ $message }}</div>@enderror</div>
        <div class="field"><label>Логин</label><input name="login" value="{{ old('login', $user->login) }}" required>@error('login')<div class="error-text">{{ $message }}</div>@enderror</div>
        <div class="field"><label>Пароль</label><input type="password" name="password" {{ $user->exists ? '' : 'required' }}>@error('password')<div class="error-text">{{ $message }}</div>@enderror</div>
        <div class="field"><label>Роль</label><select name="role" required><option value="admin" @selected(old('role', $user->role)==='admin')>admin</option><option value="user" @selected(old('role', $user->role ?? 'user')==='user')>user</option></select>@error('role')<div class="error-text">{{ $message }}</div>@enderror</div>
    </div>
    <div style="margin-top:18px;display:flex;gap:12px;"><button class="btn" type="submit">Сохранить</button><a class="btn btn-secondary" href="{{ route('users.index') }}">Назад</a></div>
</form>
@endsection
