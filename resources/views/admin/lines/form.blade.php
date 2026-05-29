@extends('layouts.app', ['title' => $line->exists ? 'Редактировать маршрут' : 'Добавить маршрут'])

@section('content')
<h1 class="section-title">{{ $line->exists ? 'Редактировать маршрут' : 'Добавить маршрут' }}</h1>
<form method="POST" action="{{ $line->exists ? route('lines.update', $line) : route('lines.store') }}">
    @csrf
    @if($line->exists) @method('PUT') @endif
    <div class="form-grid">
        <div class="field"><label>Название / код</label><input name="code" value="{{ old('code', $line->code) }}" required>@error('code')<div class="error-text">{{ $message }}</div>@enderror</div>
        <div class="field"><label>Тип транспорта</label><select name="type" required><option value="">Выберите</option>@foreach(['Трамвай','Автобус','Маршрутное такси'] as $type)<option value="{{ $type }}" @selected(old('type', $line->type)===$type)>{{ $type }}</option>@endforeach</select>@error('type')<div class="error-text">{{ $message }}</div>@enderror</div>
        <div class="field"><label>Начало работы</label><input type="time" name="start_time_operation" value="{{ old('start_time_operation', $line->start_time_operation) }}" required>@error('start_time_operation')<div class="error-text">{{ $message }}</div>@enderror</div>
        <div class="field"><label>Окончание работы</label><input type="time" name="end_time_operation" value="{{ old('end_time_operation', $line->end_time_operation) }}" required>@error('end_time_operation')<div class="error-text">{{ $message }}</div>@enderror</div>
        <div class="field full"><label>Map</label><input name="map" value="{{ old('map', $line->map) }}">@error('map')<div class="error-text">{{ $message }}</div>@enderror</div>
    </div>
    <div style="margin-top:18px;display:flex;gap:12px;"><button class="btn" type="submit">Сохранить</button><a class="btn btn-secondary" href="{{ route('lines.index') }}">Назад</a></div>
</form>
@endsection
