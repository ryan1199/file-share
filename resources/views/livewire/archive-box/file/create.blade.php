<x-form wire:submit="store" class="w-screen max-w-xs sm:max-w-md md:max-w-lg">
    <x-file wire:model="file" label="File" hint="Select a file" />
    <x-input type="text" label="Name" icon="o-document" hint="File's name" wire:model="name" error-field="name" clearable inline />
    <x-textarea
        label="Description"
        wire:model="description"
        placeholder="This file is about ..."
        hint="File's description"
        rows="5"
        error-field="description"
        clearable
        inline />
 
    <x-slot:actions>
        <x-button label="Cancel" class="btn-secondary" wire:click="cancel" />
        <x-button label="Upload" class="btn-primary" type="submit" spinner="store" />
    </x-slot:actions>
</x-form>
