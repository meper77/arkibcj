<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fail', function (Blueprint $table) {
            $table->string('jenis_fail', 8);
            $table->string('kategori', 8)->nullable();
            $table->string('sub_kategori', 12)->nullable();
            $table->string('student_id', 32)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('fail', function (Blueprint $table) {
            $table->dropColumn(['jenis_fail', 'kategori', 'sub_kategori', 'student_id']);
        });
    }
};
