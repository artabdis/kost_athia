<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            return;
        }

        $hasAdmin = DB::table('users')->where('role', 'admin')->exists();
        $firstUserId = DB::table('users')->orderBy('id')->value('id');

        if (! $hasAdmin && $firstUserId !== null) {
            DB::table('users')
                ->where('id', $firstUserId)
                ->update(['role' => 'admin']);
        }
    }

    public function down(): void
    {
        // Keep the first account's admin role when rolling back this data migration.
    }
};
