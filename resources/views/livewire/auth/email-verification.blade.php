<div class="w-full h-full flex flex-col justify-center items-center overflow-y-auto">
    <div x-data="{ show_password: false }" class="w-full max-w-lg h-fit">
        @if ($token == null)
            <x-card title="Verify email address" subtitle="Verify your email address" separator progress-indicator="sendEmailVerification">
                <x-form wire:submit="sendEmailVerification">
                    <x-input type="email" label="E-Mail" icon="o-at-symbol" hint="Your active e-mail" wire:model="email" error-field="email" clearable inline />
                    <x-input x-bind:type="show_password ? 'text' : 'password'" label="Password" icon="o-lock-closed" hint="Secure password" wire:model="password" error-field="password" clearable inline />
                    <div class="w-fit ml-auto">
                        <x-toggle label="Show password" x-model="show_password" right tight />
                    </div>
                
                    <x-slot:actions>
                        <x-button label="Cancel" class="btn-secondary" wire:click="cancel" />
                        <x-button label="Send Email Verification" class="btn-primary" type="submit" spinner="sendEmailVerification" />
                    </x-slot:actions>
                </x-form>
            </x-card>
        @else
            <x-card title="Verify email address" subtitle="Verify your email address" separator progress-indicator="verifyEmail">
                <x-form wire:submit="verifyEmail">
                    <x-input type="email" label="E-Mail" icon="o-at-symbol" hint="Your active e-mail" wire:model="email" error-field="email" clearable inline />
                    <x-input x-bind:type="show_password ? 'text' : 'password'" label="Password" icon="o-lock-closed" hint="Secure password" wire:model="password" error-field="password" clearable inline />
                    <div class="w-fit ml-auto">
                        <x-toggle label="Show password" x-model="show_password" right tight />
                    </div>
                
                    <x-slot:actions>
                        <x-button label="Cancel" class="btn-secondary" wire:click="cancel" />
                        <x-button label="Verify Email" class="btn-primary" type="submit" spinner="verifyEmail" />
                    </x-slot:actions>
                </x-form>
            </x-card>
        @endif
    </div>
</div>
