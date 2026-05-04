<?php

namespace App\Http\Controllers;

use App\Models\AvailableFakultiBahagian;
use App\Models\History;
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
        $users = User::with('fakultiBahagian')->where('is_superadmin', false)->orderBy('name')->get();
        $fakultis = AvailableFakultiBahagian::orderBy('nama')->get();
        return view('users.index', compact('users', 'fakultis'));
    }

    public function create(): View
    {
        $fakultis = AvailableFakultiBahagian::orderBy('nama')->get();
        return view('users.create', compact('fakultis'));
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
            'fakulti_bahagian_id' => ['nullable', 'exists:available_fakulti_bahagian,id'],
            'position' => [
                'nullable',
                Rule::in(['PTRJ', 'PRJ']),
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && $request->fakulti_bahagian_id) {
                        $taken = User::where('position', $value)
                            ->where('fakulti_bahagian_id', $request->fakulti_bahagian_id)
                            ->exists();
                        if ($taken) {
                            $fail('Jawatan ' . $value . ' sudah didaftarkan untuk fakulti/bahagian ini.');
                        }
                    } elseif ($value && !$request->fakulti_bahagian_id) {
                        $fail('Fakulti/Bahagian diperlukan untuk menetapkan jawatan.');
                    }
                },
            ],
        ]);

        $newUser = User::create([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'password' => Hash::make('password'),
            'kampus' => $request->kampus,
            'cawangan' => $request->cawangan ?: null,
            'fakulti_bahagian_id' => $request->fakulti_bahagian_id ?: null,
            'position' => $request->position ?: null,
            'is_superadmin' => false,
        ]);

        History::log('Tambah Pengguna', $newUser->email, 'user', $newUser->id, $newUser->fakulti_bahagian_id);

        return redirect()->route('users.index')->with('success', 'Pengguna berjaya ditambah.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->is_superadmin) {
            return back()->withErrors(['user' => 'Superadmin tidak boleh dipadam.']);
        }

        $email = $user->email;
        $userId = $user->id;
        $fakultiId = $user->fakulti_bahagian_id;
        $user->delete();

        History::log('Padam Pengguna', $email, 'user', $userId, $fakultiId);

        return redirect()->route('users.index')->with('success', 'Pengguna berjaya dipadam.');
    }

    public function resetPassword(User $user): RedirectResponse
    {
        if ($user->is_superadmin) {
            return back()->withErrors(['user' => 'Superadmin tidak boleh direset.']);
        }

        $user->update(['password' => Hash::make('password')]);

        History::log('Reset Kata Laluan Pengguna', $user->email, 'user', $user->id, $user->fakulti_bahagian_id);

        return redirect()->route('users.index')->with('success', 'Kata laluan berjaya direset kepada "password".');
    }

    public function editPosition(User $user): View
    {
        $availablePositions = $this->getAvailablePositions($user->position);
        return view('users.edit-position', compact('user', 'availablePositions'));
    }

    public function updateFakulti(Request $request, User $user): RedirectResponse
    {
        if ($user->is_superadmin) {
            return back()->withErrors(['fakulti' => 'Superadmin tidak boleh ditukar fakulti melalui senarai.']);
        }

        $request->validate([
            'fakulti_bahagian_id' => ['nullable', 'integer', Rule::exists('available_fakulti_bahagian', 'id')],
        ]);

        $newId = $request->fakulti_bahagian_id ?: null;

        if ($newId !== $user->fakulti_bahagian_id && $user->position && $newId) {
            $taken = User::where('position', $user->position)
                ->where('fakulti_bahagian_id', $newId)
                ->where('id', '!=', $user->id)
                ->exists();
            if ($taken) {
                return back()->withErrors(['fakulti' => 'Fakulti/bahagian sasaran sudah mempunyai pengguna dengan jawatan ' . $user->position . '.']);
            }
        }

        $user->update(['fakulti_bahagian_id' => $newId]);

        $newFakultiNama = $newId
            ? (AvailableFakultiBahagian::find($newId)?->nama ?? '-')
            : '-';
        History::log(
            'Kemaskini Fakulti Pengguna',
            $user->email . ' → ' . $newFakultiNama,
            'user',
            $user->id,
            $newId,
        );

        return back()->with('success', 'Fakulti/Bahagian berjaya dikemaskini.');
    }

    public function updatePosition(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'position' => [
                'nullable',
                Rule::in(['PTRJ', 'PRJ']),
                function ($attribute, $value, $fail) use ($user) {
                    if ($value && $value !== $user->position) {
                        if (!$user->fakulti_bahagian_id) {
                            $fail('Pengguna mesti dikaitkan dengan fakulti/bahagian sebelum menetapkan jawatan.');
                            return;
                        }
                        $taken = User::where('position', $value)
                            ->where('fakulti_bahagian_id', $user->fakulti_bahagian_id)
                            ->where('id', '!=', $user->id)
                            ->exists();
                        if ($taken) {
                            $fail('Jawatan ' . $value . ' sudah didaftarkan untuk fakulti/bahagian ini.');
                        }
                    }
                },
            ],
        ]);

        $user->update(['position' => $request->position ?: null]);

        History::log(
            'Kemaskini Jawatan Pengguna',
            $user->email . ' → ' . ($request->position ?: '-'),
            'user',
            $user->id,
            $user->fakulti_bahagian_id,
        );

        return redirect()->route('users.index')->with('success', 'Jawatan berjaya dikemaskini.');
    }

    private function getAvailablePositions(?string $currentPosition): array
    {
        $positions = ['PTRJ', 'PRJ'];
        $available = [];
        $user = request()->route('user');
        $userId = $user->id ?? 0;
        $fakultiId = $user->fakulti_bahagian_id ?? null;

        foreach ($positions as $pos) {
            if (!$fakultiId) {
                $available[$pos] = $pos === $currentPosition;
                continue;
            }
            $taken = User::where('position', $pos)
                ->where('fakulti_bahagian_id', $fakultiId)
                ->where('id', '!=', $userId)
                ->exists();
            $available[$pos] = !$taken || $pos === $currentPosition;
        }

        return $available;
    }
}
