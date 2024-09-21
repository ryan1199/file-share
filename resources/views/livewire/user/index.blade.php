<div>
    
    <x-header title="User List" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Filters" @click="$wire.drawer = true" responsive icon="o-funnel" class="btn-primary" />
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-table :headers="$this->headers" :rows="$users" :sort-by="$sortBy" with-pagination >
            @scope('cell_name', $user)
                <x-avatar :image="asset('storage/avatars/'.$user->avatar)" :title="$user->name" />
            @endscope
            @scope('cell_slug', $user)
                <x-button label="{{ $user->slug }}" link="{{ route('user.show', $user->slug) }}" icon="o-user" tooltip="{{ 'Visit '.$user->name }}" responsive />
            @endscope
            @scope('cell_email', $user)
                <x-button label="{{ $user->email }}" link="{{ 'mailto:'.$user->email }}" icon="o-envelope" tooltip="{{ 'Mail '.$user->email }}" external responsive />
            @endscope
        </x-table>
    </x-card>

    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <x-input placeholder="Search..." wire:model.live.debounce="search" icon="o-magnifying-glass" @keydown.enter="$wire.drawer = false" />

        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer>
    {{-- <div class="w-full h-fit">
        @foreach ($this->users as $user)
            @php
                $user->avatar = asset('storage/avatars/'.$user->avatar);
            @endphp
            <x-list-item :item="$user" value="name" sub-value="slug" avatar="avatar" link="{{ route('user.show', $user->slug) }}" wire:key="{{ rand() }}" class="bg-base-100 rounded-box">
                <x-slot:actions>
                    <div class="stats shadow">
                        <div class="stat">
                          <div class="stat-figure text-secondary">
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              fill="none"
                              viewBox="0 0 24 24"
                              class="inline-block h-8 w-8 stroke-current">
                              <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                          </div>
                          <div class="stat-title">Downloads</div>
                          <div class="stat-value">31K</div>
                          <div class="stat-desc">Jan 1st - Feb 1st</div>
                        </div>
                      
                        <div class="stat">
                          <div class="stat-figure text-secondary">
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              fill="none"
                              viewBox="0 0 24 24"
                              class="inline-block h-8 w-8 stroke-current">
                              <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                            </svg>
                          </div>
                          <div class="stat-title">New Users</div>
                          <div class="stat-value">4,200</div>
                          <div class="stat-desc">↗︎ 400 (22%)</div>
                        </div>
                      
                        <div class="stat">
                          <div class="stat-figure text-secondary">
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              fill="none"
                              viewBox="0 0 24 24"
                              class="inline-block h-8 w-8 stroke-current">
                              <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                            </svg>
                          </div>
                          <div class="stat-title">New Registers</div>
                          <div class="stat-value">1,200</div>
                          <div class="stat-desc">↘︎ 90 (14%)</div>
                        </div>
                      </div>
                </x-slot:actions>
            </x-list-item>
        @endforeach
    </div> --}}
</div>
