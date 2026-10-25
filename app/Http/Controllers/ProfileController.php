<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use RuntimeException;

class ProfileController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user),
            ],
            'foto_profil' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ], [
            'foto_profil.image' => 'File foto profil harus berupa gambar.',
            'foto_profil.mimes' => 'Foto profil harus berformat JPG, JPEG, PNG, atau WEBP.',
            'foto_profil.max' => 'Ukuran foto profil maksimal 5 MB.',
        ]);

        $oldPhotoPath = $user->profile_photo_path;
        $newPhotoPath = null;

        if ($request->hasFile('foto_profil')) {
            $newPhotoPath = $request->file('foto_profil')->store('profile-photos', 'public');

            if ($newPhotoPath === false) {
                throw new RuntimeException('Foto profil gagal disimpan.');
            }
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($newPhotoPath !== null) {
            $user->profile_photo_path = $newPhotoPath;
        }

        $user->save();

        if ($newPhotoPath !== null && $oldPhotoPath !== null) {
            $this->deletePhotoFile($oldPhotoPath);
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    public function destroyPhoto(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User, 403);

        $photoPath = $user->profile_photo_path;

        if ($photoPath !== null) {
            $user->profile_photo_path = null;
            $user->save();

            $this->deletePhotoFile($photoPath);
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Foto profil berhasil dihapus.');
    }

    private function deletePhotoFile(string $path): void
    {
        if (! Storage::disk('public')->delete($path)) {
            throw new RuntimeException('Foto profil lama gagal dihapus.');
        }
    }
}
