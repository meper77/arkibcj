<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('fakulti_bahagian_id')
                ->nullable()
                ->after('cawangan')
                ->constrained('available_fakulti_bahagian')
                ->nullOnDelete();
        });

        // Backfill mapping uppercase name -> id
        $rows = DB::table('available_fakulti_bahagian')->get(['id', 'nama']);
        $map = [];
        foreach ($rows as $r) {
            $map[strtoupper($r->nama)] = $r->id;
        }

        $users = DB::table('users')->whereNotNull('fakulti_bahagian')->get(['id', 'fakulti_bahagian']);
        foreach ($users as $u) {
            $key = strtoupper(trim($u->fakulti_bahagian));
            if (isset($map[$key])) {
                DB::table('users')->where('id', $u->id)->update(['fakulti_bahagian_id' => $map[$key]]);
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('fakulti_bahagian');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('fakulti_bahagian')->nullable()->after('cawangan');
        });

        $rows = DB::table('available_fakulti_bahagian')->get(['id', 'nama']);
        $map = [];
        foreach ($rows as $r) {
            $map[$r->id] = $r->nama;
        }
        $users = DB::table('users')->whereNotNull('fakulti_bahagian_id')->get(['id', 'fakulti_bahagian_id']);
        foreach ($users as $u) {
            if (isset($map[$u->fakulti_bahagian_id])) {
                DB::table('users')->where('id', $u->id)->update(['fakulti_bahagian' => $map[$u->fakulti_bahagian_id]]);
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('fakulti_bahagian_id');
        });
    }
};
