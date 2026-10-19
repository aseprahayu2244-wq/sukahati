<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add bod_id to divisions (departments)
        Schema::table('divisions', function (Blueprint $table) {
            $table->foreignId('bod_id')->nullable()->constrained('bods')->onDelete('set null')->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('divisions', function (Blueprint $table) {
            $table->dropForeign(['bod_id']);
            $table->dropColumn('bod_id');
        });
    }
};
