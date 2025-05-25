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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('faculty_id')->references('id')->on('faculty');
            $table->foreignId('major_id')->references('id')->on('major');
        });

        Schema::table('major', function (Blueprint $table) {
            $table->foreignId('faculty_id')->references('id')->on('faculty');
        });

        Schema::table('event_registration_response', function (Blueprint $table) {
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('event_registration_id')->references('id')->on('event_registration');
        });

        Schema::table('event_registration_question', function (Blueprint $table) {
            $table->foreignId('event_registration_id')->references('id')->on('event_registration');
        });

        Schema::table('event_registration', function (Blueprint $table) {
            $table->foreignId('event_id')->references('id')->on('event');
        });

        Schema::table('event_registration_role', function (Blueprint $table) {
            $table->foreignId('event_registration_id')->references('id')->on('event_registration');
            $table->foreignId('event_role_id')->references('id')->on('event_role');
        });

        Schema::table('event_role_permission', function (Blueprint $table) {
            $table->foreignId('event_permission_id')->references('id')->on('event_permission');
            $table->foreignId('event_role_id')->references('id')->on('event_role');
        });

        Schema::table('event_role', function (Blueprint $table) {
            $table->foreignId('event_id')->references('id')->on('event');
            $table->foreignId('detail_skp_id')->nullable()->references('id')->on('detail_skp');
        });

        Schema::table('certificate', function (Blueprint $table) {
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('event_id')->references('id')->on('event');
            $table->foreignId('event_role_id')->references('id')->on('event_role');
        });

        Schema::table('surat_tugas', function (Blueprint $table) {
            $table->foreignId('event_id')->references('id')->on('event');
        });

        Schema::table('invitation', function (Blueprint $table) {
            $table->foreignId('event_id')->references('id')->on('event');
            $table->foreignId('event_role_id')->references('id')->on('event_role');
            $table->foreignId('recipient_id')->references('id')->on('users');
        });

        Schema::table('event_user', function (Blueprint $table) {
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('event_id')->references('id')->on('event');
            $table->foreignId('event_role_id')->references('id')->on('event_role');
        });

        Schema::table('event_user', function (Blueprint $table) {
            $table->foreignId('major_id')->references('id')->on('major');
            $table->foreignId('faculty_id')->references('id')->on('faculty');
        });

        Schema::table('event_tag', function (Blueprint $table) {
            $table->foreignId('tag_id')->references('id')->on('tag');
            $table->foreignId('event_id')->references('id')->on('event');
        });

        Schema::table('event', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->references('id')->on('event');
            $table->foreignId('major_id')->references('id')->on('major');
            $table->foreignId('faculty_id')->references('id')->on('faculty');
        });

        Schema::table('contact_person', function (Blueprint $table) {
            $table->foreignId('event_id')->references('id')->on('event');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
