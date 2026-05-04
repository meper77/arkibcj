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
            $table->unsignedBigInteger('no_rujukan_id')->nullable()->after('pemisahan_id');
            $table->unsignedInteger('jilid')->nullable()->after('no_rujukan_id');
        });

        // Backfill existing LUPUS rows from their pemisahan.fail (if still linked).
        $rows = DB::table('pelupusan')
            ->whereNotNull('pemisahan_id')
            ->whereNotNull('lupus_at')
            ->get();
        foreach ($rows as $row) {
            $fail = DB::table('pemisahan')
                ->join('fail', 'pemisahan.fail_id', '=', 'fail.id')
                ->where('pemisahan.id', $row->pemisahan_id)
                ->select('fail.no_rujukan_id', 'fail.jilid')
                ->first();
            if ($fail) {
                DB::table('pelupusan')->where('id', $row->id)->update([
                    'no_rujukan_id' => $fail->no_rujukan_id,
                    'jilid' => $fail->jilid,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('pelupusan', function (Blueprint $table) {
            $table->dropColumn(['no_rujukan_id', 'jilid']);
        });
    }
};
