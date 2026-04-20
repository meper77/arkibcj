<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'kampus' => ['required', 'string', 'max:255'],
            'cawangan' => ['nullable', 'string', 'max:255'],
            'fakulti_bahagian' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update([
            'name' => $request->name,
            'kampus' => $request->kampus,
            'cawangan' => $request->cawangan ?: null,
            'fakulti_bahagian' => $request->fakulti_bahagian ?: null,
        ]);

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'string',
                'confirmed',
                function ($attribute, $value, $fail) {
                    if (strlen($value) !== 8) {
                        $fail('Kata laluan baharu mestilah tepat 8 aksara.');
                    }
                },
            ],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.edit')->with('status', 'password-updated');
    }

    public function transferPosition(Request $request): RedirectResponse
    {
        $currentUser = $request->user();

        $request->validate([
            'target_user_id' => ['required', 'exists:users,id'],
        ]);

        $targetUser = User::find($request->target_user_id);

        if (!$currentUser->position) {
            return back()->withErrors(['transfer' => 'Anda tidak mempunyai jawatan untuk dipindahkan.']);
        }

        if ($targetUser->position && $targetUser->position !== $currentUser->position) {
            return back()->withErrors(['transfer' => 'Pengguna sasaran sudah mempunyai jawatan lain.']);
        }

        $position = $currentUser->position;
        $currentUser->update(['position' => null]);
        $targetUser->update(['position' => $position]);

        return redirect()->route('profile.edit')->with('status', 'position-transferred');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to('/');
    }
}
