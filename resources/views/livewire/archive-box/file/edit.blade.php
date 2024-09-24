<div class="w-full h-full">
    <x-card title="Edit File" subtitle="Edit a file" separator progress-indicator="update" shadow class="border border-base-300">
        <x-form wire:submit="update">
            <x-file wire:model="uploadFile" label="File" hint="Select a file" />
            <x-input type="text" label="Name" icon="o-document" hint="File's name" wire:model="name" error-field="name" clearable inline />
            <x-textarea
                label="Description"
                wire:model="description"
                placeholder="This file is about ..."
                hint="File's name"
                rows="5"
                error-field="description"
                clearable
                inline />
         
            <x-slot:actions>
                <x-button label="Cancel" class="btn-secondary" wire:click="cancel" />
                <x-button label="Create" class="btn-primary" type="submit" spinner="update" />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>
