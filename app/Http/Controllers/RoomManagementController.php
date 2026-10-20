<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoomManagementController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.rooms.index', [
            'rooms' => Room::query()->withCount('bookings')->orderBy('kode_kamar')->paginate(15),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.rooms.form', ['room' => new Room]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        Room::create($this->validatedRoom($request));

        return redirect()
            ->route('admin.rooms.index')
            ->with('success', 'Data kamar berhasil ditambahkan.');
    }

    public function edit(Request $request, Room $room): View
    {
        $this->authorizeAdmin($request);

        return view('admin.rooms.form', compact('room'));
    }

    public function update(Request $request, Room $room): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $room->update($this->validatedRoom($request, $room));

        return redirect()
            ->route('admin.rooms.index')
            ->with('success', 'Data kamar berhasil diperbarui.');
    }

    public function destroy(Request $request, Room $room): RedirectResponse
    {
        $this->authorizeAdmin($request);

        if ($room->kode_kamar === 'VIP') {
            return redirect()
                ->route('admin.rooms.index')
                ->with('error', 'Kamar VIP adalah kamar utama yang terhubung ke halaman detail khusus dan tidak dapat dihapus.');
        }

        if ($room->bookings()->exists()) {
            return redirect()
                ->route('admin.rooms.index')
                ->with('error', 'Kamar tidak dapat dihapus karena masih memiliki data booking. Hapus atau selesaikan booking sesuai aturan bisnis terlebih dahulu.');
        }

        $room->delete();

        return redirect()
            ->route('admin.rooms.index')
            ->with('success', 'Data kamar berhasil dihapus.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'admin', 403);
    }

    /**
     * @return array{kode_kamar: string, nama: string, lokasi: string, harga: int, status: string}
     */
    private function validatedRoom(Request $request, ?Room $room = null): array
    {
        $codeRules = [
            'required',
            'string',
            'max:255',
            Rule::unique('rooms', 'kode_kamar')->ignore($room?->id),
        ];

        if ($room?->kode_kamar === 'VIP') {
            $codeRules[] = Rule::in(['VIP']);
        }

        return $request->validate([
            'kode_kamar' => $codeRules,
            'nama' => ['required', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
            'harga' => ['required', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['tersedia', 'terisi', 'belum_dikonfirmasi'])],
        ]);
    }
}
