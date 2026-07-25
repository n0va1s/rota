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
        Schema::create('votos', function (Blueprint $table) {
            $table->uuid('idt_voto')->primary();
            $table->foreignUuid('idt_necessidade')->constrained('necessidades', 'idt_necessidade')->cascadeOnDelete();
            $table->foreignUuid('idt_usuario')->constrained('users', 'id')->cascadeOnDelete();
            $table->text('txt_comentario')->nullable();
            $table->timestamps();

            $table->unique(['idt_necessidade', 'idt_usuario']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votos');
    }
};
