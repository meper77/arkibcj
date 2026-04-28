<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('no_rujukan', function (Blueprint $table) {
            $table->string('deskripsi')->nullable()->after('perkara');
        });
    }

    public function down(): void
    {
        Schema::table('no_rujukan', function (Blueprint $table) {
            $table->dropColumn('deskripsi');
        });
    }
};
