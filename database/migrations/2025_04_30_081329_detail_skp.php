<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detail_skp', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('description');
            $table->string('role');
            $table->string('event_level');
            $table->integer('skp');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_skp');
    }
};
