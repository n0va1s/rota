<?php

namespace App\Models;

use App\Enums\TipoProduto;
use App\Enums\TipoSuperintendencia;
use App\Enums\TipoTema;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produto extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'produtos';

    protected $primaryKey = 'idt_produto';

    protected $fillable = [
        'nom_produto',
        'tip_tema',
        'tip_superintendencia',
        'cod_servico',
        'cod_produto',
        'tip_produto',
        'idt_gestor',
        'idt_substituto',
        'eml_responsavel',
        'url_loja',
        'url_central_ajuda',
    ];

    protected function casts(): array
    {
        return [
            'tip_tema' => TipoTema::class,
            'tip_superintendencia' => TipoSuperintendencia::class,
            'tip_produto' => TipoProduto::class,
        ];
    }

    public function gestor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idt_gestor', 'id');
    }

    public function substituto(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idt_substituto', 'id');
    }

    public function necessidades(): HasMany
    {
        return $this->hasMany(Necessidade::class, 'idt_produto', 'idt_produto');
    }

    public function satisfacoes(): HasMany
    {
        return $this->hasMany(ProdutoSatisfacao::class, 'idt_produto', 'idt_produto');
    }

    public function usuariosAutorizados(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'produto_autorizacao', 'idt_produto', 'user_id')
            ->withPivot('ind_gestor')
            ->withTimestamps();
    }
}
