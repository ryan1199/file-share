<div class="w-full h-full flex flex-col justify-center items-center overflow-y-auto">
    @auth
        @if ($user->id == Auth::id())
            <x-drawer
                wire:model="showSetting"
                title="Setting"
                subtitle="Setting your account"
                separator
                with-close-button
                close-on-escape
                class="w-11/12 lg:w-1/3"
            >
                @livewire('user.edit', ['user' => $user])
            </x-drawer>
            <x-drawer
                wire:model="showCreateArchiveBox"
                title="Archive Box"
                subtitle="Create your archive box"
                separator
                with-close-button
                close-on-escape
                class="w-11/12 lg:w-1/3"
            >
                @livewire('archive-box.create')
            </x-drawer>
        @endif
    @endauth
    <div class="w-full max-w-lg h-fit rounded-xl">
        <x-card shadow>
            <div class="pb-6">
                {{ $status }}
            </div>
            <div class="w-full h-fit flex flex-row flex-wrap">
                @foreach($links as $link)
                    <x-button label="{{ Str::between($link, '://', '/') }}" link="{{ $link }}" icon="o-link" tooltip="{{ $link }}" class="mr-1 mb-1" external />
                @endforeach
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
                    @endif
                @endauth
            </x-slot:actions>
        </x-card>
    </div>
</div>
