<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('available_fakulti_bahagian', function (Blueprint $table) {
            $table->boolean('additional_space_1')->default(false)->after('additional_space_2');
        });
    }

    public function down(): void
    {
        Schema::table('available_fakulti_bahagian', function (Blueprint $table) {
            $table->dropColumn('additional_space_1');
        });
    }
};
