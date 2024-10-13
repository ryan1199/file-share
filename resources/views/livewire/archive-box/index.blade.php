<x-card shadow>
    <x-header title="Archive Box List" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
    </x-header>
    <x-range wire:model.live.debounce="perPage" min="1" max="100" step="1" label="Show archive boxes per page" hint="{{ 'Showing archive box list per page: '.$perPage }}" />
    <x-table :headers="$this->headers" :rows="$archiveBoxes" :sort-by="$sortBy" with-pagination >
        @scope('cell_slug', $archiveBox)
            <x-button label="{{ $archiveBox->slug }}" link="{{ route('archive-box.show', $archiveBox->slug) }}" icon="o-archive-box" tooltip="{{ Str::of('Visit '.$archiveBox->name)->toHtmlString() }}" responsive class="w-fit flex-nowrap" />
        @endscope
        @scope('cell_description', $archiveBox)
            <div class="tooltip" data-tip="{{ $archiveBox->description }}">
                <p>{{ Str::words($archiveBox->description, 3, ' ...') }}</p>
            </div>
        @endscope
        @scope('cell_private', $archiveBox)
            @if ($archiveBox->private)
                <x-badge value="Private" class="badge-accent" />
            @else
                <x-badge value="Public" class="badge-accent" />
            @endif
        @endscope
        @scope('cell_users_name', $archiveBox)
            <x-button label="{{ $archiveBox->users_name }}" wire:click="seeUserByName('{{ $archiveBox->users_name }}')" icon="o-user" tooltip="{{ 'Search '.$archiveBox->users_name }}" responsive class="w-fit flex-nowrap" />
        @endscope
    </x-table>
</x-card>
