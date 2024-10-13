<x-card shadow>
    <x-header title="Popular Archive Boxes" subtitle="Here are some popular archive boxes" separator />
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-2">
        @forelse ($archiveBoxes as $archiveBox)
            <x-card class="shadow-lg" wire:key="{{ rand() }}">
                <x-slot:title>
                    <x-popover>
                        <x-slot:trigger>
                            {{ Str::words($archiveBox->name, 3, ' ...') }}
                        </x-slot:trigger>
                        <x-slot:content>
                            {{ $archiveBox->name }}
                        </x-slot:content>
                    </x-popover>
                </x-slot:title>
                <x-collapse>
                    <x-slot:heading>
                        Description
                    </x-slot:heading>
                    <x-slot:content>
                        {{ $archiveBox->description }}
                    </x-slot:content>
                </x-collapse>
            
                <x-slot:figure>
                    <img src="{{ asset('storage/covers/'.$archiveBox->cover) }}" />
                </x-slot:figure>
                <x-slot:menu>
                    <x-button icon="o-users" tooltip="{{ Number::format($archiveBox->users_count).' users joined' }}" class="btn-circle btn-xl relative">
                        <x-badge value="{{ Number::abbreviate($archiveBox->users_count) }}" class="badge-info absolute -right-2 -top-2" />
                    </x-button>
                    <x-button icon="o-clipboard-document" tooltip="{{ Number::format($archiveBox->files_count).' files stored' }}" class="btn-circle btn-xl relative">
                        <x-badge value="{{ Number::abbreviate($archiveBox->files_count) }}" class="badge-info absolute -right-2 -top-2" />
                    </x-button>
                </x-slot:menu>
                <x-slot:actions>
                    <x-button label="see" link="{{ route('archive-box.show', $archiveBox->slug) }}" class="btn-primary" />
                </x-slot:actions>
            </x-card>
        @empty
            
        @endforelse
    </div>
</x-card>
