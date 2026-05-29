@extends('layouts.app', ['title' => $station->exists ? 'Редактировать остановку' : 'Добавить остановку'])

@section('content')
<h1 class="section-title">{{ $station->exists ? 'Редактировать остановку' : 'Добавить остановку' }}</h1>
<form method="POST" action="{{ $station->exists ? route('stations.update', $station) : route('stations.store') }}">
    @csrf
    @if($station->exists) @method('PUT') @endif
    <div class="form-grid">
        <div class="field"><label>Название</label><input name="name" value="{{ old('name', $station->name) }}" required>@error('name')<div class="error-text">{{ $message }}</div>@enderror</div>
        <div class="field"><label>Position</label><input name="position_station" value="{{ old('position_station', $station->position_station) }}">@error('position_station')<div class="error-text">{{ $message }}</div>@enderror</div>
        <div class="field full"><label>Маршрут</label><select name="line_id"><option value="">Без привязки</option>@foreach($lines as $line)<option value="{{ $line->id }}" @selected((string)old('line_id', $station->line_id)===(string)$line->id)>{{ $line->code }} ({{ $line->type }})</option>@endforeach</select>@error('line_id')<div class="error-text">{{ $message }}</div>@enderror</div>
    </div>
    <div style="margin-top:18px;display:flex;gap:12px;"><button class="btn" type="submit">Сохранить</button><a class="btn btn-secondary" href="{{ route('stations.index') }}">Назад</a></div>
</form>
@endsection
