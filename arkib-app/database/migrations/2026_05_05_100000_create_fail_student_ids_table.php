<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fail_student_ids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fail_id')->constrained('fail')->cascadeOnDelete();
            $table->string('student_id', 32);
            $table->timestamps();
            $table->unique(['fail_id', 'student_id']);
            $table->index('student_id');
        });

        // Backfill from existing fail.student_id column.
        $rows = DB::table('fail')->whereNotNull('student_id')->where('student_id', '!=', '')->get(['id', 'student_id']);
        foreach ($rows as $row) {
            DB::table('fail_student_ids')->insert([
                'fail_id' => $row->id,
                'student_id' => $row->student_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('fail', function (Blueprint $table) {
            $table->dropColumn('student_id');
        });
    }

    public function down(): void
    {
        Schema::table('fail', function (Blueprint $table) {
            $table->string('student_id', 32)->nullable();
        });

        // Restore one ID per fail (the first).
        $rows = DB::table('fail_student_ids')->orderBy('id')->get();
        $seen = [];
        foreach ($rows as $row) {
            if (isset($seen[$row->fail_id])) continue;
            DB::table('fail')->where('id', $row->fail_id)->update(['student_id' => $row->student_id]);
            $seen[$row->fail_id] = true;
        }

        Schema::dropIfExists('fail_student_ids');
    }
};
