<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('no_rujukan_id')->constrained('no_rujukan')->onDelete('cascade');
            $table->integer('jilid');               // positive int, unique per no_rujukan
            $table->date('tarikh_pertama');          // compulsory
            $table->date('tarikh_akhir')->nullable();
            $table->date('tarikh_tutup')->nullable();
            $table->string('kotak')->nullable();     // no letters
            $table->string('person_in_charge');      // inherited from current user
            $table->unique(['no_rujukan_id', 'jilid']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fail');
    }
};
