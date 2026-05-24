<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('no_rujukan', function (Blueprint $table) {
            $table->dropUnique('no_rujukan_composite_unique');
            $table->unique(
                ['siri', 'kampus', 'kod_bahagian', 'nombor_fail', 'fakulti_bahagian_id'],
                'no_rujukan_composite_unique',
            );
        });
    }

    public function down(): void
    {
        Schema::table('no_rujukan', function (Blueprint $table) {
            $table->dropUnique('no_rujukan_composite_unique');
            $table->unique(
                ['siri', 'kampus', 'kod_bahagian', 'nombor_fail'],
                'no_rujukan_composite_unique',
            );
        });
    }
};
