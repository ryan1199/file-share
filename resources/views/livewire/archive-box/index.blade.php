<div>
    
    <x-header title="Archive Box List" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Filters" @click="$wire.drawer = true" responsive icon="o-funnel" class="btn-primary" />
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-table :headers="$this->headers" :rows="$archiveBoxes" :sort-by="$sortBy" with-pagination >
            @scope('cell_slug', $archiveBox)
                <x-button label="{{ $archiveBox->slug }}" link="{{ route('user.show', $archiveBox->slug) }}" icon="o-archive-box" tooltip="{{ Str::of('Visit '.$archiveBox->name)->toHtmlString() }}" responsive />
            @endscope
            @scope('cell_description', $archiveBox)
                <div class="tooltip" data-tip="{{ $archiveBox->description }}">
                    <p>{{ Str::words($archiveBox->description, 3, ' ...') }}</p>
                </div>
            @endscope
            @scope('cell_private', $archiveBox)
                @if ($archiveBox->private)
                    <x-badge value="Private" class="badge-error" />
                @else
                    <x-badge value="Public" class="badge-success" />
                @endif
            @endscope
            @scope('cell_users_name', $archiveBox)
                <x-button label="{{ $archiveBox->users_name }}" wire:click="seeUserByName('{{ $archiveBox->users_name }}')" icon="o-user" tooltip="{{ 'Search '.$archiveBox->users_name }}" responsive />
            @endscope
        </x-table>
    </x-card>

    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <x-input placeholder="Search..." wire:model.live.debounce="search" icon="o-magnifying-glass" @keydown.enter="$wire.drawer = false" />

        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer>
</div>
