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
        Schema::create('necessidades', function (Blueprint $table) {
            $table->uuid('idt_necessidade')->primary();
            $table->foreignUuid('idt_produto')->constrained('produtos', 'idt_produto')->cascadeOnDelete();
            $table->string('des_necessidade');
            $table->text('txt_descricao');
            $table->foreignUuid('idt_solicitante')->constrained('users', 'id')->cascadeOnDelete();
            $table->string('tip_status')->default('em_analise');
            $table->boolean('ind_aprovado')->default(false);
            $table->foreignUuid('usu_inclusao')->constrained('users', 'id')->cascadeOnDelete();
            $table->foreignUuid('usu_alteracao')->nullable()->constrained('users', 'id')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('necessidades');
    }
};
