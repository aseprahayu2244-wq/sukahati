<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Safely convert role enum if not already
        \DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL DEFAULT 'anggota'");
        \DB::statement("UPDATE users SET role = 'super_admin' WHERE role = 'ketua'");
        \DB::statement("UPDATE users SET role = 'anggota' WHERE role = 'user_umum'");
        \DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'admin', 'anggota') NOT NULL DEFAULT 'anggota'");

        // 2. Add extra profile fields if not exists
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->unique()->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'jabatan')) {
                $table->string('jabatan')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('jabatan');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('avatar');
            }
        });

        // 3. Populate unique username for existing users
        $users = \DB::table('users')->whereNull('username')->orWhere('username', '')->get();
        foreach ($users as $u) {
            $base = preg_replace('/[^a-zA-Z0-9_]/', '', explode('@', $u->email)[0]);
            $username = $base ?: 'user_' . $u->id;
            // Ensure unique
            $count = \DB::table('users')->where('username', $username)->where('id', '!=', $u->id)->count();
            if ($count > 0) {
                $username = $username . '_' . $u->id;
            }
            \DB::table('users')->where('id', $u->id)->update(['username' => $username]);
        }
    }

    public function down(): void
    {
        \DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL DEFAULT 'user_umum'");
        \DB::statement("UPDATE users SET role = 'ketua' WHERE role = 'super_admin'");
        \DB::statement("UPDATE users SET role = 'user_umum' WHERE role IN ('admin', 'anggota')");
        \DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('ketua', 'user_umum') NOT NULL DEFAULT 'user_umum'");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'phone', 'jabatan', 'avatar', 'is_active']);
        });
    }
};
