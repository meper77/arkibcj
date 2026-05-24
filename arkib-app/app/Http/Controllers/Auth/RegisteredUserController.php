<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
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
                'unique:' . User::class,
                function ($attribute, $value, $fail) {
                    if (!str_ends_with(strtolower($value), '@uitm.edu.my')) {
                        $fail('Emel mesti menggunakan domain @uitm.edu.my.');
                    }
                },
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                function ($attribute, $value, $fail) {
                    if (strlen($value) !== 8) {
                        $fail('Kata laluan mestilah tepat 8 aksara.');
                    }
                },
            ],
            'kampus' => ['required', 'string', 'max:255'],
            'cawangan' => ['nullable', 'string', 'max:255'],
            'fakulti_bahagian_id' => ['nullable', 'integer', 'exists:available_fakulti_bahagian,id'],
            'position' => [
                'nullable',
                Rule::in(['PTRJ', 'PRJ']),
                function ($attribute, $value, $fail) use ($request) {
                    if ($value) {
                        $exists = User::where('position', $value)->exists();
                        if ($exists) {
                            $fail('Jawatan ' . $value . ' sudah didaftarkan oleh pengguna lain.');
                        }
                    }
                },
            ],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
            'kampus' => $request->kampus,
            'cawangan' => $request->cawangan ?: null,
            'fakulti_bahagian_id' => $request->fakulti_bahagian_id ?: null,
            'position' => $request->position ?: null,
            'is_superadmin' => false,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('no-rujukan.index', absolute: false));
    }
}
