<?php

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

use function Livewire\Volt\state;

state([
    'name' => fn () => auth()->user()->name,
    'email' => fn () => auth()->user()->email,
    'num_cpf' => fn () => auth()->user()->num_cpf,
]);

$updateProfileInformation = function () {
    $user = Auth::user();

    $validated = $this->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        'num_cpf' => ['nullable', 'string', 'max:20'],
    ]);

    $user->fill($validated);

    if ($user->isDirty('email')) {
        $user->email_verified_at = null;
    }

    $user->save();

    $this->dispatch('profile-updated', name: $user->name);
};

$sendVerification = function () {
    $user = Auth::user();

    if ($user->hasVerifiedEmail()) {
        $this->redirectIntended(default: route('welcome', absolute: false));

        return;
    }

    $user->sendEmailVerificationNotification();

    Session::flash('status', 'verification-link-sent');
};

?>

<section>
    <header>
        <h2 class="text-lg font-bold text-slate-900">
            Informações do Perfil
        </h2>

        <p class="mt-1 text-sm text-slate-600">
            Atualize as informações do seu perfil, CPF e endereço de e-mail.
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
        <!-- Exibição somente leitura do Perfil (tip_role) -->
        <div>
            <x-input-label for="tip_role" value="Perfil / Função (Apenas leitura)" />
            <div class="mt-1 flex items-center gap-2">
                <flux:badge :color="auth()->user()->tip_role?->color() ?? 'indigo'" size="md">
                    {{ auth()->user()->tip_role?->label() ?? 'Usuário' }}
                </flux:badge>
                <span class="text-xs text-slate-500 italic">(O perfil é gerenciado pelo sistema e não pode ser alterado aqui)</span>
            </div>
        </div>

        <div>
            <x-input-label for="name" value="Nome Completo" />
            <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="num_cpf" value="CPF" />
            <x-text-input wire:model="num_cpf" id="num_cpf" name="num_cpf" type="text" class="mt-1 block w-full" placeholder="000.000.000-00" autocomplete="off" />
            <x-input-error class="mt-2" :messages="$errors->get('num_cpf')" />
        </div>

        <div>
            <x-input-label for="email" value="E-mail" />
            <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if (auth()->user() instanceof MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-slate-800">
                        Seu endereço de e-mail não foi verificado.

                        <button wire:click.prevent="sendVerification" class="underline text-sm text-slate-600 hover:text-slate-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Clique aqui para reenviar o e-mail de verificação.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-emerald-600">
                            Um novo link de verificação foi enviado para o seu e-mail.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Salvar Alterações</x-primary-button>

            <x-action-message class="me-3 text-emerald-600 font-semibold" on="profile-updated">
                Salvo com sucesso.
            </x-action-message>
        </div>
    </form>
</section>
