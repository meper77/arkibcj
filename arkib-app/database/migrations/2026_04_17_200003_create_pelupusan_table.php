<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelupusan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemisahan_id')->constrained('pemisahan')->onDelete('cascade');
            $table->string('status')->default('PENDING'); // PENDING, APPROVE, DECLINE
            $table->string('person_in_charge')->nullable();
            $table->timestamp('lupus_at')->nullable(); // set when lupus action performed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelupusan');
    }
};
