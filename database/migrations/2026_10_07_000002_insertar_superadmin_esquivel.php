<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Crea el Superadmin principal (esquivel7809@gmail.com).
 * Usa insertOrIgnore para no fallar si ya existe (por ejemplo,
 * si ya fue creado a mano antes de esta migración).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->insertOrIgnore([
            'id' => 1111111119,
            'name' => 'Super Administrador',
            'email' => 'esquivel7809@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'rol' => 'superadmin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('users')->where('id', 1111111119)->delete();
    }
};
