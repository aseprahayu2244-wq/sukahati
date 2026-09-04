<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Users -----------------------------------------------
        $ketua = User::create([
            'name'     => 'Ketua HRD',
            'email'    => 'ketua@sukhati.com',
            'password' => Hash::make('password'),
            'role'     => 'ketua',
        ]);

        User::create([
            'name'     => 'User Umum',
            'email'    => 'user@sukhati.com',
            'password' => Hash::make('password'),
            'role'     => 'user_umum',
        ]);

        // --- Divisions & Positions --------------------------------
        $divisions = [
            ['name' => 'HRD', 'color' => '#3b82f6', 'order' => 1, 'positions' => [
                ['name' => 'HR Manager', 'level' => 0, 'order' => 1],
                ['name' => 'Staff HRD', 'level' => 1, 'order' => 2],
                ['name' => 'Recruitment Staff', 'level' => 1, 'order' => 3],
            ]],
            ['name' => 'Keuangan', 'color' => '#10b981', 'order' => 2, 'positions' => [
                ['name' => 'Finance Manager', 'level' => 0, 'order' => 1],
                ['name' => 'Staff Finance', 'level' => 1, 'order' => 2],
                ['name' => 'Staff Akuntansi', 'level' => 1, 'order' => 3],
            ]],
            ['name' => 'Produksi', 'color' => '#f59e0b', 'order' => 3, 'positions' => [
                ['name' => 'Manager Produksi', 'level' => 0, 'order' => 1],
                ['name' => 'Supervisor Produksi', 'level' => 1, 'order' => 2],
                ['name' => 'Staff Produksi', 'level' => 1, 'order' => 3],
            ]],
            ['name' => 'Marketing', 'color' => '#ef4444', 'order' => 4, 'positions' => [
                ['name' => 'Marketing Manager', 'level' => 0, 'order' => 1],
                ['name' => 'Staff Marketing', 'level' => 1, 'order' => 2],
            ]],
            ['name' => 'IT', 'color' => '#8b5cf6', 'order' => 5, 'positions' => [
                ['name' => 'IT Manager', 'level' => 0, 'order' => 1],
                ['name' => 'Staff IT', 'level' => 1, 'order' => 2],
                ['name' => 'Developer', 'level' => 1, 'order' => 3],
            ]],
        ];

        foreach ($divisions as $divData) {
            $positions = $divData['positions'];
            unset($divData['positions']);
            $divData['description'] = 'Divisi ' . $divData['name'];

            $division = Division::create($divData);

            // Create root positions first
            $rootPositions = [];
            foreach ($positions as $pos) {
                if ($pos['level'] === 0) {
                    $created = Position::create([
                        'division_id' => $division->id,
                        'name'        => $pos['name'],
                        'level'       => 0,
                        'order'       => $pos['order'],
                        'parent_id'   => null,
                    ]);
                    $rootPositions[] = $created;
                }
            }

            // Create sub positions
            $parentId = count($rootPositions) > 0 ? $rootPositions[0]->id : null;
            foreach ($positions as $pos) {
                if ($pos['level'] > 0) {
                    Position::create([
                        'division_id' => $division->id,
                        'name'        => $pos['name'],
                        'level'       => $pos['level'],
                        'order'       => $pos['order'],
                        'parent_id'   => $parentId,
                    ]);
                }
            }
        }
    }
}
