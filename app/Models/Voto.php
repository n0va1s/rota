<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voto extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'votos';

    protected $primaryKey = 'idt_voto';

    protected $fillable = [
        'idt_necessidade',
        'idt_usuario',
        'txt_comentario',
    ];

    public function necessidade(): BelongsTo
    {
        return $this->belongsTo(Necessidade::class, 'idt_necessidade', 'idt_necessidade');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idt_usuario', 'id');
    }
}
