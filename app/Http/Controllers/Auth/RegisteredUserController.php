<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Barangay;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $barangays = Barangay::orderBy('name')->get();

        return view('pages.auth.login', compact('barangays'))->with('defaultMode', 'register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name'  => ['required', 'string', 'max:128'],
            'last_name'   => ['required', 'string', 'max:128'],
            'email'       => ['required', 'string', 'email', 'max:191', 'unique:users,email'],
            'gender'      => ['required', 'in:male,female'],
            'birthdate'   => ['required', 'date', 'before:today'],
            'barangay_id' => ['nullable', 'exists:barangays,id'],
            'password'    => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $name  = trim($validated['first_name'] . ' ' . $validated['last_name']);
        $age   = Carbon::parse($validated['birthdate'])->age;

        $user = User::create([
            'name'           => $name,
            'email'          => $validated['email'],
            'password'       => Hash::make($validated['password']),
            'role'           => 'citizen',
            'account_status' => 'pending',
            'gender'         => $validated['gender'],
            'birthdate'      => $validated['birthdate'],
            'age'            => $age,
            'barangay_id'    => $validated['barangay_id'] ?? null,
        ]);

        event(new Registered($user));

        // Do NOT log in — the account needs admin approval first.
        return redirect()->route('register.pending');
    }
}
