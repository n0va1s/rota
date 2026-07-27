<?php

namespace Database\Seeders;

use App\Enums\AcaoArtefato;
use App\Enums\CategoriaArtefato;
use App\Enums\StatusNecessidade;
use App\Enums\TipoCriterio;
use App\Enums\TipoRole;
use App\Models\Artefato;
use App\Models\Necessidade;
use App\Models\Produto;
use App\Models\ProdutoSatisfacao;
use App\Models\User;
use App\Models\Voto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Criar usuários principais de teste
        $admin = User::firstOrCreate(
            ['email' => 'joao.novais@serpro.gov.br'],
            ['name' => 'João Paulo Novais', 'password' => Hash::make('password'), 'num_cpf' => '85236250110', 'tip_role' => TipoRole::ADMIN->value]
        );

        $daniele = User::firstOrCreate(
            ['email' => 'daniele.farias@serpro.gov.br'],
            ['name' => 'Daniele Farias', 'password' => Hash::make('password'), 'num_cpf' => '11111111111', 'tip_role' => TipoRole::GESTOR->value]
        );

        $alan = User::firstOrCreate(
            ['email' => 'alan.daniel@serpro.gov.br'],
            ['name' => 'Alan Daniel', 'password' => Hash::make('password'), 'num_cpf' => '22222222222', 'tip_role' => TipoRole::USER->value]
        );

        $marcos = User::firstOrCreate(
            ['email' => 'marcos.vinicius@serpro.gov.br'],
            ['name' => 'Marcos Vinícius', 'password' => Hash::make('password'), 'num_cpf' => '33333333333', 'tip_role' => TipoRole::USER->value]
        );

        $renata = User::firstOrCreate(
            ['email' => 'renata.souza@serpro.gov.br'],
            ['name' => 'Renata Souza', 'password' => Hash::make('password'), 'num_cpf' => '44444444444', 'tip_role' => TipoRole::USER->value]
        );

        // 2. Rodar Seeder de Produtos
        $this->call(ProdutoSeeder::class);

        // 3. Criar Necessidades de Amostragem
        $apiInfracoes = Produto::where('nom_produto', 'like', '%Consulta Online SENATRAN%')->first()
            ?? Produto::where('tip_produto', 'api')->first();

        $painelVeicular = Produto::where('nom_produto', 'like', '%Painel de Inteligência Veicular%')->first()
            ?? Produto::where('tip_produto', 'painel')->first();

        if ($apiInfracoes) {
            $n1 = Necessidade::create([
                'idt_produto' => $apiInfracoes->idt_produto,
                'des_necessidade' => 'Exportação de relatório de infrações em lote',
                'txt_descricao' => 'Permitir exportação em lote dos relatórios de infração para as prefeituras conveniadas, reduzindo o volume de chamados manuais de suporte.',
                'idt_solicitante' => $daniele->id,
                'tip_status' => StatusNecessidade::EM_ANALISE,
                'ind_aprovado' => false,
                'usu_inclusao' => $daniele->id,
            ]);

            Artefato::create(['idt_necessidade' => $n1->idt_necessidade, 'tip_categoria' => CategoriaArtefato::TELA->value, 'tip_acao' => AcaoArtefato::NOVA->value, 'qtd_itens' => 1]);
            Artefato::create(['idt_necessidade' => $n1->idt_necessidade, 'tip_categoria' => CategoriaArtefato::REGRA->value, 'tip_acao' => AcaoArtefato::NOVA->value, 'qtd_itens' => 1]);
            Artefato::create(['idt_necessidade' => $n1->idt_necessidade, 'tip_categoria' => CategoriaArtefato::ENTIDADE->value, 'tip_acao' => AcaoArtefato::NOVA->value, 'qtd_itens' => 1]);
        }

        if ($painelVeicular) {
            $n2 = Necessidade::create([
                'idt_produto' => $painelVeicular->idt_produto,
                'des_necessidade' => 'Painel de indicadores por segmento',
                'txt_descricao' => 'Visão consolidada de indicadores de fiscalização segmentada por município, para apoiar decisões dos gestores regionais.',
                'idt_solicitante' => $alan->id,
                'tip_status' => StatusNecessidade::APROVADA,
                'ind_aprovado' => true,
                'usu_inclusao' => $alan->id,
                'usu_alteracao' => $daniele->id,
            ]);

            Artefato::create(['idt_necessidade' => $n2->idt_necessidade, 'tip_categoria' => CategoriaArtefato::TELA->value, 'tip_acao' => AcaoArtefato::NOVA->value, 'qtd_itens' => 2]);
            Artefato::create(['idt_necessidade' => $n2->idt_necessidade, 'tip_categoria' => CategoriaArtefato::REGRA->value, 'tip_acao' => AcaoArtefato::ALTERACAO->value, 'qtd_itens' => 2]);
            Artefato::create(['idt_necessidade' => $n2->idt_necessidade, 'tip_categoria' => CategoriaArtefato::INTEGRACAO->value, 'tip_acao' => AcaoArtefato::NOVA->value, 'qtd_itens' => 1]);

            // Votos e comentários
            Voto::create(['idt_necessidade' => $n2->idt_necessidade, 'idt_usuario' => $daniele->id, 'txt_comentario' => 'Seria muito útil também exportar em PDF para os relatórios trimestrais.']);
            Voto::create(['idt_necessidade' => $n2->idt_necessidade, 'idt_usuario' => $marcos->id]);
            Voto::create(['idt_necessidade' => $n2->idt_necessidade, 'idt_usuario' => $renata->id]);
        }

        // 4. Seeder de Satisfação de Amostragem
        if ($apiInfracoes) {
            ProdutoSatisfacao::create(['idt_produto' => $apiInfracoes->idt_produto, 'idt_usuario' => $alan->id, 'tip_criterio' => TipoCriterio::CES_FACILIDADE, 'val_nota' => 6]);
            ProdutoSatisfacao::create(['idt_produto' => $apiInfracoes->idt_produto, 'idt_usuario' => $alan->id, 'tip_criterio' => TipoCriterio::CSAT_DOCUMENTACAO, 'val_nota' => 5]);
            ProdutoSatisfacao::create(['idt_produto' => $apiInfracoes->idt_produto, 'idt_usuario' => $alan->id, 'tip_criterio' => TipoCriterio::CSAT_ERROS, 'val_nota' => 4]);
            ProdutoSatisfacao::create(['idt_produto' => $apiInfracoes->idt_produto, 'idt_usuario' => $alan->id, 'tip_criterio' => TipoCriterio::DEV_NPS, 'val_nota' => 9]);
        }
    }
}
