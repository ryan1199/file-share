<div class="w-full h-full flex flex-col justify-center items-center overflow-y-auto">
    <div class="w-full max-w-lg h-fit">
        <x-card title="Reset Password" subtitle="Reset my account's password" separator progress-indicator="resetPassword">
            <x-form wire:submit="resetPassword">
                <x-input type="email" label="E-Mail" icon="o-at-symbol" hint="Your active e-mail" wire:model="email" error-field="email" clearable inline />
             
                <x-slot:actions>
                    <x-button label="Cancel" class="btn-secondary" wire:click="cancel" />
                    <x-button label="Reset Password" class="btn-primary" type="submit" spinner="resetPassword" />
                </x-slot:actions>
            </x-form>
        </x-card>
    </div>
</div>
