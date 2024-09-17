<div class="w-full h-full flex flex-col justify-center items-center overflow-y-auto">
    <div x-data="{ show_password: false }" class="w-full max-w-lg h-fit">
        <x-card title="Log in" subtitle="Log in your account" separator progress-indicator="login">
            <x-form wire:submit="login">
                <x-input type="email" label="E-Mail" icon="o-at-symbol" hint="Your active e-mail" wire:model="email" error-field="email" clearable inline />
                <x-input x-bind:type="show_password ? 'text' : 'password'" label="Password" icon="o-lock-closed" hint="Secure password" wire:model="password" error-field="password" clearable inline />
                <div class="w-fit ml-auto">
                    <x-toggle label="Show password" x-model="show_password" right tight />
                </div>
                <div class="w-fit ml-auto">
                    <x-toggle label="Remember me" wire:model="rememberMe" right tight />
                </div>
             
                <x-slot:actions>
                    <x-button label="Cancel" wire:click="cancel" />
                    <x-button label="Log in" class="btn-primary" type="submit" spinner="login" />
                </x-slot:actions>
            </x-form>
        </x-card>
    </div>
</div>
