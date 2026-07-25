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
        Schema::create('produto_satisfacao', function (Blueprint $table) {
            $table->uuid('idt_satisfacao')->primary();
            $table->foreignUuid('idt_produto')->constrained('produtos', 'idt_produto')->cascadeOnDelete();
            $table->foreignUuid('idt_usuario')->constrained('users', 'id')->cascadeOnDelete();
            $table->string('tip_criterio');
            $table->integer('val_nota');
            $table->text('txt_frustracao')->nullable();
            $table->text('txt_sugestao')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produto_satisfacao');
    }
};
