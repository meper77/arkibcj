<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('available_fakulti_bahagian', function (Blueprint $table) {
            $table->boolean('borang_pemisahan')->default(true)->after('student_id');
        });
    }

    public function down(): void
    {
        Schema::table('available_fakulti_bahagian', function (Blueprint $table) {
            $table->dropColumn('borang_pemisahan');
        });
    }
};
