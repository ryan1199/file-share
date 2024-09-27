<x-card shadow>
    <x-header title="File List" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
    </x-header>

    <x-card>
        <x-table :headers="$this->headers" :rows="$files" :sort-by="$sortBy" with-pagination >
            @scope('cell_slug', $file)
                <x-button label="{{ $file->slug }}" link="{{ route('file.show', $file->slug) }}" icon="o-document" tooltip="{{ 'View '.$file->name }}" responsive class="w-fit flex-nowrap" />
            @endscope
            @scope('cell_extension', $file)
                <x-badge value="{{ $file->extension }}" class="badge-info whitespace-nowrap" />
            @endscope
            @scope('cell_size', $file)
                <x-badge value="{{ Number::fileSize($file->size) }}" class="badge-info whitespace-nowrap" />
            @endscope
            @scope('cell_actions', $file, $archiveBox)
                <div class="w-fit h-fit flex flex-row space-x-2 space-y-0">
                    @auth
                        @if ($archiveBox->users->whereIn('pivot.permission', [2,3])->contains(Auth::id()))
                            <x-button label="Edit" wire:click="editFile({{ $file->id }})" icon="o-pencil" tooltip="{{ 'Edit '.$file->name }}" responsive class="w-fit flex-nowrap" />
                            @if ($archiveBox->users->where('pivot.permission', 3)->contains(Auth::id()))
                                <x-button label="Delete" wire:click="deleteFile({{ $file->id }})" wire:confirm="{{ 'Delete '.$file->name.' ?' }}" icon="o-trash" tooltip="{{ 'Delete '.$file->name }}" responsive class="w-fit flex-nowrap" />
                            @endif
                        @endif
                    @endauth
                    @switch($archiveBox->private)
                        @case(false)
                            <x-button label="Download" link="{{ route('file.download', $file->slug) }}" icon="o-document-arrow-down" tooltip="{{ 'Download '.$file->name }}" no-wire-navigate responsive class="w-fit flex-nowrap" />
                            @break
                        @default
                            @auth
                                @if ($archiveBox->users->whereIn('pivot.permission', [1,2,3])->contains(Auth::id()))
                                    <x-button label="Download" link="{{ route('file.download', $file->slug) }}" icon="o-document-arrow-down" tooltip="{{ 'Download '.$file->name }}" no-wire-navigate responsive class="w-fit flex-nowrap" />
                                @endif
                            @endauth
                    @endswitch
                </div>
            @endscope
        </x-table>
    </x-card>
    @auth
        @if ($archiveBox->users->whereIn('pivot.permission', [2,3])->contains(Auth::id()))
            <x-drawer
                wire:model="showEditFile"
                title="Edit File"
                subtitle="Edit a file"
                separator
                with-close-button
                close-on-escape
                class="w-fit">
                @livewire('archive-box.file.edit', ['archiveBox' => $archiveBox])
            </x-drawer>
        @endif
    @endauth
</x-card>
