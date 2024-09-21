<div class="w-full h-full flex flex-col justify-center items-center overflow-y-auto">
    <div class="w-full max-w-lg h-fit">
        <x-card title="Archive Box" subtitle="Create your archive box" separator progress-indicator="store">
            <x-form wire:submit="store">
                <x-file wire:model="cover" label="Cover" hint="Select an image" accept="image/*" crop-after-change>
                    <img src="{{ $cover ?? asset('storage/avatars/default-avatar-white.svg') }}" class="h-40 rounded-lg" />
                </x-file>
                <x-input type="text" label="Name" icon="o-user" hint="Archive Box's name" wire:model="name" error-field="name" clearable inline />
                <x-textarea
                    label="Description"
                    wire:model="description"
                    placeholder="This archive box is about ..."
                    hint="Archive Box's name"
                    rows="5"
                    error-field="description"
                    clearable
                    inline />
                <div class="w-fit ml-auto">
                    <x-toggle label="Private" wire:model="private" right tight />
                </div>
             
                <x-slot:actions>
                    <x-button label="Cancel" class="btn-secondary" wire:click="cancel" />
                    <x-button label="Create" class="btn-primary" type="submit" spinner="store" />
                </x-slot:actions>
            </x-form>
        </x-card>
    </div>
</div>
