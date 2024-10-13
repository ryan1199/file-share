<div class="w-screen max-w-xs sm:max-w-md md:max-w-lg h-fit">
    <div class="w-full pb-4 flex flex-col space-x-0 space-y-4">
        <x-input placeholder="Search..." wire:model.live.debounce="search" icon="o-magnifying-glass" class="mt-2" />
        <x-select label="Sort By" icon="o-funnel" :options="$availableSortBy" wire:model.live.debounce="sortBy" inline />
        <x-range wire:model.live.debounce="perPage" min="1" max="100" step="1" label="Show user per page" hint="{{ 'Showing user list per page: '.$perPage }}" />
        <div class="w-fit self-end">
            <x-toggle label="Asc" wire:model.live.debounce="asc" right tight />
        </div>
    </div>

    @forelse ($users as $user)
        @php
            $user->avatar = asset('storage/avatars/'.$user->avatar);
        @endphp
        <x-list-item :item="$user" avatar="avatar" no-separator no-hover wire:key="{{ 'new-users-'.rand() }}">
            <x-slot:value>
                {{ $user->name }}
            </x-slot:value>
            <x-slot:sub-value>
                {{ $user->slug }}
            </x-slot:sub-value>
            <x-slot:actions>
                <x-dropdown>
                    <x-slot:trigger>
                        <x-button icon="o-cog-6-tooth" />
                    </x-slot:trigger>
                 
                    @if ($archiveBox->private)
                        @for ($i = 1; $i <= 3; $i++)
                            <x-menu-item title="{{ 'Permission '.$i }}" wire:click="newUser({{ $user->id }},{{ $i }})" />
                        @endfor
                    @else
                        @for ($i = 2; $i <= 3; $i++)
                            <x-menu-item title="{{ 'Permission '.$i }}" wire:click="newUser({{ $user->id }},{{ $i }})" />
                        @endfor
                    @endif
                </x-dropdown>
            </x-slot:actions>
        </x-list-item>
    @empty
        <x-alert title="User not found" icon="o-exclamation-triangle" class="bg-error text-error-content" shadow />
    @endforelse
    <x-pagination :rows="$users" wire:model.live="perPage" />
</div>