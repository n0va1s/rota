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
        Schema::create('artefatos', function (Blueprint $table) {
            $table->uuid('idt_artefato')->primary();
            $table->foreignUuid('idt_necessidade')->constrained('necessidades', 'idt_necessidade')->cascadeOnDelete();
            $table->string('tip_categoria');
            $table->string('tip_acao');
            $table->unsignedInteger('qtd_itens')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artefatos');
    }
};
