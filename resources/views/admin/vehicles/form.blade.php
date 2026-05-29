@extends('layouts.app', ['title' => $vehicle->exists ? 'Редактировать транспорт' : 'Добавить транспорт'])

@section('content')
<h1 class="section-title">{{ $vehicle->exists ? 'Редактировать транспорт' : 'Добавить транспорт' }}</h1>
<form method="POST" action="{{ $vehicle->exists ? route('vehicles.update', $vehicle) : route('vehicles.store') }}">
    @csrf
    @if($vehicle->exists) @method('PUT') @endif
    <div class="form-grid">
        <div class="field"><label>Название</label><input name="name" value="{{ old('name', $vehicle->name) }}" required>@error('name')<div class="error-text">{{ $message }}</div>@enderror</div>
        <div class="field"><label>Вместимость</label><input type="number" min="1" name="capacity" value="{{ old('capacity', $vehicle->capacity) }}" required>@error('capacity')<div class="error-text">{{ $message }}</div>@enderror</div>
        <div class="field"><label>Тип</label><select name="type" required><option value="">Выберите</option>@foreach(['Трамвай','Автобус','Маршрутное такси'] as $type)<option value="{{ $type }}" @selected(old('type', $vehicle->type)===$type)>{{ $type }}</option>@endforeach</select>@error('type')<div class="error-text">{{ $message }}</div>@enderror</div>
        <div class="field"><label>Маршрут</label><select name="line_id"><option value="">Без привязки</option>@foreach($lines as $line)<option value="{{ $line->id }}" @selected((string)old('line_id', $vehicle->line_id)===(string)$line->id)>{{ $line->code }} ({{ $line->type }})</option>@endforeach</select>@error('line_id')<div class="error-text">{{ $message }}</div>@enderror</div>
    </div>
    <div style="margin-top:18px;display:flex;gap:12px;"><button class="btn" type="submit">Сохранить</button><a class="btn btn-secondary" href="{{ route('vehicles.index') }}">Назад</a></div>
</form>
@endsection
