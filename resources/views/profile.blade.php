<x-layouts.app title="Perfil">
    <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-extrabold text-slate-900 mb-6">Perfil do Usuário</h1>

        <div class="space-y-6">
            <div class="p-6 bg-white border border-slate-200 rounded-2xl shadow-xs">
                <div class="max-w-xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <div class="p-6 bg-white border border-slate-200 rounded-2xl shadow-xs">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            <div class="p-6 bg-white border border-slate-200 rounded-2xl shadow-xs">
                <div class="max-w-xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
