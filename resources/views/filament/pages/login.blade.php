<x-filament::page>
    <x-filament-auth::card>
        <div class="space-y-8">
            <h2 class="text-2xl font-bold tracking-tight text-center">
                {{ __('filament::login.heading') }}
            </h2>

            <x-filament-auth::card.form>
                {{ $this->form }}

                <x-filament::button type="submit" class="w-full">
                    {{ __('filament::login.buttons.submit.label') }}
                </x-filament::button>
            </x-filament-auth::card.form>
        </div>

        <div class="mt-4 text-center">
            <a href="{{ $registerLink }}" class="text-primary-600 hover:text-primary-700">
                Register as Restaurant Admin
            </a>
        </div>
    </x-filament-auth::card>
</x-filament::page>