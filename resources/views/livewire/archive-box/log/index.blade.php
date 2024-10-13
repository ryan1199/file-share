<div class="w-screen max-w-xs sm:max-w-md md:max-w-lg flex flex-col space-x-0 space-y-2">
    <x-toggle label="Oldest" wire:model.live="sort" class="ml-auto"/>
    <x-range wire:model.live.debounce="perPage" min="1" max="100" step="1" label="Show logs per page" hint="{{ 'Showing logs per page: '.$perPage }}" />
    @foreach ($logs as $log)
        <x-collapse wire:key="{{ rand() }}">
            <x-slot:heading class="!font-black">
                {{ $log->created_at }}
            </x-slot:heading>
            <x-slot:content>
                <div class="tracking-widest leading-loose">
                    <span class="font-semibold">
                        @if ($log->user != null)
                            <x-button label="{{ $log->user->slug }}/{{ $log->user->name }}" tooltip="{{ $log->user->slug }}/{{ $log->user->name }}" link="{{ route('user.show', $log->user->slug) }}" />
                        @else
                            <x-button label="{{ $log->user_slug }}/{{ $log->user_name }}" tooltip="User already deleted" />
                        @endif
                    </span>
                    <span>: {{ $log->message }}</span>
                </div>
            </x-slot:content>
        </x-collapse>
    @endforeach
    <x-pagination :rows="$logs" wire:model.live="perPage" />
</div>
