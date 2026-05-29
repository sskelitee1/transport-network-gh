<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Line;
use App\Models\Station;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StationController extends Controller
{
    public function index(): View
    {
        return view('admin.stations.index', [
            'stations' => Station::with('line')->latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('admin.stations.form', [
            'station' => new Station,
            'lines' => Line::orderBy('code')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $this->guardLineLimit($data['line_id'] ?? null);
        Station::create($data);

        return redirect()->route('stations.index')->with('success', 'Остановка создана.');
    }

    public function edit(Station $station): View
    {
        return view('admin.stations.form', [
            'station' => $station,
            'lines' => Line::orderBy('code')->get(),
        ]);
    }

    public function update(Request $request, Station $station): RedirectResponse
    {
        $data = $this->validateData($request);
        $this->guardLineLimit($data['line_id'] ?? null, $station->id);
        $station->update($data);

        return redirect()->route('stations.index')->with('success', 'Остановка обновлена.');
    }

    public function destroy(Station $station): RedirectResponse
    {
        $station->delete();

        return redirect()->route('stations.index')->with('success', 'Остановка удалена.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'position_station' => ['nullable', 'string', 'max:15'],
            'line_id' => ['nullable', 'exists:lines,id'],
        ]);
    }

    private function guardLineLimit(?int $lineId, ?int $ignoreStationId = null): void
    {
        if (! $lineId) {
            return;
        }

        $query = Station::where('line_id', $lineId);

        if ($ignoreStationId) {
            $query->where('id', '!=', $ignoreStationId);
        }

        if ($query->count() >= 7) {
            throw ValidationException::withMessages([
                'line_id' => 'У выбранного маршрута уже максимум 7 остановок.',
            ]);
        }
    }
}
