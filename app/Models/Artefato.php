<?php

namespace App\Models;

use App\Enums\AcaoArtefato;
use App\Enums\CategoriaArtefato;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Artefato extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'artefatos';

    protected $primaryKey = 'idt_artefato';

    protected $fillable = [
        'idt_necessidade',
        'tip_categoria',
        'tip_acao',
        'qtd_itens',
    ];

    protected function casts(): array
    {
        return [
            'tip_categoria' => CategoriaArtefato::class,
            'tip_acao' => AcaoArtefato::class,
            'qtd_itens' => 'integer',
        ];
    }

    public function necessidade(): BelongsTo
    {
        return $this->belongsTo(Necessidade::class, 'idt_necessidade', 'idt_necessidade');
    }

    protected function pontos(): Attribute
    {
        return Attribute::make(
            get: function () {
                $categoriaKey = $this->tip_categoria instanceof CategoriaArtefato ? $this->tip_categoria->value : $this->tip_categoria;
                $acaoKey = $this->tip_acao instanceof AcaoArtefato ? $this->tip_acao->value : $this->tip_acao;

                $peso = config("artefatos.pesos.{$categoriaKey}.{$acaoKey}", 0);

                return $this->qtd_itens * $peso;
            }
        );
    }
}
