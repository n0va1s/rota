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

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.5
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- livewire/flux (FLUXUI_FREE) - v2
- livewire/livewire (LIVEWIRE) - v3
- livewire/volt (VOLT) - v1
- laravel/boost (BOOST) - v2
- laravel/breeze (BREEZE) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- tailwindcss (TAILWINDCSS) - v3

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `vendor/bin/sail npm run build`, `vendor/bin/sail npm run dev`, or `vendor/bin/sail composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `vendor/bin/sail artisan route:list`). Use `vendor/bin/sail artisan list` to discover available commands and `vendor/bin/sail artisan [command] --help` to check parameters.
- Inspect routes with `vendor/bin/sail artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `vendor/bin/sail artisan config:show app.name`, `vendor/bin/sail artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `vendor/bin/sail artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `vendor/bin/sail artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Follow existing application Enum naming conventions.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== sail rules ===

# Laravel Sail

- This project runs inside Laravel Sail's Docker containers. You MUST execute all commands through Sail.
- Start services using `vendor/bin/sail up -d` and stop them with `vendor/bin/sail stop`.
- Open the application in the browser by running `vendor/bin/sail open`.
- Always prefix PHP, Artisan, Composer, and Node commands with `vendor/bin/sail`. Examples:
    - Run Artisan Commands: `vendor/bin/sail artisan migrate`
    - Install Composer packages: `vendor/bin/sail composer install`
    - Execute Node commands: `vendor/bin/sail npm run dev`
    - Execute PHP scripts: `vendor/bin/sail php [script]`
- View all available Sail commands by running `vendor/bin/sail` without arguments.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `vendor/bin/sail artisan test --compact` with a specific filename or filter.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `vendor/bin/sail artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `vendor/bin/sail artisan list` and check their parameters with `vendor/bin/sail artisan [command] --help`.
- If you're creating a generic PHP class, use `vendor/bin/sail artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `vendor/bin/sail artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `vendor/bin/sail artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `vendor/bin/sail npm run build` or ask the user to run `vendor/bin/sail npm run dev` or `vendor/bin/sail composer run dev`.

=== livewire/core rules ===

# Livewire

- Livewire allow to build dynamic, reactive interfaces in PHP without writing JavaScript.
- You can use Alpine.js for client-side interactions instead of JavaScript frameworks.
- Keep state server-side so the UI reflects it. Validate and authorize in actions as you would in HTTP requests.

=== volt/core rules ===

# Livewire Volt

- Single-file Livewire components: PHP logic and Blade templates in one file.
- Always check existing Volt components to determine functional vs class-based style.
- IMPORTANT: Always use `search-docs` tool for version-specific Volt documentation and updated code examples.
- IMPORTANT: Activate `volt-development` every time you're working with a Volt or single-file component-related task.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/sail bin pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/sail bin pint --test --format agent`, simply run `vendor/bin/sail bin pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `vendor/bin/sail artisan make:test --pest {name}`.
- The `{name}` argument should not include the test suite directory. Use `vendor/bin/sail artisan make:test --pest SomeFeatureTest` instead of `vendor/bin/sail artisan make:test --pest Feature/SomeFeatureTest`.
- Run tests: `vendor/bin/sail artisan test --compact` or filter: `vendor/bin/sail artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.

</laravel-boost-guidelines>
