<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('available_fakulti_bahagian', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->boolean('additional_space_2')->default(true);
            $table->boolean('additional_cawangan')->default(false);
            $table->boolean('fail_am')->default(true);
            $table->boolean('fail_sulit')->default(true);
            $table->boolean('fail_pelajar')->default(true);
            $table->boolean('fail_staff')->default(true);
            $table->boolean('fail_akademik')->default(true);
            $table->boolean('fail_pentadbiran')->default(true);
            $table->boolean('student_id')->default(true);
            $table->timestamps();
        });

        // Seed from existing distinct users.fakulti_bahagian values (uppercased)
        $existing = DB::table('users')
            ->whereNotNull('fakulti_bahagian')
            ->where('fakulti_bahagian', '!=', '')
            ->pluck('fakulti_bahagian')
            ->map(fn($v) => strtoupper(trim($v)))
            ->unique()
            ->values();

        $now = now();
        foreach ($existing as $nama) {
            DB::table('available_fakulti_bahagian')->insertOrIgnore([
                'nama' => $nama,
                'additional_space_2' => true,
                'additional_cawangan' => false,
                'fail_am' => true,
                'fail_sulit' => true,
                'fail_pelajar' => true,
                'fail_staff' => true,
                'fail_akademik' => true,
                'fail_pentadbiran' => true,
                'student_id' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('available_fakulti_bahagian');
    }
};
