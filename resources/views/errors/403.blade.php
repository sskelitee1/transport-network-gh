@extends('layouts.app', ['title' => 'Доступ запрещен'])

@section('content')
<h1 class="section-title">Доступ запрещен</h1>
<p class="text-muted">У вас нет прав для выполнения этого действия. Для обычного пользователя доступен только просмотр данных.</p>
<a href="{{ route('dashboard') }}" class="btn">Вернуться на главную</a>
@endsection
