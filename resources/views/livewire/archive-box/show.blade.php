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
                class="w-11/12 lg:w-1/3">
                @livewire('archive-box.edit', ['archiveBox' => $archiveBox])
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
                class="w-11/12 lg:w-1/3">
                @livewire('archive-box.file.create', ['archiveBox' => $archiveBox])
            </x-drawer>
        @endif
    @endauth
    <div class="w-full h-fit flex flex-row flex-wrap justify-center">
        <div class="w-full max-w-xl h-fit mr-4 mb-4 flex flex-col space-x-0 space-y-4">
            <div class="w-full h-fit bg-cover bg-center bg-no-repeat rounded-btn" style="background-image: url({{ asset('storage/covers/'.$cover) }});">
                <x-card title="{{ Str::of($name)->toHtmlString() }}" shadow class="w-full h-fit bg-base-300/60 backdrop-blur-[1.5px]">
                    <div class="h-fit max-h-40 overflow-y-auto">
                        {{ $description }}
                    </div>
                    <x-slot:menu>
                        @if ($private)
                            <x-badge value="Private" class="badge-error" />
                        @else
                            <x-badge value="Public" class="badge-success" />
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
                        @endauth
                    </x-slot:actions>
                </x-card>
            </div>
            <div class="w-full h-fit">
                @livewire('archive-box.user.index', ['archiveBox' => $archiveBox])
            </div>
        </div>
        <div class="w-full max-w-xl h-fit">
            @livewire('archive-box.file.index', ['archiveBox' => $archiveBox])
        </div>
    </div>
</div>
