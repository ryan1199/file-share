<x-card shadow>
    <x-header title="User List" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
    </x-header>
    <x-range wire:model.live.debounce="perPage" min="1" max="100" step="1" label="Show users per page" hint="{{ 'Showing user list per page: '.$perPage }}" />
    <x-table :headers="$this->headers" :rows="$users" :sort-by="$sortBy" with-pagination >
        @scope('cell_name', $user)
            <x-avatar :image="asset('storage/avatars/'.$user->avatar)" :title="$user->name" />
        @endscope
        @scope('cell_slug', $user)
            <x-button label="{{ $user->slug }}" link="{{ route('user.show', $user->slug) }}" icon="o-user" tooltip="{{ 'Visit '.$user->name }}" responsive class="w-fit flex-nowrap" />
        @endscope
        @scope('cell_email', $user)
            <x-button label="{{ $user->email }}" link="{{ 'mailto:'.$user->email }}" icon="o-envelope" tooltip="{{ 'Mail '.$user->email }}" external responsive class="w-fit flex-nowrap" />
        @endscope
    </x-table>
</x-card>
