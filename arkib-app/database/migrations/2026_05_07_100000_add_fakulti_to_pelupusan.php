<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelupusan', function (Blueprint $table) {
            $table->unsignedBigInteger('fakulti_bahagian_id')->nullable()->after('jilid');
        });

        // Backfill from no_rujukan snapshot.
        DB::statement('
            UPDATE pelupusan
            SET fakulti_bahagian_id = (
                SELECT fakulti_bahagian_id FROM no_rujukan WHERE no_rujukan.id = pelupusan.no_rujukan_id
            )
            WHERE no_rujukan_id IS NOT NULL
        ');
    }

    public function down(): void
    {
        Schema::table('pelupusan', function (Blueprint $table) {
            $table->dropColumn('fakulti_bahagian_id');
        });
    }
};
