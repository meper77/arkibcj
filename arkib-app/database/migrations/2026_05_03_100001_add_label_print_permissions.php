<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('available_fakulti_bahagian', function (Blueprint $table) {
            $table->boolean('label_pentadbiran')->default(true)->after('borang_pemisahan');
            $table->boolean('label_staff')->default(true)->after('label_pentadbiran');
            $table->boolean('label_pelajar')->default(true)->after('label_staff');
        });
    }

    public function down(): void
    {
        Schema::table('available_fakulti_bahagian', function (Blueprint $table) {
            $table->dropColumn(['label_pentadbiran', 'label_staff', 'label_pelajar']);
        });
    }
};
