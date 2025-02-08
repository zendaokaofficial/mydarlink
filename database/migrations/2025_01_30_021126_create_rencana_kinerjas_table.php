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
        Schema::create('rencana_kinerjas', function (Blueprint $table) {
            $table->id();
            $table->string('rencana_kinerja'); // Judul
            $table->text('description')->nullable(); // Deskripsi
            $table->dateTime('start_at'); // Waktu Mulai
            $table->dateTime('end_at'); // Waktu Selesai
            $table->string('tempat'); // Tempat
            $table->string('tempat_lainnya')->nullable(); // Tempat Lainnya
            $table->string('kategori'); // Kategori
            $table->string('target'); // Target
            $table->string('satuan'); // Satuan
            $table->string('realisasi')->nullable(); // Realisasi
            $table->string('daftar_hadir')->nullable(); // Daftar Hadir
            $table->string('rekap_daftar_hadir')->nullable(); // Rekap Daftar Hadir
            $table->string('link_materi')->nullable(); // Link Materi
            $table->string('notulensi')->nullable(); // Notulensi
            $table->foreignId('proyek_id')->constrained('proyeks')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rencana_kinerjas');
    }
};
