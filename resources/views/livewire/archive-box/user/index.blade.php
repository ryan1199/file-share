<x-card shadow>
    <x-header title="Keeper List" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Setting" icon="o-cog-6-tooth" @click="$wire.showUpdateUser = true" responsive />
            <x-button label="New User" icon="o-user-plus" @click="$wire.showNewUser = true" responsive />
        </x-slot:actions>
    </x-header>

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
            <x-badge value="{{ $user->archive_boxes_permission }}" class="badge-info" />
        @endscope
    </x-table>
    @auth
        @if ($archiveBox->users->whereIn('pivot.permission', [2,3])->contains(Auth::id()))
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
