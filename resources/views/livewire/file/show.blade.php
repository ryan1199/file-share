<div class="w-full container lg:max-w-3xl h-fit mx-auto grid grid-cols-1 gap-4">
    <div class="breadcrumbs text-sm">
        <ul>
            <li><a wire:navigate href="{{ route('welcome') }}">Home</a></li>
            <li><a wire:navigate href="{{ route('archive-box.index') }}">Archive Box</a></li>
            <li><a wire:navigate href="{{ route('archive-box.show', $file->archiveBox->slug) }}">{{ $file->archiveBox->slug }}</a></li>
            <li><a wire:navigate href="{{ route('file.show', $file->slug) }}">{{ $file->slug }}</a></li>
        </ul>
    </div>
    <x-card shadow>
        <x-collapse>
            <x-slot:heading>
                Description
            </x-slot:heading>
            <x-slot:content>
                {{ $file->description }}
            </x-slot:content>
        </x-collapse>
        <x-slot:title>
            {{ $file->name }}
        </x-slot:title>
        <x-slot:menu>
            <x-badge value="{{ $file->extension }}" class="badge-info whitespace-nowrap" />
            <x-badge value="{{ Number::fileSize($file->size) }}" class="badge-info whitespace-nowrap" />
        </x-slot:menu>
        <x-slot:actions>
            <x-button label="Download" link="{{ route('file.download', $file->slug) }}" icon="o-document-arrow-down" tooltip="{{ 'Download '.$file->name }}" no-wire-navigate responsive class="w-fit flex-nowrap" />
        </x-slot:actions>
    </x-card>
    <x-card shadow separator>
        <object data="{{ route('file.preview', $file->slug) }}" class="w-full h-full rounded-btn">Test</object>
    </x-card>
    <div class="stats stats-vertical sm:stats-horizontal shadow">
        <div class="stat">
            <div class="stat-figure text-secondary">
                <x-icon name="o-arrow-down-circle" class="w-8 h-8" />
            </div>
            <div class="stat-title">Downloads</div>
            <div class="stat-value">{{ Number::abbreviate($downloads) }}</div>
            @if ($downloads > 999)
                <div class="stat-desc">{{ Number::format($downloads) }}</div>
            @endif
        </div>
        
        <div class="stat">
            <div class="stat-figure text-secondary">
                <x-icon name="o-eye" class="w-8 h-8" />
            </div>
            <div class="stat-title">Views</div>
            <div class="stat-value">{{ Number::abbreviate($views) }}</div>
            @if ($views > 999)
                <div class="stat-desc">{{ Number::format($views) }}</div>
            @endif
        </div>
        
        <div @auth wire:click="like" @endauth class="stat">
            <div class="stat-figure text-secondary">
                <x-icon name="o-heart" class="w-8 h-8" />
            </div>
            <div class="stat-title">Likes</div>
            <div class="stat-value">{{ Number::abbreviate($likes) }}</div>
            @if ($this->file->likes->contains(Auth::id()))
                <div class="stat-desc">You liked it</div>
            @endif
        </div>
    </div>
</div>
