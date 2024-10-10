<div class="w-full h-full">
    @auth
        @if ($archiveBox->users->where('pivot.permission', 3)->contains(Auth::id()))
            <x-drawer
                wire:model="showSetting"
                title="Setting"
                subtitle="Setting archive box and keeper"
                separator
                with-close-button
                close-on-escape
                class="w-fit">
                @livewire('archive-box.edit', ['archiveBox' => $archiveBox], key(rand()))
            </x-drawer>
        @endif
        @if ($archiveBox->users->whereIn('pivot.permission', [2,3])->contains(Auth::id()))
            <x-drawer
                wire:model="showUploadFile"
                title="Upload File"
                subtitle="Upload a file into archive box"
                separator
                with-close-button
                close-on-escape
                class="w-fit">
                @livewire('archive-box.file.create', ['archiveBox' => $archiveBox], key(rand()))
            </x-drawer>
            <x-drawer
                wire:model="showLogs"
                title="Logs"
                subtitle="All activities of this archive box"
                separator
                with-close-button
                close-on-escape
                class="w-fit">
                @livewire('archive-box.log.index', ['archiveBox' => $archiveBox], key(rand()))
            </x-drawer>
        @endif
    @endauth
    <div class="w-full h-fit grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2 gap-4">
        <div class="w-full h-fit mr-4 mb-4 flex flex-col space-x-0 space-y-4">
            <x-card class="w-full h-fit !p-0 bg-cover bg-center bg-no-repeat" style="background-image: url({{ asset('storage/covers/'.$cover) }});">
                <x-card title="{{ Str::of($name)->toHtmlString() }}" shadow class="w-full h-fit bg-base-300/60 backdrop-blur-[1.5px]">
                    <div class="h-fit max-h-40 overflow-y-auto">
                        {{ $description }}
                    </div>
                    <x-slot:menu>
                        @if ($private)
                            <x-badge value="Private" class="badge-accent" />
                        @else
                            <x-badge value="Public" class="badge-accent" />
                        @endif
                    </x-slot:menu>
                    <x-slot:actions>
                        @auth
                            @if ($archiveBox->users->whereIn('pivot.permission', [2,3])->contains(Auth::id()))
                                <x-button label="Upload File" icon="o-document-arrow-up" @click="$wire.showUploadFile = true" responsive />
                            @endif
                            @if ($archiveBox->users->where('pivot.permission', 3)->contains(Auth::id()))
                                <x-button label="Setting" icon="o-cog-6-tooth" @click="$wire.showSetting = true" responsive />
                            @endif
                            @if ($archiveBox->users->whereIn('pivot.permission', [2,3])->contains(Auth::id()))
                                <x-button label="Logs" icon="o-book-open" @click="$wire.showLogs = true" responsive />
                            @endif
                        @endauth
                    </x-slot:actions>
                </x-card>
            </x-card>
            <div class="w-full h-fit">
                @livewire('archive-box.user.index', ['archiveBox' => $archiveBox], key(rand()))
            </div>
        </div>
        <div class="w-full h-fit">
            @livewire('archive-box.file.index', ['archiveBox' => $archiveBox], key(rand()))
        </div>
    </div>
</div>
