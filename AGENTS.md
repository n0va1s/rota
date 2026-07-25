# Diretrizes para Agentes de IA - Projeto Rota

Este documento estabelece as regras arquiteturais, convenções de desenvolvimento, padrões de banco de dados, frontend, acessibilidade e logging inegociáveis para o desenvolvimento da aplicação **Rota**.

---

## 1. Arquitetura Base & Livewire Volt

- **Single File Components (Volt):** Todas as páginas web e componentes interativos **devem** utilizar o **Livewire Volt** (`livewire/volt`). É proibida a criação de Controllers de página ou views Blade separadas.
- **Sintaxe de Classe:** Utilize a sintaxe de classe (`new class extends Component`) para componentes com estado ou complexos, garantindo tipagem estrita e gestão limpa do ciclo de vida.
- **PHP Enums (≤ 10 opções):** Qualquer domínio com 10 ou menos opções (status, tipos, canais, categorias) deve ser implementado como **Backed PHP Enum** (ex: `enum StatusRota: string`), com `$casts` nos models e métodos auxiliares `label(): string`, `color(): string` e `icon(): string`.
- **Cobertura de Testes (Pest > 70%):** Todo código novo ou alterado deve ter testes automatizados via **Pest**, mantendo a cobertura global acima de 70%. Testes de feature devem exercitar componentes Volt usando `Livewire::test(...)`.
- **Ambiente de Execução (WSL):** Execução estritamente em ambiente Linux/WSL no repositório local (ex: `/home/n0va1s/projects/rota`). Comandos como `php artisan`, `composer`, `pest` e `npm` devem ser disparados no terminal WSL.

---

## 2. Padrões de Modelagem e Banco de Dados

- **Linguagem Ubíqua:** Tabelas e relacionamentos devem seguir a terminologia do negócio (`evento`, `participante`, `trabalhador`, `voluntario`).
- **Hierarquia de Nomes de Tabelas:** Tabelas filhas, relacionamentos N:N ou de detalhamento **devem** iniciar com o nome da tabela pai (ex: `pessoa_saude`, `evento_foto`).
- **Prefixos Semânticos de Colunas (3 letras + `_`):**
  - `idt_`: Chaves Primárias e Estrangeiras (`idt_pessoa`, `idt_evento`).
  - `nom_`: Nomes e Títulos textuais (`nom_pessoa`).
  - `des_`: Descrições curtas/médias (`des_endereco`).
  - `txt_`: Textos longos e observações (`txt_observacao`).
  - `ind_`: Flags booleanas (`ind_ativo`).
  - `dat_`: Datas e timestamps (`dat_nascimento`).
  - `tip_`: Tipos e siglas (`tip_estado_civil`).
  - `val_`: Valores monetários/contábeis (`val_taxa`).
  - `qtd_`: Quantidades inteiras (`qtd_vagas`).
  - `num_`: Números textuais sem operações matemáticas (`num_cpf`).
  - `eml_`: E-mails (`eml_contato`).
  - `tel_`: Telefones com DDD (`tel_contato`).
  - `tam_`: Tamanhos físicos (`tam_camiseta`).
  - `med_`: Caminhos de mídia/storage (`med_foto`).
  - `usu_`: IDs de usuário para auditoria (`usu_inclusao`, `usu_alteracao`).
- **Mapeamento Eloquent:** Modelos Eloquent devem declarar explicitamente `protected $table`, `protected $primaryKey = 'idt_...'`, `$fillable`, `$casts` (converter `ind_` em `boolean` e `tip_` em Enum) e indicar PK/FK em relacionamentos (`belongsTo`, `hasMany`).

---

## 3. Frontend, Acessibilidade & Componentes Flux

- **Componentes Flux (Livewire 3):** Priorizar a biblioteca **Livewire Flux** (`<flux:input>`, `<flux:textarea>`, `<flux:button>`, `<flux:select>`, `<flux:badge>`, `<flux:modal>`).
- **Acessibilidade (WCAG 2.2 Nível AA / Lighthouse 100):**
  - Tags HTML5 semânticas (`<main>`, `<nav>`, `<header>`, `<footer>`, `<article>`, `<button>`).
  - Foco navegável por teclado com anel `:focus-visible` evidente e skip link "Pular para conteúdo".
  - Atributos ARIA (`aria-label`, `aria-expanded`, `aria-live="polite"` para áreas Livewire dinâmicas).
  - Contraste de texto ≥ 4.5:1 (Light e Dark Mode).
  - Suporte a zoom de até 200% e respeito à preferência `prefers-reduced-motion`.
- **Mobile First & Performance:**
  - Layouts responsivos a partir de 320px com Media Queries progressivas (`sm:`, `md:`, `lg:`).
  - Áreas de toque (Touch Targets) de no mínimo **48px x 48px** em mobile.
  - Ícones exclusivamente em formato SVG inline ou `<flux:icon... />`.
  - Scripts não bloqueantes via `@vite` (`defer`/`async`).
- **Design Tokens:**
  - Tipografia: **Nunito** (`font-sans`, pesos 400 a 800).
  - Paleta: Primary (`#6366f1` / `indigo-500`), Accent (`#f472b6` / `pink-400`), Tertiary (`#06b6d4` / `cyan-500`), Success (`#10b981`), Warning (`#f59e0b`), Danger (`#ef4444`).
  - Cards e Painéis: `border-radius: 12px` (`rounded-lg`).

---

## 4. Logging, Rastreabilidade & LGPD

- **Trace ID Global:** Utilizar `TraceIdMiddleware` gravando UUID no header `X-Trace-ID` e injetando automaticamente no logger (`Log::withContext(['trace_id' => ...])`).
- **Contexto Base (`LogContext` Trait):** Incluir a Trait `App\Traits\LogContext` nos componentes/controladores para enriquecer os logs com `user_id`, `ip` e `route_name`.
- **Níveis de Severidade PSR-3:** `DEBUG` (dev), `INFO` (operações normais), `NOTICE` (sucessos de negócio), `WARNING` (erros de regra/validação de usuário), `ERROR` (exceptions e falhas de sistema).
- **Log de Exceções:** Em blocos `catch (\Throwable $e)`, registrar obrigatoriamente: `exception` (classe), `message`, `file` e `line`.
- **Proteção de Dados (LGPD):** Jamais logar senhas, tokens de autenticação, cartões de crédito ou dados médicos sigilosos. Sanitizar payloads com `$request->except(...)`.

---

## 5. Diretrizes Operacionais para Agentes de IA

### A. Fluxo de Execução e Verificação
1. **Inspeção Prévia:** Antes de criar arquivos, verificar migrations, enums e modelos já existentes no repositório.
2. **Execução de Testes:** Sempre rodar a suíte de testes (`php artisan test` ou `vendor/bin/pest`) após implementar uma alteração. Nunca declarar uma tarefa concluída sem verificação com resultado verde (passing tests).
3. **Formatação de Código:** Executar o linter/formatter (ex: `vendor/bin/pint`) para manter o estilo alinhado com o PSR-12/Laravel.

### B. Informações Relevantes Adicionais para o Projeto Rota
- **Formato de Notificações à UI:** Preferir o envio de notificações assíncronas (Toasts) através dos recursos nativos do Flux ou Alpine.js, evitando reloads desnecessários de página.
- **Estrutura de Diretórios Recomendada:**
  - Componentes Volt: `resources/views/livewire/` ou `resources/views/pages/`
  - Enums: `app/Enums/`
  - Traits: `app/Traits/`
  - Models: `app/Models/`
