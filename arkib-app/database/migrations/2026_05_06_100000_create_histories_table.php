<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name')->nullable();
            $table->foreignId('fakulti_bahagian_id')->nullable()->constrained('available_fakulti_bahagian')->nullOnDelete();
            $table->string('action');
            $table->string('entity_type')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['fakulti_bahagian_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('histories');
    }
};
