<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()->latest()->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.form', ['user' => new User]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'gender' => ['nullable', 'in:M,F'],
            'birth_date' => ['nullable', 'date'],
            'email' => ['required', 'email', 'max:50', 'unique:users,email'],
            'login' => ['required', 'string', 'max:40', 'unique:users,login'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', Rule::in(['admin', 'user'])],
        ]);

        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return redirect()->route('users.index')->with('success', 'Пользователь создан.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'gender' => ['nullable', 'in:M,F'],
            'birth_date' => ['nullable', 'date'],
            'email' => ['required', 'email', 'max:50', Rule::unique('users', 'email')->ignore($user->id)],
            'login' => ['required', 'string', 'max:40', Rule::unique('users', 'login')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', Rule::in(['admin', 'user'])],
        ]);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Пользователь обновлен.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->login === 'admin') {
            return redirect()->route('users.index')->with('error', 'Основного администратора удалить нельзя.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Пользователь удален.');
    }
}
