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
            $table->foreignUuid('idt_solicitante')->constrained('users', 'id')->cascadeOnDelete();
            $table->foreignUuid('idt_produto')->constrained('produtos', 'idt_produto')->cascadeOnDelete();
            $table->string('des_necessidade');
            $table->text('txt_descricao');
            $table->string('tip_status')->default('em_analise');
            $table->boolean('ind_aprovado')->default(false);
            $table->boolean('ind_nova_oferta')->default(false);
            $table->boolean('ind_diferenciacao')->default(false);
            $table->boolean('ind_novos_clientes')->default(false);
            $table->boolean('ind_reduz_custo')->default(false);
            $table->boolean('ind_desoneracao')->default(false);
            $table->boolean('ind_urgente')->default(false);
            $table->boolean('ind_roi_alinhado')->default(false);
            $table->text('txt_parecer_gestor')->nullable();
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
