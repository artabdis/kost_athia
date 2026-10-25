<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_their_profile_and_replace_their_photo(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['role' => 'user']);
        $oldPhotoPath = UploadedFile::fake()
            ->image('old-photo.png')
            ->store('profile-photos', 'public');
        $user->forceFill(['profile_photo_path' => $oldPhotoPath])->save();

        $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => 'Nama Baru',
                'email' => 'nama-baru@example.com',
                'foto_profil' => UploadedFile::fake()->image('new-photo.png'),
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success', 'Profil berhasil diperbarui.');

        $updatedUser = $user->fresh();

        $this->assertSame('Nama Baru', $updatedUser->name);
        $this->assertSame('nama-baru@example.com', $updatedUser->email);
        $this->assertNotNull($updatedUser->profile_photo_path);
        Storage::disk('public')->assertExists($updatedUser->profile_photo_path);
        Storage::disk('public')->assertMissing($oldPhotoPath);
    }

    public function test_user_can_remove_their_profile_photo(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['role' => 'user']);
        $photoPath = UploadedFile::fake()
            ->image('profile.png')
            ->store('profile-photos', 'public');
        $user->forceFill(['profile_photo_path' => $photoPath])->save();

        $this->actingAs($user)
            ->delete(route('profile.photo.destroy'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success', 'Foto profil berhasil dihapus.');

        $this->assertNull($user->fresh()->profile_photo_path);
        Storage::disk('public')->assertMissing($photoPath);
    }

    public function test_profile_update_rejects_an_existing_email_and_non_image_file(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'name' => 'Nama Awal',
        ]);
        User::factory()->create(['email' => 'taken@example.com']);

        $this->actingAs($user)
            ->from(route('dashboard'))
            ->put(route('profile.update'), [
                'name' => 'Nama Terkirim',
                'email' => 'taken@example.com',
                'foto_profil' => UploadedFile::fake()->create('document.txt', 10, 'text/plain'),
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasErrors(['email', 'foto_profil']);

        $this->assertSame('Nama Awal', $user->fresh()->name);
    }
}
