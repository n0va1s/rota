<?php

namespace App\Models;

use App\Enums\TipoCriterio;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProdutoSatisfacao extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'produto_satisfacao';

    protected $primaryKey = 'idt_satisfacao';

    protected $fillable = [
        'idt_produto',
        'idt_usuario',
        'tip_criterio',
        'val_nota',
        'txt_frustracao',
        'txt_sugestao',
    ];

    protected function casts(): array
    {
        return [
            'tip_criterio' => TipoCriterio::class,
            'val_nota' => 'integer',
        ];
    }

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class, 'idt_produto', 'idt_produto');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idt_usuario', 'id');
    }
}
