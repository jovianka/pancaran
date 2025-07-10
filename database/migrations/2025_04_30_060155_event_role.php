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
        Schema::create('event_role', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('quota')->default(0);
            $table->jsonb('certificate_schema')->nullable();
            $table->string('certificate_basepdf')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_role');
    }
};
