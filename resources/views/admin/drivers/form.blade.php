@extends('layouts.app', ['title' => $driver->exists ? 'Редактировать водителя' : 'Добавить водителя'])

@section('content')
<h1 class="section-title">{{ $driver->exists ? 'Редактировать водителя' : 'Добавить водителя' }}</h1>
<form method="POST" enctype="multipart/form-data" action="{{ $driver->exists ? route('drivers.update', $driver) : route('drivers.store') }}">
    @csrf
    @if($driver->exists) @method('PUT') @endif
    <div class="form-grid">
        <div class="field"><label>ФИО</label><input name="name" value="{{ old('name', $driver->name) }}" required>@error('name')<div class="error-text">{{ $message }}</div>@enderror</div>
        <div class="field"><label>Дата рождения</label><input type="date" name="birth_date" value="{{ old('birth_date', optional($driver->birth_date)->format('Y-m-d')) }}">@error('birth_date')<div class="error-text">{{ $message }}</div>@enderror</div>
        <div class="field"><label>Email</label><input type="email" name="email" value="{{ old('email', $driver->email) }}" required>@error('email')<div class="error-text">{{ $message }}</div>@enderror</div>
        <div class="field"><label>Телефон</label><input name="phone" value="{{ old('phone', $driver->phone) }}">@error('phone')<div class="error-text">{{ $message }}</div>@enderror</div>
        <div class="field"><label>Фото</label><input type="file" name="avatar" accept="image/*">@error('avatar')<div class="error-text">{{ $message }}</div>@enderror</div>
        <div class="field"><label>Транспорт</label><select name="vehicle_id"><option value="">Без привязки</option>@foreach($vehicles as $vehicle)<option value="{{ $vehicle->id }}" @selected((string)old('vehicle_id', $driver->vehicle_id)===(string)$vehicle->id)>{{ $vehicle->name }} @if($vehicle->line) / {{ $vehicle->line->code }} @endif</option>@endforeach</select>@error('vehicle_id')<div class="error-text">{{ $message }}</div>@enderror</div>
        @if($driver->avatar)
        <div class="field full"><label>Текущий аватар</label><img class="avatar-preview" src="{{ asset('storage/'.$driver->avatar) }}" alt="{{ $driver->name }}"></div>
        @endif
    </div>
    <div style="margin-top:18px;display:flex;gap:12px;"><button class="btn" type="submit">Сохранить</button><a class="btn btn-secondary" href="{{ route('drivers.index') }}">Назад</a></div>
</form>
@endsection
