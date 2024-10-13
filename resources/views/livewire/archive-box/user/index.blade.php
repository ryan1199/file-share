<x-card shadow>
    <x-header title="User List" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" class="min-w-56" />
        </x-slot:middle>
        <x-slot:actions class="w-full">
            @if ($archiveBox->users->where('pivot.permission', 3)->contains(Auth::id()))
                <x-button label="Setting" icon="o-cog-6-tooth" @click="$wire.showUpdateUser = true" responsive />
                <x-button label="New User" icon="o-user-plus" @click="$wire.showNewUser = true" responsive />
            @endif
            @if ($archiveBox->users->whereIn('pivot.permission', [1,2,3])->contains(Auth::id()))
                <x-button label="Quit" icon="o-arrow-left-start-on-rectangle" wire:confirm="{{ 'Quit from '.$archiveBox->name.' ?' }}" wire:click="quitFromArchiveBox" responsive />
            @endif
        </x-slot:actions>
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
        @scope('cell_archive_boxes_permission', $user)
            <x-badge value="{{ $user->archive_boxes_permission }}" class="badge-accent" />
        @endscope
    </x-table>
    @auth
        @if ($archiveBox->users->where('pivot.permission', 3)->contains(Auth::id()))
            <x-drawer
                wire:model="showUpdateUser"
                title="Update Users"
                subtitle="Update users"
                separator
                with-close-button
                close-on-escape
                class="w-fit">
                @livewire('archive-box.user.edit', ['archiveBox' => $archiveBox])
            </x-drawer>
            <x-drawer
                wire:model="showNewUser"
                title="New Users"
                subtitle="New users"
                separator
                with-close-button
                close-on-escape
                class="w-fit">
                @livewire('archive-box.user.create', ['archiveBox' => $archiveBox])
            </x-drawer>
        @endif
    @endauth
</x-card>
