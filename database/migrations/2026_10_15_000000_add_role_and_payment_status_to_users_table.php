<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('user');
            }
            if (! Schema::hasColumn('users', 'payment_status')) {
                $table->string('payment_status')->default('pending');
            }
            if (! Schema::hasColumn('users', 'payment_proof')) {
                $table->string('payment_proof')->nullable();
            }
            if (! Schema::hasColumn('users', 'payment_note')) {
                $table->text('payment_note')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = ['payment_status', 'payment_proof', 'payment_note'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
