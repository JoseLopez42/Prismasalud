<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('migrations')->whereIn('migration', [
            '2026_10_05_000001_create_planes_table',
            '2026_10_05_000002_create_licencias_table',
            '2026_10_05_000003_add_licencia_id_to_usuarios_table',
            '2026_10_05_000004_create_transacciones_table',
            '2026_10_05_000005_add_rol_to_users_table',
            '2026_10_13_200528_create_or_update_licencias_table',
        ])->delete();
    }

    public function down(): void
    {
        DB::table('migrations')->insert([
            ['migration' => '2026_10_05_000001_create_planes_table', 'batch' => 11],
            ['migration' => '2026_10_05_000002_create_licencias_table', 'batch' => 11],
            ['migration' => '2026_10_05_000003_add_licencia_id_to_usuarios_table', 'batch' => 11],
            ['migration' => '2026_10_05_000004_create_transacciones_table', 'batch' => 11],
            ['migration' => '2026_10_05_000005_add_rol_to_users_table', 'batch' => 11],
            ['migration' => '2026_10_13_200528_create_or_update_licencias_table', 'batch' => 18],
        ]);
    }
};
