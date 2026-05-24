<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Remove pelupusan rows that were left orphaned by earlier fail deletions:
     * never disposed (lupus_at NULL) and detached from any pemisahan
     * (pemisahan_id NULL). These are invisible in the UI and carry no history.
     */
    public function up(): void
    {
        DB::table('pelupusan')
            ->whereNull('pemisahan_id')
            ->whereNull('lupus_at')
            ->delete();
    }

    public function down(): void
    {
        // Irreversible cleanup; nothing to restore.
    }
};
