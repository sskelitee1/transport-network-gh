@extends('layouts.app', ['title' => 'Вход'])

@section('content')
<div style="max-width:520px;margin:40px auto;background:#fff;border:1px solid #bdb7af;box-shadow:0 0 8px rgba(0,0,0,.15);padding:24px;">
    <img class="hero-banner" src="{{ asset('assets/images/HEADER.jpg') }}" alt="Транспортная сеть">
    <h1 class="section-title">Авторизация</h1>
    @if($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('login.attempt') }}">
        @csrf
        <div class="field" style="margin-bottom:14px;">
            <label for="login">Логин</label>
            <input id="login" name="login" value="{{ old('login') }}" required>
        </div>
        <div class="field" style="margin-bottom:14px;">
            <label for="password">Пароль</label>
            <input id="password" type="password" name="password" required>
        </div>
        <button class="btn" type="submit">Войти</button>
        <p class="hint" style="margin-top:14px;">Тестовый доступ: admin / admin12345</p>
    </form>
</div>
@endsection
