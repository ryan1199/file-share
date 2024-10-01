<div class="w-full h-full flex flex-col justify-center items-center overflow-y-auto">
    <div class="w-full max-w-lg h-fit">
        <x-card title="Reset Password" subtitle="Reset my account's password" separator progress-indicator>
            @if ($token != null)
                <x-button wire:click="resetPassword" label="Reset Password" class="btn-primary" spinner="resetPassword" />
            @else
                <x-form wire:submit="requestResetPassword">
                    <x-input type="email" label="E-Mail" icon="o-at-symbol" hint="Your active e-mail" wire:model="email" error-field="email" clearable inline />
                
                    <x-slot:actions>
                        <x-button label="Cancel" class="btn-secondary" wire:click="cancel" />
                        <x-button label="Reset Password" class="btn-primary" type="submit" spinner="requestResetPassword" />
                    </x-slot:actions>
                </x-form>
            @endif
        </x-card>
    </div>
</div>
