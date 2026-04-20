<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('no_rujukan', function (Blueprint $table) {
            $table->id();
            $table->integer('siri');                   // positive int
            $table->string('kampus')->default('UiTM'); // uppercase
            $table->string('kod_bahagian');            // uppercase
            $table->string('nombor_fail');             // no letters
            $table->string('perkara');                 // uppercase
            $table->boolean('additional_space')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('no_rujukan');
    }
};
