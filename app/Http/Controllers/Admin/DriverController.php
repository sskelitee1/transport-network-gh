<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DriverController extends Controller
{
    public function index(): View
    {
        return view('admin.drivers.index', [
            'drivers' => Driver::with('vehicle.line')->latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('admin.drivers.form', [
            'driver' => new Driver,
            'vehicles' => Vehicle::doesntHave('driver')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('drivers', 'public');
        }

        Driver::create($data);

        return redirect()->route('drivers.index')->with('success', 'Водитель создан.');
    }

    public function edit(Driver $driver): View
    {
        return view('admin.drivers.form', [
            'driver' => $driver,
            'vehicles' => Vehicle::whereDoesntHave('driver', function ($query) use ($driver) {
                $query->where('id', '!=', $driver->id);
            })->orWhere('id', $driver->vehicle_id)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Driver $driver): RedirectResponse
    {
        $data = $this->validateData($request, $driver);

        if ($request->hasFile('avatar')) {
            if ($driver->avatar) {
                Storage::disk('public')->delete($driver->avatar);
            }

            $data['avatar'] = $request->file('avatar')->store('drivers', 'public');
        }

        $driver->update($data);

        return redirect()->route('drivers.index')->with('success', 'Водитель обновлён.');
    }

    public function destroy(Driver $driver): RedirectResponse
    {
        if ($driver->avatar) {
            Storage::disk('public')->delete($driver->avatar);
        }

        $driver->delete();

        return redirect()->route('drivers.index')->with('success', 'Водитель удалён.');
    }

    private function validateData(Request $request, ?Driver $driver = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:45'],
            'birth_date' => ['nullable', 'date'],
            'email' => ['required', 'email', 'max:50', Rule::unique('drivers', 'email')->ignore($driver?->id)],
            'phone' => ['nullable', 'string', 'max:40'],
            'vehicle_id' => ['nullable', 'exists:vehicles,id', Rule::unique('drivers', 'vehicle_id')->ignore($driver?->id)],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);
    }
}
