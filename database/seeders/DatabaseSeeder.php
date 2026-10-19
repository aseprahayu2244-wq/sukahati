<?php

namespace Database\Seeders;

use App\Models\Bod;
use App\Models\Division;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Users ---------------------------------------------------
        User::create([
            'name'      => 'Super Admin',
            'username'  => 'superadmin',
            'email'     => 'superadmin@sukhati.com',
            'password'  => Hash::make('password'),
            'role'      => 'super_admin',
            'jabatan'   => 'Super Administrator',
            'is_active' => true,
        ]);

        User::create([
            'name'      => 'Admin HRD',
            'username'  => 'admin',
            'email'     => 'admin@sukhati.com',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'jabatan'   => 'Administrator HRD',
            'is_active' => true,
        ]);

        User::create([
            'name'      => 'Anggota Umum',
            'username'  => 'anggota',
            'email'     => 'anggota@sukhati.com',
            'password'  => Hash::make('password'),
            'role'      => 'anggota',
            'jabatan'   => 'Staff Umum',
            'is_active' => true,
        ]);

        // --- BODs ---------------------------------------------------
        $bodA = Bod::create([
            'name'        => 'BOD A',
            'description' => 'Direksi A - Operasional & SDM',
            'color'       => '#3b82f6',
            'order'       => 1,
        ]);

        $bodB = Bod::create([
            'name'        => 'BOD B',
            'description' => 'Direksi B - Keuangan & Produksi',
            'color'       => '#10b981',
            'order'       => 2,
        ]);

        // --- Departments (Divisions) under BOD A -------------------
        $divData = [
            [
                'bod_id' => $bodA->id, 'name' => 'HRD',
                'color' => '#3b82f6', 'order' => 1,
                'description' => 'Departemen Human Resource Development',
                'positions' => ['HR Manager', 'Staff HRD', 'Recruitment Staff'],
            ],
            [
                'bod_id' => $bodA->id, 'name' => 'Marketing',
                'color' => '#ef4444', 'order' => 2,
                'description' => 'Departemen Marketing & Pemasaran',
                'positions' => ['Marketing Manager', 'Staff Marketing'],
            ],
        ];

        // --- Departments under BOD B ---
        $divData[] = [
            'bod_id' => $bodB->id, 'name' => 'Keuangan',
            'color' => '#10b981', 'order' => 3,
            'description' => 'Departemen Keuangan & Akuntansi',
            'positions' => ['Finance Manager', 'Staff Finance', 'Staff Akuntansi'],
        ];

        $divData[] = [
            'bod_id' => $bodB->id, 'name' => 'Produksi',
            'color' => '#f59e0b', 'order' => 4,
            'description' => 'Departemen Produksi & Operasional',
            'positions' => ['Manager Produksi', 'Supervisor Produksi', 'Staff Produksi'],
        ];

        $divData[] = [
            'bod_id' => $bodB->id, 'name' => 'IT',
            'color' => '#8b5cf6', 'order' => 5,
            'description' => 'Departemen Teknologi Informasi',
            'positions' => ['IT Manager', 'Staff IT', 'Developer'],
        ];

        foreach ($divData as $data) {
            $positions = $data['positions'];
            unset($data['positions']);

            $division = Division::create($data);

            $order = 1;
            foreach ($positions as $posName) {
                Position::create([
                    'division_id' => $division->id,
                    'name'        => $posName,
                    'level'       => 0,
                    'order'       => $order++,
                    'parent_id'   => null,
                ]);
            }
        }
    }
}
