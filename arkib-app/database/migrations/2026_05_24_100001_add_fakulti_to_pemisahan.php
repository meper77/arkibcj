<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemisahan', function (Blueprint $table) {
            $table->foreignId('fakulti_bahagian_id')
                ->nullable()
                ->after('fail_id')
                ->constrained('available_fakulti_bahagian')
                ->nullOnDelete();
        });

        DB::table('pemisahan')
            ->whereNull('fakulti_bahagian_id')
            ->whereNotNull('fail_id')
            ->update([
                'fakulti_bahagian_id' => DB::raw('(SELECT fakulti_bahagian_id FROM fail WHERE fail.id = pemisahan.fail_id)'),
            ]);
    }

    public function down(): void
    {
        Schema::table('pemisahan', function (Blueprint $table) {
            $table->dropConstrainedForeignId('fakulti_bahagian_id');
        });
    }
};
