<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Line;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LineController extends Controller
{
    public function index(): View
    {
        return view('admin.lines.index', [
            'lines' => Line::withCount(['stations', 'vehicles'])->latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('admin.lines.form', ['line' => new Line]);
    }

    public function store(Request $request): RedirectResponse
    {
        Line::create($this->validateData($request));

        return redirect()->route('lines.index')->with('success', 'Маршрут создан.');
    }

    public function edit(Line $line): View
    {
        return view('admin.lines.form', compact('line'));
    }

    public function update(Request $request, Line $line): RedirectResponse
    {
        $line->update($this->validateData($request));

        return redirect()->route('lines.index')->with('success', 'Маршрут обновлён.');
    }

    public function destroy(Line $line): RedirectResponse
    {
        $line->delete();

        return redirect()->route('lines.index')->with('success', 'Маршрут удалён. Связанные остановки и транспорт сохранены.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'start_time_operation' => ['required'],
            'end_time_operation' => ['required'],
            'type' => ['required', Rule::in(['Трамвай', 'Автобус', 'Маршрутное такси'])],
            'map' => ['nullable', 'string', 'max:50'],
        ]);
    }
}
