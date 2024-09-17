<div class="w-full h-full flex flex-col justify-center items-center overflow-y-auto">
    <div x-data="{ show_password: false, show_password_confirmation: false }" class="w-full max-w-lg h-fit">
        <x-card title="Register" subtitle="Register your account" separator progress-indicator="register">
            <x-form wire:submit="register">
                <x-input type="text" label="Name" icon="o-user" hint="Your full name" wire:model="name" error-field="name" clearable inline />
                <x-input type="email" label="E-Mail" icon="o-at-symbol" hint="Your active e-mail" wire:model="email" error-field="email" clearable inline />
                <x-input x-bind:type="show_password ? 'text' : 'password'" label="Password" icon="o-lock-closed" hint="Secure password" wire:model="password" error-field="password" clearable inline />
                <div class="w-fit ml-auto">
                    <x-toggle label="Show password" x-model="show_password" right tight />
                </div>
                <x-input x-bind:type="show_password_confirmation ? 'text' : 'password'" label="Password confirmation" icon="o-lock-closed" hint="Same password" wire:model="password_confirmation" error-field="password_confirmation" clearable inline />
                <div class="w-fit ml-auto">
                    <x-toggle label="Show password" x-model="show_password_confirmation" right tight />
                </div>
             
                <x-slot:actions>
                    <x-button label="Cancel" wire:click="cancel" />
                    <x-button label="Register" class="btn-primary" type="submit" spinner="register" />
                </x-slot:actions>
            </x-form>
        </x-card>
    </div>
</div>
