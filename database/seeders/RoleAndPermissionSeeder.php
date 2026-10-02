<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage_users', 'manage_master', 'manage_template',
            'create_pengajuan', 'edit_pengajuan', 'view_pengajuan',
            'generate_nomor_surat', 'verify_pengajuan', 'sign_pengajuan'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Super Admin
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $roleSuperAdmin->givePermissionTo(Permission::all());

        // Admin Fakultas
        $roleAdmin = Role::firstOrCreate(['name' => 'Admin Fakultas']);
        $roleAdmin->givePermissionTo(['create_pengajuan', 'edit_pengajuan', 'view_pengajuan']);

        // BAAK
        $roleBaak = Role::firstOrCreate(['name' => 'BAAK']);
        $roleBaak->givePermissionTo(['view_pengajuan', 'generate_nomor_surat', 'verify_pengajuan']);

        // Penandatangan
        $roleTtd = Role::firstOrCreate(['name' => 'Penandatangan']);
        $roleTtd->givePermissionTo(['view_pengajuan', 'sign_pengajuan']);
    }
}