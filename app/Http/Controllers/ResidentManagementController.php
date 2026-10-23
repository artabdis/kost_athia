<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ResidentManagementController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.residents.index', [
            'residents' => User::query()
                ->where('role', 'user')
                ->withCount(['bookings', 'tagihans'])
                ->orderBy('name')
                ->paginate(15),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.residents.form', ['resident' => new User]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate($this->rules());

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ])->forceFill([
            'role' => 'user',
            'payment_status' => $validated['payment_status'],
        ])->save();

        return redirect()
            ->route('admin.residents.index')
            ->with('success', 'Data penghuni berhasil ditambahkan.');
    }

    public function show(Request $request, User $resident): View
    {
        $this->authorizeAdmin($request);
        $this->authorizeResident($resident);

        return view('admin.residents.show', [
            'resident' => $resident->loadCount(['bookings', 'tagihans']),
        ]);
    }

    public function edit(Request $request, User $resident): View
    {
        $this->authorizeAdmin($request);
        $this->authorizeResident($resident);

        return view('admin.residents.form', compact('resident'));
    }

    public function update(Request $request, User $resident): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $this->authorizeResident($resident);

        $validated = $request->validate($this->rules($resident));

        $resident->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (filled($validated['password'] ?? null)) {
            $resident->password = Hash::make($validated['password']);
        }

        $resident->forceFill([
            'payment_status' => $validated['payment_status'],
        ])->save();

        return redirect()
            ->route('admin.residents.index')
            ->with('success', 'Data penghuni berhasil diperbarui.');
    }

    public function destroy(Request $request, User $resident): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $this->authorizeResident($resident);

        if ($resident->is($request->user())) {
            return redirect()
                ->route('admin.residents.index')
                ->with('error', 'Akun admin yang sedang digunakan tidak dapat dihapus dari Data Penghuni.');
        }

        if ($resident->bookings()->exists() || $resident->tagihans()->exists()) {
            return redirect()
                ->route('admin.residents.index')
                ->with('error', 'Penghuni tidak dapat dihapus karena masih memiliki riwayat booking atau tagihan.');
        }

        $resident->delete();

        return redirect()
            ->route('admin.residents.index')
            ->with('success', 'Data penghuni berhasil dihapus.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'admin', 403);
    }

    private function authorizeResident(User $resident): void
    {
        abort_unless($resident->role === 'user', 404);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(?User $resident = null): array
    {
        $passwordRules = $resident
            ? ['nullable', 'string', Password::min(8), 'regex:/^[A-Za-z0-9]+$/']
            : ['required', 'string', Password::min(8), 'regex:/^[A-Za-z0-9]+$/'];

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($resident?->id),
            ],
            'password' => $passwordRules,
            'payment_status' => ['required', Rule::in(['pending', 'submitted', 'approved', 'rejected'])],
        ];
    }
}
