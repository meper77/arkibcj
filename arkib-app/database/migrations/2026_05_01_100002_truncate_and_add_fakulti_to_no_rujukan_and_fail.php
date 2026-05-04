<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('pelupusan')->delete();
        DB::table('pemisahan')->delete();
        DB::table('fail')->delete();
        DB::table('no_rujukan')->delete();
        Schema::enableForeignKeyConstraints();

        Schema::table('no_rujukan', function (Blueprint $table) {
            $table->foreignId('fakulti_bahagian_id')
                ->constrained('available_fakulti_bahagian')
                ->cascadeOnDelete();
        });

        Schema::table('fail', function (Blueprint $table) {
            $table->foreignId('fakulti_bahagian_id')
                ->constrained('available_fakulti_bahagian')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('no_rujukan', function (Blueprint $table) {
            $table->dropConstrainedForeignId('fakulti_bahagian_id');
        });
        Schema::table('fail', function (Blueprint $table) {
            $table->dropConstrainedForeignId('fakulti_bahagian_id');
        });
    }
};
