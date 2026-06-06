<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Barangay;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserApprovalController extends Controller
{
    /**
     * Show all pending (and recent) citizen account registrations + all users table.
     */
    public function index(): View
    {
        $pending = User::with('barangay')
            ->where('role', 'citizen')
            ->where('account_status', 'pending')
            ->latest()
            ->get();

        $recent = User::with('barangay')
            ->where('role', 'citizen')
            ->whereIn('account_status', ['approved', 'rejected'])
            ->latest()
            ->take(20)
            ->get();

        $statusOrder = ['pending' => 0, 'approved' => 1, 'rejected' => 2];

        $allUsers = User::with('barangay')
            ->latest()
            ->get()
            ->sortBy(fn ($u) => $statusOrder[$u->account_status] ?? 9)
            ->values();

        $barangays = Barangay::orderBy('name')->get();

        return view('pages.admin.users.index', compact('pending', 'recent', 'allUsers', 'barangays'));
    }

    public function storeRhu(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:191'],
            'email'        => ['required', 'email', 'max:191', 'unique:users,email'],
            'org_role'     => ['required', 'string', 'max:191'],
            'barangay_id'  => ['nullable', 'exists:barangays,id'],
            'new_password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name'           => $validated['name'],
            'email'          => $validated['email'],
            'password'       => Hash::make($validated['new_password']),
            'role'           => 'rhu',
            'org_role'       => $validated['org_role'],
            'account_status' => 'approved',
            'barangay_id'    => $validated['barangay_id'] ?? null,
        ]);

        ActivityLog::create([
            'user_id'    => $request->user()->id,
            'action'     => 'rhu_account_created',
            'properties' => [
                'target_id'   => $user->id,
                'target_name' => preg_replace('/\s+(\S)\S*$/', ' $1.', $user->name),
            ],
        ]);

        return back()->with('success', "RHU account for {$user->name} has been created.");
    }

    /**
     * Approve a citizen's account registration.
     */
    public function approve(User $user): RedirectResponse
    {
        abort_if($user->role !== 'citizen', 403);

        $user->update(['account_status' => 'approved']);

        return back()->with('success', "{$user->name}'s account has been approved.");
    }

    /**
     * Reject a citizen's account registration.
     */
    public function reject(User $user): RedirectResponse
    {
        abort_if($user->role !== 'citizen', 403);

        $user->update(['account_status' => 'rejected']);

        return back()->with('success', "{$user->name}'s account has been rejected.");
    }

    /**
     * Update a user's profile fields without changing their role lane.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:191'],
            'email'        => ['required', 'email', 'max:191', "unique:users,email,{$user->id}"],
            'org_role'     => ['nullable', 'string', 'max:191'],
            'barangay_id'  => ['nullable', 'exists:barangays,id'],
            'new_password' => ['nullable', 'string', 'min:8'],
        ]);

        $user->update([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'org_role'    => $user->role === 'citizen' ? null : ($validated['org_role'] ?? null),
            'barangay_id' => $validated['barangay_id'] ?? null,
        ]);

        if (! empty($validated['new_password'])) {
            $user->update(['password' => Hash::make($validated['new_password'])]);
        }

        return back()->with('success', "{$user->name}'s information has been updated.");
    }
}
