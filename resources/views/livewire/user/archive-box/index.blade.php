<x-card shadow>
    <x-header title="Archive Box" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Filters" @click="$wire.drawer = true" responsive icon="o-funnel" class="btn-primary" />
        </x-slot:actions>
    </x-header>

    @if ($archiveBoxes == [])
        <x-alert title="Archive Box not found" icon="o-exclamation-triangle" class="bg-error text-error-content" shadow />
    @else
        <x-carousel :slides="$archiveBoxes" />
    @endif

    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <div class="flex flex-col space-x-0 space-y-4">
            <x-input placeholder="Search..." wire:model.live.debounce="search" icon="o-magnifying-glass" @keydown.enter="$wire.drawer = false" />
            <x-select label="Sort By" icon="o-funnel" :options="$availableSortBy" wire:model.live.debounce="sortBy" inline />
            <div class="w-fit self-end">
                <x-toggle label="Asc" wire:model.live.debounce="asc" right tight />
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer>
</x-card>

