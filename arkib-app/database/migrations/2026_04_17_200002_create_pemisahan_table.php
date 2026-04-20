<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemisahan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fail_id')->constrained('fail')->onDelete('cascade');
            $table->date('tarikh_pemisahan')->nullable();
            $table->string('tujuan_pemisahan')->nullable();
            $table->string('person_in_charge')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemisahan');
    }
};
