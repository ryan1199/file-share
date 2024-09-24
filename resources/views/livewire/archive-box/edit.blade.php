<div class="w-full h-fit flex flex-col space-x-0 space-y-4">
    <x-collapse>
        <x-slot:heading>
            Update Archive Box
        </x-slot:heading>
        <x-slot:content>
            <div class="w-full h-fit">
                <x-card title="Update Archive Box" subtitle="Update archive box detail" separator progress-indicator="updateArchiveBox">
                    <x-form wire:submit="updateArchiveBox">
                        <x-file wire:model="cover" label="Cover" hint="Select an image" accept="image/*" crop-after-change>
                            <img src="{{ $cover ?? 'https://picsum.photos/500/200' }}" class="h-40 rounded-lg" />
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
                            <x-button label="Cancel" class="btn-secondary" wire:click="cancelUpdateArchiveBox" />
                            <x-button label="Update" class="btn-primary" type="submit" spinner="updateArchiveBox" />
                        </x-slot:actions>
                    </x-form>
                </x-card>
            </div>
        </x-slot:content>
    </x-collapse>
    <x-collapse>
        <x-slot:heading>
            Update Keeper
        </x-slot:heading>
        <x-slot:content class="w-full max-w-full overflow-x-auto">
            <div class="w-full h-fit">
                <div class="w-full pb-4 flex flex-col space-x-0 space-y-4">
                    <x-input placeholder="Search..." wire:model.live.debounce="searchKeeper" icon="o-magnifying-glass" class="mt-2" />
                    <x-select label="Sort By" icon="o-funnel" :options="$availableSortBy" wire:model.live.debounce="sortByKeeper" inline />
                    <div class="w-fit self-end">
                        <x-toggle label="Asc" wire:model.live.debounce="ascKeeper" right tight />
                    </div>
                </div>
                <x-header separator progress-indicator />
            
                @forelse ($keepers as $keeper)
                    @php
                        $keeper->avatar = asset('storage/avatars/'.$keeper->avatar);
                    @endphp
                    <x-list-item :item="$keeper" avatar="avatar" no-separator no-hover wire:key="{{ 'keepers-'.rand() }}">
                        <x-slot:value>
                            {{ $keeper->name }}
                        </x-slot:value>
                        <x-slot:sub-value>
                            {{ $keeper->slug }}
                        </x-slot:sub-value>
                        <x-slot:actions>
                            <x-dropdown>
                                <x-slot:trigger>
                                    <x-button icon="o-cog-6-tooth" />
                                </x-slot:trigger>
                             
                                @if ($archiveBox->private)
                                    @for ($i = 1; $i <= 3; $i++)
                                        @if ($keeper->archive_boxes_permission == $i)
                                            <x-menu-item title="{{ 'Permission '.$i }}" class="bg-base-200" />
                                        @endif
                                        @if ($keeper->archive_boxes_permission != $i && $keeper->id != Auth::id())
                                            <x-menu-item title="{{ 'Permission '.$i }}" wire:click="updateUserPermission({{ $keeper->id }},{{ $i }})" />
                                        @endif
                                    @endfor
                                @else
                                    @for ($i = 2; $i <= 3; $i++)
                                        @if ($keeper->archive_boxes_permission == $i)
                                            <x-menu-item title="{{ 'Permission '.$i }}" class="bg-base-200" />
                                        @endif
                                        @if ($keeper->archive_boxes_permission != $i && $keeper->id != Auth::id())
                                            <x-menu-item title="{{ 'Permission '.$i }}" wire:click="updateUserPermission({{ $keeper->id }},{{ $i }})" />
                                        @endif
                                    @endfor
                                @endif
                                <x-menu-item title="Remove" wire:confirm="{{ 'Remove user '.$keeper->name.' ?' }}" wire:click="removeUser({{ $keeper->id }})" />
                            </x-dropdown>
                        </x-slot:actions>
                    </x-list-item>
                @empty
                    <x-alert title="Keeper not found" icon="o-exclamation-triangle" class="bg-error text-error-content" />
                @endforelse
            </div>
        </x-slot:content>
    </x-collapse>
    <x-collapse>
        <x-slot:heading>
            New Keeper
        </x-slot:heading>
        <x-slot:content class="w-full max-w-full overflow-x-auto">
            <div class="w-full h-fit">
                <div class="w-full pb-4 flex flex-col space-x-0 space-y-4">
                    <x-input placeholder="Search..." wire:model.live.debounce="searchUser" icon="o-magnifying-glass" class="mt-2" />
                    <x-select label="Sort By" icon="o-funnel" :options="$availableSortBy" wire:model.live.debounce="sortByUser" inline />
                    <div class="w-fit self-end">
                        <x-toggle label="Asc" wire:model.live.debounce="ascUser" right tight />
                    </div>
                </div>
                <x-header separator progress-indicator />
            
                @forelse ($users as $user)
                    @php
                        $user->avatar = asset('storage/avatars/'.$user->avatar);
                    @endphp
                    <x-list-item :item="$user" avatar="avatar" no-separator no-hover wire:key="{{ 'users-'.rand() }}">
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
                    <x-alert title="Keeper not found" icon="o-exclamation-triangle" class="bg-error text-error-content" />
                @endforelse
            </div>
        </x-slot:content>
    </x-collapse>
</div>
