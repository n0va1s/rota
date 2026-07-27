<?php

namespace Database\Seeders;

use App\Enums\TipoProduto;
use App\Enums\TipoRole;
use App\Enums\TipoSuperintendencia;
use App\Enums\TipoTema;
use App\Models\Produto;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProdutoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $produtos = [
            [
                'nom_produto' => 'Arquivos Eletrônicos SENATRAN',
                'tip_tema' => TipoTema::VEICULOS->value,
                'tip_superintendencia' => TipoSuperintendencia::SUNCF->value,
                'cod_servico' => '11552',
                'cod_produto' => null,
                'tip_produto' => TipoProduto::API->value,
                'nom_gestor' => 'Luciano Fernandes',
                'nom_substituto' => 'Antonio Gilberto Meneses',
                'eml_responsavel' => 'luciano.fernandes@serpro.gov.br',
                'url_loja' => 'https://loja.serpro.gov.br/arquivos-eletr%C3%B4nicos-senatran/product/arquivoseletronicossenatran',
                'url_central_ajuda' => null,
            ],
            [
                'nom_produto' => 'CV-e - Comunicação Eletrônica de Venda de Veículos',
                'tip_tema' => TipoTema::VEICULOS->value,
                'tip_superintendencia' => TipoSuperintendencia::SUNCF->value,
                'cod_servico' => '10995',
                'cod_produto' => null,
                'tip_produto' => TipoProduto::API->value,
                'nom_gestor' => 'Maria Cleide Conessa',
                'nom_substituto' => 'Antonio Gilberto Meneses',
                'eml_responsavel' => 'maria.conessa@serpro.gov.br',
                'url_loja' => 'https://loja.serpro.gov.br/comunica%C3%A7%C3%A3o-eletr%C3%B4nica-de-venda-de-ve%C3%ADculos-cve/product/cve',
                'url_central_ajuda' => null,
            ],
            [
                'nom_produto' => 'Consulta Online SENATRAN',
                'tip_tema' => TipoTema::VEICULOS->value,
                'tip_superintendencia' => TipoSuperintendencia::SUNCF->value,
                'cod_servico' => '10394',
                'cod_produto' => null,
                'tip_produto' => TipoProduto::API->value,
                'nom_gestor' => 'Antonio Gilberto Meneses',
                'nom_substituto' => 'Luciano Fernandes',
                'eml_responsavel' => 'antonio.meneses@serpro.gov.br',
                'url_loja' => 'https://loja.serpro.gov.br/consulta-online-senatran/product/consultasenatran',
                'url_central_ajuda' => 'https://centraldeajuda.serpro.gov.br/consultasenatran/',
            ],
            [
                'nom_produto' => 'e-Frotas',
                'tip_tema' => TipoTema::TRANSVERSAL->value,
                'tip_superintendencia' => TipoSuperintendencia::SUNCF->value,
                'cod_servico' => null,
                'cod_produto' => null,
                'tip_produto' => TipoProduto::API->value,
                'nom_gestor' => 'Mara Leniza Souza',
                'nom_substituto' => 'Luciano Fernandes',
                'eml_responsavel' => 'mara.souza@serpro.gov.br',
                'url_loja' => 'https://loja.serpro.gov.br/e-frotas/product/efrotas',
                'url_central_ajuda' => 'https://centraldeajuda.serpro.gov.br/efrotas/',
            ],
            [
                'nom_produto' => 'Emissão de Laudo Toxicológico',
                'tip_tema' => TipoTema::CONDUTORES->value,
                'tip_superintendencia' => TipoSuperintendencia::SUNCF->value,
                'cod_servico' => null,
                'cod_produto' => null,
                'tip_produto' => TipoProduto::API->value,
                'nom_gestor' => 'Fernando Paiva',
                'nom_substituto' => 'Maria Cleide Conessa',
                'eml_responsavel' => 'fernando.paiva@serpro.gov.br',
                'url_loja' => 'https://loja.serpro.gov.br/emiss%C3%A3o-de-laudo-toxicol%C3%B3gico/product/laudotoxicologico',
                'url_central_ajuda' => null,
            ],
            [
                'nom_produto' => 'Emissão CNH/PID',
                'tip_tema' => TipoTema::CONDUTORES->value,
                'tip_superintendencia' => TipoSuperintendencia::SUNCF->value,
                'cod_servico' => null,
                'cod_produto' => null,
                'tip_produto' => TipoProduto::API->value,
                'nom_gestor' => 'Luciano Fernandes',
                'nom_substituto' => 'Não há',
                'eml_responsavel' => 'luciano.fernandes@serpro.gov.br',
                'url_loja' => 'https://loja.serpro.gov.br/emiss%C3%A3o-de-pid/product/emissaopid',
                'url_central_ajuda' => null,
            ],
            [
                'nom_produto' => 'Emplaca - Sistema Nacional de Emplacamento',
                'tip_tema' => TipoTema::VEICULOS->value,
                'tip_superintendencia' => TipoSuperintendencia::SUNCF->value,
                'cod_servico' => '11131',
                'cod_produto' => null,
                'tip_produto' => TipoProduto::API->value,
                'nom_gestor' => 'Maria Cleide Conessa',
                'nom_substituto' => 'Mara Leniza Souza',
                'eml_responsavel' => 'maria.conessa@serpro.gov.br',
                'url_loja' => 'https://loja.serpro.gov.br/emplaca/product/emplaca',
                'url_central_ajuda' => 'https://centraldeajuda.serpro.gov.br/emplaca/',
            ],
            [
                'nom_produto' => 'Extrajud',
                'tip_tema' => TipoTema::VEICULOS->value,
                'tip_superintendencia' => TipoSuperintendencia::SUNCF->value,
                'cod_servico' => null,
                'cod_produto' => null,
                'tip_produto' => TipoProduto::API->value,
                'nom_gestor' => 'Luciano Fernandes',
                'nom_substituto' => 'Não há',
                'eml_responsavel' => 'luciano.fernandes@serpro.gov.br',
                'url_loja' => null,
                'url_central_ajuda' => null,
            ],
            [
                'nom_produto' => 'Painel de Inteligência Veicular',
                'tip_tema' => TipoTema::VEICULOS->value,
                'tip_superintendencia' => TipoSuperintendencia::SUNCF->value,
                'cod_servico' => '11763',
                'cod_produto' => null,
                'tip_produto' => TipoProduto::PAINEL->value,
                'nom_gestor' => 'Daniele Farias',
                'nom_substituto' => 'Alan Daniel',
                'eml_responsavel' => 'daniele.farias@serpro.gov.br',
                'url_loja' => 'https://loja.serpro.gov.br/painel-de-intelig%C3%AAncia-veicular/product/painelveicular',
                'url_central_ajuda' => null,
            ],
            [
                'nom_produto' => 'Painel Toxicológico',
                'tip_tema' => TipoTema::CONDUTORES->value,
                'tip_superintendencia' => TipoSuperintendencia::SUNCF->value,
                'cod_servico' => '11282',
                'cod_produto' => null,
                'tip_produto' => TipoProduto::PAINEL->value,
                'nom_gestor' => 'Daniele Farias',
                'nom_substituto' => 'Alan Daniel',
                'eml_responsavel' => 'daniele.farias@serpro.gov.br',
                'url_loja' => 'https://loja.serpro.gov.br/painel-toxicol%C3%B3gico/product/paineltoxicologico',
                'url_central_ajuda' => null,
            ],
            [
                'nom_produto' => 'Pré-Cadastro Veicular',
                'tip_tema' => TipoTema::VEICULOS->value,
                'tip_superintendencia' => TipoSuperintendencia::SUNCF->value,
                'cod_servico' => '11673',
                'cod_produto' => null,
                'tip_produto' => TipoProduto::API->value,
                'nom_gestor' => 'Luciano Fernandes',
                'nom_substituto' => 'Antonio Gilberto Meneses',
                'eml_responsavel' => 'luciano.fernandes@serpro.gov.br',
                'url_loja' => 'https://loja.serpro.gov.br/pr%C3%A9-cadastro-veicular/product/precadastroveicular',
                'url_central_ajuda' => 'https://centraldeajuda.serpro.gov.br/precadastroveicular/',
            ],
            [
                'nom_produto' => 'Registro e Notificação de RECALL',
                'tip_tema' => TipoTema::VEICULOS->value,
                'tip_superintendencia' => TipoSuperintendencia::SUNCF->value,
                'cod_servico' => null,
                'cod_produto' => null,
                'tip_produto' => TipoProduto::API->value,
                'nom_gestor' => 'Mara Leniza Souza',
                'nom_substituto' => 'Não há',
                'eml_responsavel' => 'mara.souza@serpro.gov.br',
                'url_loja' => 'https://loja.serpro.gov.br/notifica%C3%A7%C3%A3o-e-registro-de-recall/product/recall',
                'url_central_ajuda' => null,
            ],
            [
                'nom_produto' => 'RENAVE - Registro Nacional de Veículos em Estoque',
                'tip_tema' => TipoTema::VEICULOS->value,
                'tip_superintendencia' => TipoSuperintendencia::SUNCF->value,
                'cod_servico' => '10417',
                'cod_produto' => null,
                'tip_produto' => TipoProduto::API->value,
                'nom_gestor' => 'Luciano Fernandes',
                'nom_substituto' => 'Antonio Gilberto Meneses',
                'eml_responsavel' => 'luciano.fernandes@serpro.gov.br',
                'url_loja' => 'https://loja.serpro.gov.br/renave/product/renave',
                'url_central_ajuda' => 'https://centraldeajuda.serpro.gov.br/renave',
            ],
            [
                'nom_produto' => 'SisCSV - Sistema de Certificação de Segurança Veicular',
                'tip_tema' => TipoTema::VEICULOS->value,
                'tip_superintendencia' => TipoSuperintendencia::SUNCF->value,
                'cod_servico' => '10219',
                'cod_produto' => null,
                'tip_produto' => TipoProduto::API->value,
                'nom_gestor' => 'Maria Cleide Conessa',
                'nom_substituto' => 'Antonio Gilberto Meneses',
                'eml_responsavel' => 'maria.conessa@serpro.gov.br',
                'url_loja' => 'https://loja.serpro.gov.br/siscsv-certifica%C3%A7%C3%A3o-de-seguran%C3%A7a-veicular/product/siscsv',
                'url_central_ajuda' => 'https://centraldeajuda.serpro.gov.br/siscsv/',
            ],
            [
                'nom_produto' => 'SisLV',
                'tip_tema' => TipoTema::VEICULOS->value,
                'tip_superintendencia' => TipoSuperintendencia::SUNCF->value,
                'cod_servico' => null,
                'cod_produto' => null,
                'tip_produto' => TipoProduto::API->value,
                'nom_gestor' => 'Maria Cleide Conessa',
                'nom_substituto' => 'Não há',
                'eml_responsavel' => 'maria.conessa@serpro.gov.br',
                'url_loja' => null,
                'url_central_ajuda' => null,
            ],
            [
                'nom_produto' => 'Venda Digital Cartórios',
                'tip_tema' => TipoTema::VEICULOS->value,
                'tip_superintendencia' => TipoSuperintendencia::SUNCF->value,
                'cod_servico' => null,
                'cod_produto' => null,
                'tip_produto' => TipoProduto::API->value,
                'nom_gestor' => 'Antonio Gilberto Meneses',
                'nom_substituto' => 'Maria Cleide Conessa',
                'eml_responsavel' => 'antonio.meneses@serpro.gov.br',
                'url_loja' => 'http://loja.serpro.gov.br/vdcartorios',
                'url_central_ajuda' => 'https://centraldeajuda.serpro.gov.br/vendadigitalcartorios/',
            ],
            [
                'nom_produto' => 'Integra PSP',
                'tip_tema' => TipoTema::TRANSVERSAL->value,
                'tip_superintendencia' => TipoSuperintendencia::SUNCF->value,
                'cod_servico' => null,
                'cod_produto' => null,
                'tip_produto' => TipoProduto::API->value,
                'nom_gestor' => 'Luís Henrique Santos',
                'nom_substituto' => 'Não há',
                'eml_responsavel' => 'luis.santos@serpro.gov.br',
                'url_loja' => 'Não há',
                'url_central_ajuda' => 'Não há',
            ],
            [
                'nom_produto' => 'Suprinav',
                'tip_tema' => TipoTema::TRANSVERSAL->value,
                'tip_superintendencia' => TipoSuperintendencia::SUNCF->value,
                'cod_servico' => null,
                'cod_produto' => null,
                'tip_produto' => TipoProduto::API->value,
                'nom_gestor' => 'Luís Henrique Santos',
                'nom_substituto' => 'Não há',
                'eml_responsavel' => 'luis.santos@serpro.gov.br',
                'url_loja' => 'Não há',
                'url_central_ajuda' => 'Não há',
            ],
            [
                'nom_produto' => 'TIS',
                'tip_tema' => TipoTema::TRANSVERSAL->value,
                'tip_superintendencia' => TipoSuperintendencia::SUNCF->value,
                'cod_servico' => null,
                'cod_produto' => null,
                'tip_produto' => TipoProduto::API->value,
                'nom_gestor' => 'Luís Henrique Santos',
                'nom_substituto' => 'Não há',
                'eml_responsavel' => 'luis.santos@serpro.gov.br',
                'url_loja' => 'Não há',
                'url_central_ajuda' => 'Não há',
            ],
        ];

        foreach ($produtos as $p) {
            $prod = Produto::updateOrCreate(
                ['nom_produto' => $p['nom_produto']],
                $p
            );

            // Import users and link to product autorizacao
            $this->importarUsuarioEAutorizar($prod, $p['nom_gestor'], true);
            $this->importarUsuarioEAutorizar($prod, $p['nom_substituto'], false);
        }
    }

    private function importarUsuarioEAutorizar(Produto $produto, ?string $nome, bool $isGestorPrincipal): void
    {
        if (empty($nome) || mb_strtolower(trim($nome)) === 'não há') {
            return;
        }

        $nome = trim($nome);
        $parts = explode(' ', strtolower($nome));
        $email = $parts[0].'.'.end($parts).'@serpro.gov.br';

        // Evitar recriar CPFs para usuários já existentes
        $user = User::where('email', $email)->first();
        if (! $user) {
            $user = User::create([
                'name' => $nome,
                'email' => $email,
                'password' => Hash::make('password'),
                'num_cpf' => fake()->numerify('###########'),
                'tip_role' => TipoRole::GESTOR->value,
            ]);
        }

        $produto->usuariosAutorizados()->syncWithoutDetaching([
            $user->id => ['ind_gestor' => $isGestorPrincipal],
        ]);
    }
}
