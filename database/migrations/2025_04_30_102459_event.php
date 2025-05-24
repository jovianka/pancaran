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
            $table->string('poster');
            $table->string('event_level');
            $table->text('requirements');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('job_description'); // file
            $table->timestamps();
            $table->foreignId('parent_id')->nullable()->constrained('event')->nullOnDelete();//parent event
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
