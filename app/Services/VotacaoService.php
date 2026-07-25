<?php

namespace App\Services;

use App\Models\Necessidade;
use App\Models\User;
use App\Models\Voto;
use App\Traits\LogContext;
use InvalidArgumentException;

class VotacaoService
{
    use LogContext;

    /**
     * Alterna (toggle) o voto do usuário para uma necessidade aprovada.
     */
    public function votar(User $user, Necessidade $necessidade): bool
    {
        if (! $necessidade->ind_aprovado) {
            $this->logWithContext('warning', 'Tentativa de voto em necessidade não aprovada', [
                'necessidade_id' => $necessidade->idt_necessidade,
            ]);

            throw new InvalidArgumentException('Apenas necessidades aprovadas pelo gestor podem receber votos.');
        }

        $votoExistente = Voto::where('idt_necessidade', $necessidade->idt_necessidade)
            ->where('idt_usuario', $user->id)
            ->first();

        if ($votoExistente) {
            $votoExistente->delete();

            $this->logWithContext('notice', 'Voto removido com sucesso', [
                'necessidade_id' => $necessidade->idt_necessidade,
            ]);

            return false; // Voto removido
        }

        Voto::create([
            'idt_necessidade' => $necessidade->idt_necessidade,
            'idt_usuario' => $user->id,
        ]);

        $this->logWithContext('notice', 'Voto registrado com sucesso', [
            'necessidade_id' => $necessidade->idt_necessidade,
        ]);

        return true; // Voto computado
    }

    /**
     * Registra ou atualiza um comentário do usuário para uma necessidade aprovada.
     */
    public function comentar(User $user, Necessidade $necessidade, string $comentario): Voto
    {
        if (! $necessidade->ind_aprovado) {
            throw new InvalidArgumentException('Apenas necessidades aprovadas pelo gestor podem receber comentários.');
        }

        $voto = Voto::updateOrCreate(
            [
                'idt_necessidade' => $necessidade->idt_necessidade,
                'idt_usuario' => $user->id,
            ],
            [
                'txt_comentario' => trim($comentario),
            ]
        );

        $this->logWithContext('notice', 'Comentário salvo na necessidade', [
            'necessidade_id' => $necessidade->idt_necessidade,
        ]);

        return $voto;
    }
}
