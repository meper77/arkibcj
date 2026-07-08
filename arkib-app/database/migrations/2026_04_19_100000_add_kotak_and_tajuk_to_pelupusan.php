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
        // Order matters on MySQL: drop the old FK, make the column nullable,
        // THEN add the SET NULL FK (a SET NULL FK on a NOT NULL column errors
        // with errno 150). SQLite is lax about this; MySQL is not.
        Schema::table('pelupusan', function (Blueprint $table) {
            $table->dropForeign(['pemisahan_id']);
        });

        Schema::table('pelupusan', function (Blueprint $table) {
            $table->unsignedBigInteger('pemisahan_id')->nullable()->change();
        });

        Schema::table('pelupusan', function (Blueprint $table) {
            $table->foreign('pemisahan_id')
                ->references('id')->on('pemisahan')
                ->onDelete('set null');
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
