<?php

namespace App\Http\Controllers;

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
        $users = User::where('is_superadmin', false)->orderBy('name')->get();
        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
                function ($attribute, $value, $fail) {
                    if (!str_ends_with(strtolower($value), '@uitm.edu.my')) {
                        $fail('Emel mesti menggunakan domain @uitm.edu.my.');
                    }
                },
            ],
            'kampus' => ['required', 'string', 'max:255'],
            'cawangan' => ['nullable', 'string', 'max:255'],
            'fakulti_bahagian' => ['nullable', 'string', 'max:255'],
            'position' => [
                'nullable',
                Rule::in(['PTRJ', 'PRJ']),
                function ($attribute, $value, $fail) use ($request) {
                    if ($value) {
                        if (User::where('position', $value)->exists()) {
                            $fail('Jawatan ' . $value . ' sudah didaftarkan.');
                        }
                    }
                },
            ],
        ]);

        User::create([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'password' => Hash::make('password'),
            'kampus' => $request->kampus,
            'cawangan' => $request->cawangan ?: null,
            'fakulti_bahagian' => $request->fakulti_bahagian ?: null,
            'position' => $request->position ?: null,
            'is_superadmin' => false,
        ]);

        return redirect()->route('users.index')->with('success', 'Pengguna berjaya ditambah.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->is_superadmin) {
            return back()->withErrors(['user' => 'Superadmin tidak boleh dipadam.']);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Pengguna berjaya dipadam.');
    }

    public function resetPassword(User $user): RedirectResponse
    {
        if ($user->is_superadmin) {
            return back()->withErrors(['user' => 'Superadmin tidak boleh direset.']);
        }

        $user->update(['password' => Hash::make('password')]);

        return redirect()->route('users.index')->with('success', 'Kata laluan berjaya direset kepada "password".');
    }

    public function editPosition(User $user): View
    {
        $availablePositions = $this->getAvailablePositions($user->position);
        return view('users.edit-position', compact('user', 'availablePositions'));
    }

    public function updatePosition(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'position' => [
                'nullable',
                Rule::in(['PTRJ', 'PRJ']),
                function ($attribute, $value, $fail) use ($user) {
                    if ($value && $value !== $user->position) {
                        if (User::where('position', $value)->where('id', '!=', $user->id)->exists()) {
                            $fail('Jawatan ' . $value . ' sudah didaftarkan.');
                        }
                    }
                },
            ],
        ]);

        $user->update(['position' => $request->position ?: null]);

        return redirect()->route('users.index')->with('success', 'Jawatan berjaya dikemaskini.');
    }

    private function getAvailablePositions(?string $currentPosition): array
    {
        $positions = ['PTRJ', 'PRJ'];
        $available = [];

        foreach ($positions as $pos) {
            $taken = User::where('position', $pos)->where('id', '!=', request()->route('user')->id ?? 0)->exists();
            $available[$pos] = !$taken || $pos === $currentPosition;
        }

        return $available;
    }
}
