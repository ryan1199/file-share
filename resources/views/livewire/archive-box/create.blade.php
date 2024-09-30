<x-form wire:submit="store" class="w-screen max-w-xs sm:max-w-md h-fit">
    <x-file wire:model="cover" label="Cover" hint="Select an image" accept="image/*" crop-after-change>
        <img src="{{ $cover ?? 'https://picsum.photos/500/200' }}" class="h-40" />
    </x-file>
    <x-input type="text" label="Name" icon="o-archive-box" hint="Archive Box's name" wire:model="name" error-field="name" clearable inline />
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
