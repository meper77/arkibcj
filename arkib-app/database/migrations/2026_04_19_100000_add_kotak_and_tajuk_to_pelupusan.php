<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelupusan', function (Blueprint $table) {
            // Snapshot columns so "Selepas pelupusan" can render after the
            // underlying Fail row is deleted.
            $table->string('kotak')->nullable()->after('pemisahan_id');
            $table->string('tajuk_fail')->nullable()->after('kotak');
        });

        // Change pelupusan.pemisahan_id FK to SET NULL so deleting the Fail
        // (which cascade-deletes pemisahan) does not cascade-delete pelupusan.
        Schema::table('pelupusan', function (Blueprint $table) {
            $table->dropForeign(['pemisahan_id']);
            $table->foreign('pemisahan_id')
                ->references('id')->on('pemisahan')
                ->onDelete('set null');
        });

        // Make pemisahan_id nullable so SET NULL works.
        Schema::table('pelupusan', function (Blueprint $table) {
            $table->unsignedBigInteger('pemisahan_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pelupusan', function (Blueprint $table) {
            $table->dropForeign(['pemisahan_id']);
            $table->foreign('pemisahan_id')
                ->references('id')->on('pemisahan')
                ->onDelete('cascade');
            $table->unsignedBigInteger('pemisahan_id')->nullable(false)->change();
            $table->dropColumn(['kotak', 'tajuk_fail']);
        });
    }
};
