<?php

namespace App\Services;

use App\Enums\TipoCriterio;
use App\Models\Produto;
use App\Models\ProdutoSatisfacao;
use App\Models\User;
use App\Traits\LogContext;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class SatisfacaoService
{
    use LogContext;

    /**
     * Registra ou atualiza as avaliações de satisfação de um usuário para determinado produto.
     *
     * @param  array<int, array{criterio: string|TipoCriterio, nota: int}>  $avaliacoes
     */
    public function registrarAvaliacao(
        User $user,
        Produto $produto,
        array $avaliacoes,
        ?string $txtFrustracao = null,
        ?string $txtSugestao = null
    ): Collection {
        if (empty($avaliacoes)) {
            throw new InvalidArgumentException('Ao menos uma avaliação de critério deve ser fornecida.');
        }

        $temNotaBaixa = false;

        foreach ($avaliacoes as $item) {
            $criterio = $item['criterio'] instanceof TipoCriterio
                ? $item['criterio']
                : TipoCriterio::from($item['criterio']);

            $nota = (int) $item['nota'];

            if ($criterio->isBaixaNota($nota)) {
                $temNotaBaixa = true;
            }
        }

        if ($temNotaBaixa && empty(trim((string) $txtFrustracao))) {
            throw new InvalidArgumentException('Para avaliações com nota baixa, é obrigatório descrever o problema ou frustração ocorrido.');
        }

        $registrosSalvos = collect();

        foreach ($avaliacoes as $item) {
            $criterio = $item['criterio'] instanceof TipoCriterio
                ? $item['criterio']
                : TipoCriterio::from($item['criterio']);

            $registro = ProdutoSatisfacao::updateOrCreate(
                [
                    'idt_produto' => $produto->idt_produto,
                    'idt_usuario' => $user->id,
                    'tip_criterio' => $criterio->value,
                ],
                [
                    'val_nota' => (int) $item['nota'],
                    'txt_frustracao' => $temNotaBaixa ? trim((string) $txtFrustracao) : null,
                    'txt_sugestao' => ! empty(trim((string) $txtSugestao)) ? trim((string) $txtSugestao) : null,
                ]
            );

            $registrosSalvos->push($registro);
        }

        $this->logWithContext('notice', 'Pesquisa de satisfação registrada', [
            'produto_id' => $produto->idt_produto,
            'qtd_criterios' => count($avaliacoes),
        ]);

        return $registrosSalvos;
    }
}
