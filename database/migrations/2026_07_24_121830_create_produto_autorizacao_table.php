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
        Schema::create('produto_autorizacao', function (Blueprint $table) {
            $table->uuid('idt_produto');
            $table->uuid('user_id');
            $table->boolean('ind_gestor')->default(false)->comment('Determina se é o gestor (ou substituto) principal do produto');
            $table->timestamps();

            $table->primary(['idt_produto', 'user_id']);
            $table->foreign('idt_produto')->references('idt_produto')->on('produtos')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produto_autorizacao');
    }
};
