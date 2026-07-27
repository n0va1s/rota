<?php

namespace App\Models;

use App\Enums\StatusNecessidade;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Necessidade extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'necessidades';

    protected $primaryKey = 'idt_necessidade';

    protected $fillable = [
        'idt_produto',
        'des_necessidade',
        'txt_descricao',
        'idt_solicitante',
        'tip_status',
        'ind_aprovado',
        'ind_nova_oferta',
        'ind_diferenciacao',
        'ind_novos_clientes',
        'ind_reduz_custo',
        'ind_desoneracao',
        'ind_urgente',
        'ind_roi_alinhado',
        'txt_parecer_gestor',
        'usu_inclusao',
        'usu_alteracao',
    ];

    protected function casts(): array
    {
        return [
            'tip_status' => StatusNecessidade::class,
            'ind_aprovado' => 'boolean',
            'ind_nova_oferta' => 'boolean',
            'ind_diferenciacao' => 'boolean',
            'ind_novos_clientes' => 'boolean',
            'ind_reduz_custo' => 'boolean',
            'ind_desoneracao' => 'boolean',
            'ind_urgente' => 'boolean',
            'ind_roi_alinhado' => 'boolean',
        ];
    }

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class, 'idt_produto', 'idt_produto');
    }

    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idt_solicitante', 'id');
    }

    public function usuarioInclusao(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usu_inclusao', 'id');
    }

    public function usuarioAlteracao(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usu_alteracao', 'id');
    }

    public function artefatos(): HasMany
    {
        return $this->hasMany(Artefato::class, 'idt_necessidade', 'idt_necessidade');
    }

    public function votos(): HasMany
    {
        return $this->hasMany(Voto::class, 'idt_necessidade', 'idt_necessidade');
    }

    protected function esforcoTotal(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->artefatos->sum(fn (Artefato $artefato) => $artefato->pontos)
        );
    }
}
