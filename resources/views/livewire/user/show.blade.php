<div class="w-full container lg:max-w-3xl h-fit mx-auto grid grid-cols-1 gap-4">
    @auth
        @if ($user->id == Auth::id())
            <x-drawer
                wire:model="showSetting"
                title="Setting"
                subtitle="Setting your account"
                separator
                with-close-button
                close-on-escape
                class="w-fit">
                @livewire('user.edit', ['user' => $user], key(rand()))
            </x-drawer>
            <x-drawer
                wire:model="showCreateArchiveBox"
                title="Archive Box"
                subtitle="Create your archive box"
                separator
                with-close-button
                close-on-escape
                class="w-fit">
                @livewire('archive-box.create', key(rand()))
            </x-drawer>
            <x-drawer
                wire:model="showLogs"
                title="Logs"
                subtitle="All activities of this user"
                separator
                with-close-button
                close-on-escape
                class="w-fit">
                @livewire('user.log.index', ['user' => $user], key(rand()))
            </x-drawer>
        @endif
    @endauth
    <x-card shadow>
        <div class="pb-6">
            {{ $status }}
        </div>
        <div class="w-full h-fit flex flex-row flex-wrap">
            @if ($links != null)
                @foreach($links as $link)
                    <x-button label="{{ Str::between($link, '://', '/') }}" link="{{ $link }}" icon="o-link" tooltip="{{ $link }}" class="mr-1 mb-1" external />
                @endforeach
            @endif
        </div>
     
        <x-slot:figure class="p-4 pb-0">
            <div class="w-full">
                <x-avatar :image="asset('storage/avatars/'.$avatar)" class="!w-24">
                    <x-slot:title class="text-3xl pl-2">
                        {{ $name }}
                    </x-slot:title>
                 
                    <x-slot:subtitle class="text-base-content flex flex-col gap-1 mt-2 pl-2">
                        <x-icon name="o-at-symbol" label="{{ $email }}" />
                        <x-icon name="o-identification" label="{{ $slug }}" />
                        <x-icon name="o-cake" label="{{ $dob }}" />
                    </x-slot:subtitle>
                </x-avatar>
            </div>
        </x-slot:figure>
        <x-slot:actions>
            @auth
                @if ($user->id == Auth::id())
                    <x-button label="Create Archive Box" icon="o-archive-box" @click="$wire.showCreateArchiveBox = true" />
                    <x-button label="Setting" icon="o-cog-6-tooth" @click="$wire.showSetting = true" />
                    <x-button label="Logs" icon="o-book-open" @click="$wire.showLogs = true" />
                @endif
            @endauth
        </x-slot:actions>
    </x-card>
    @livewire('user.archive-box.index', ['user' => $user], key(rand()))
</div>
