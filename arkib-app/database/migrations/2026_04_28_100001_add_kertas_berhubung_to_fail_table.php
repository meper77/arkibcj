<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fail', function (Blueprint $table) {
            $table->foreignId('kertas_berhubung_id')->nullable()->after('jilid')
                ->constrained('fail')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('fail', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kertas_berhubung_id');
        });
    }
};
