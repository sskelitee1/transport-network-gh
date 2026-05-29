<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Line;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(): View
    {
        return view('admin.vehicles.index', [
            'vehicles' => Vehicle::with(['line', 'driver'])->latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('admin.vehicles.form', [
            'vehicle' => new Vehicle,
            'lines' => Line::orderBy('code')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $this->guardLineRules($data['line_id'] ?? null, $data['type']);
        Vehicle::create($data);

        return redirect()->route('vehicles.index')->with('success', 'Транспортное средство создано.');
    }

    public function edit(Vehicle $vehicle): View
    {
        return view('admin.vehicles.form', [
            'vehicle' => $vehicle,
            'lines' => Line::orderBy('code')->get(),
        ]);
    }

    public function update(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $data = $this->validateData($request);
        $this->guardLineRules($data['line_id'] ?? null, $data['type'], $vehicle->id);
        $vehicle->update($data);

        return redirect()->route('vehicles.index')->with('success', 'Транспортное средство обновлено.');
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $vehicle->delete();

        return redirect()->route('vehicles.index')->with('success', 'Транспортное средство удалено.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:30'],
            'capacity' => ['required', 'integer', 'min:1'],
            'type' => ['required', Rule::in(['Трамвай', 'Автобус', 'Маршрутное такси'])],
            'line_id' => ['nullable', 'exists:lines,id'],
        ]);
    }

    private function guardLineRules(?int $lineId, string $type, ?int $ignoreVehicleId = null): void
    {
        if (! $lineId) {
            return;
        }

        $line = Line::findOrFail($lineId);

        if ($line->type !== $type) {
            throw ValidationException::withMessages([
                'type' => 'Тип транспорта должен совпадать с типом маршрута.',
            ]);
        }

        $query = Vehicle::where('line_id', $lineId);

        if ($ignoreVehicleId) {
            $query->where('id', '!=', $ignoreVehicleId);
        }

        if ($query->count() >= 10) {
            throw ValidationException::withMessages([
                'line_id' => 'У выбранного маршрута уже максимум 10 транспортных средств.',
            ]);
        }
    }
}
