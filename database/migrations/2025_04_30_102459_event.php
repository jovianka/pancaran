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
        Schema::create('event', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('event_level');
            $table->string('poster')->nullable(); // image file
            $table->date('start_date');
            $table->date('end_date');
            $table->string('job_description'); // file
            $table->string('requirements')->nullable();
            $table->enum('status', ['ongoing', 'finished']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event');
    }
};
