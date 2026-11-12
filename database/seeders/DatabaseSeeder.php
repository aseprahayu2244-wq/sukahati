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
        User::firstOrCreate(
            ['email' => 'superadmin@sukhati.com'],
            [
                'name'      => 'Super Administrator',
                'username'  => 'superadmin',
                'password'  => Hash::make('password'),
                'role'      => 'super_admin',
                'jabatan'   => 'Super Administrator',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@sukhati.com'],
            [
                'name'      => 'Admin HRD',
                'username'  => 'admin',
                'password'  => Hash::make('password'),
                'role'      => 'admin',
                'jabatan'   => 'Administrator HRD',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'anggota@sukhati.com'],
            [
                'name'      => 'Staff Anggota',
                'username'  => 'anggota',
                'password'  => Hash::make('password'),
                'role'      => 'anggota',
                'jabatan'   => 'Staff Umum',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'anakiniy18@gmail.com'],
            [
                'name'      => 'hirara',
                'username'  => 'hirara_super',
                'password'  => Hash::make('password'),
                'role'      => 'super_admin',
                'jabatan'   => 'Super Administrator',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'pepi@gmail.com'],
            [
                'name'      => 'PEPI',
                'username'  => 'pepi',
                'password'  => Hash::make('password'),
                'role'      => 'anggota',
                'jabatan'   => 'Staff',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'anakiniy8@gmail.com'],
            [
                'name'      => 'hirara',
                'username'  => 'hirara_anggota',
                'password'  => Hash::make('password'),
                'role'      => 'anggota',
                'jabatan'   => 'Staff',
                'is_active' => true,
            ]
        );

        // --- BODs ---------------------------------------------------
        $bodCoo = Bod::firstOrCreate(
            ['name' => 'COO'],
            [
                'description' => 'Chief Operating Officer',
                'color'       => '#3b82f6',
                'order'       => 1,
            ]
        );

        $bodCbd = Bod::firstOrCreate(
            ['name' => 'CBD'],
            [
                'description' => 'Chief Business Development',
                'color'       => '#f73b3b',
                'order'       => 2,
            ]
        );

        $bodCfo = Bod::firstOrCreate(
            ['name' => 'CFO'],
            [
                'description' => 'Chief Financial Officer',
                'color'       => '#3bf751',
                'order'       => 3,
            ]
        );

        // --- Departments COO ---
        $cooDepts = ['SALES', 'BISDEV', 'OPRASIONAL', 'ARMADA', 'BENGKEL', 'TEKNISI', 'SECRETARY'];
        $order = 1;
        foreach ($cooDepts as $dName) {
            Division::firstOrCreate(
                ['bod_id' => $bodCoo->id, 'name' => $dName],
                ['color' => '#3b82f6', 'order' => $order++]
            );
        }

        // --- Departments CBD ---
        $cbdDepts = ['HRD', 'GA', 'SPI', 'QA QC', 'KREY'];
        foreach ($cbdDepts as $dName) {
            Division::firstOrCreate(
                ['bod_id' => $bodCbd->id, 'name' => $dName],
                ['color' => '#f73b3b', 'order' => $order++]
            );
        }

        // --- Departments CFO ---
        $cfoDepts = ['KEUANGAN', 'AKUNTING', 'PURCHESING'];
        foreach ($cfoDepts as $dName) {
            Division::firstOrCreate(
                ['bod_id' => $bodCfo->id, 'name' => $dName],
                ['color' => '#3bf751', 'order' => $order++]
            );
        }
    }
}
