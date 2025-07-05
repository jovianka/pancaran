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
        Schema::table('certificate', function (Blueprint $table) {
            $table->foreignId('detail_skp_id')->nullable()->constrained('detail_skp')->onDelete('set null');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificate', function (Blueprint $table) {
            // Coba hapus foreign key jika ada, lalu kolomnya
            if (Schema::hasColumn('certificate', 'detail_skp_id')) {
                $table->dropForeign(['detail_skp_id']); // hanya jalan jika constraint dibuat otomatis
                $table->dropColumn('detail_skp_id');
            }
        });
    }       
};
