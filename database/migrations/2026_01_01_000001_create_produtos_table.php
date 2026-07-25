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
        Schema::create('produtos', function (Blueprint $table) {
            $table->uuid('idt_produto')->primary();
            $table->string('nom_produto');
            $table->string('tip_tema');
            $table->string('tip_superintendencia')->default('suncf');
            $table->string('cod_servico')->nullable();
            $table->string('cod_produto')->nullable();
            $table->string('tip_produto');
            $table->string('nom_gestor');
            $table->string('nom_substituto')->nullable();
            $table->string('eml_responsavel');
            $table->string('url_loja')->nullable();
            $table->string('url_central_ajuda')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};
