<div class="w-screen max-w-xs sm:max-w-md h-fit flex flex-col space-x-0 space-y-4">
    <x-tabs wire:model="selectedTab" class="w-full justify-stretch justify-items-stretch content-stretch items-stretch">
        <x-tab name="profile" label="Profile" icon="o-users">
            <x-form wire:submit="updateProfile">
                <x-file wire:model="avatar" label="Avatar" hint="Select an image" accept="image/*" crop-after-change>
                    <img src="{{ $avatar ?? asset('storage/avatars/default-avatar-white.svg') }}" class="h-40" />
                </x-file>
                <x-input type="text" label="Name" icon="o-user" hint="Your full name" wire:model="name" error-field="name" clearable />
                <x-datepicker label="Date" wire:model="dob" icon="o-calendar" hint="Your date of birth" error-field="dob" :config="['dateFormat' => 'Y-m-d']" />
                <x-tags label="Links" wire:model="links" icon="o-link" hint="Hit enter to create a new link" error-field="links" />
                <x-input type="text" label="Status" icon="o-pencil" hint="Your status" wire:model="status" error-field="status" clearable />
             
                <x-slot:actions>
                    <x-button label="Cancel" class="btn-secondary" wire:click="cancelUpdateProfile" />
                    <x-button label="Update" class="btn-primary" type="submit" spinner="updateProfile" />
                </x-slot:actions>
            </x-form>
        </x-tab>
        <x-tab name="password" label="Password" icon="o-sparkles">
            <x-form wire:submit="updatePassword">
                <x-input x-bind:type="show_password ? 'text' : 'password'" label="Password" icon="o-lock-closed" hint="Secure password" wire:model="password" error-field="password" clearable />
                <div class="w-fit ml-auto">
                    <x-toggle label="Show password" x-model="show_password" right tight />
                </div>
                <x-input x-bind:type="show_password_confirmation ? 'text' : 'password'" label="Password confirmation" icon="o-lock-closed" hint="Same password" wire:model="password_confirmation" error-field="password_confirmation" clearable />
                <div class="w-fit ml-auto">
                    <x-toggle label="Show password" x-model="show_password_confirmation" right tight />
                </div>
             
                <x-slot:actions>
                    <x-button label="Cancel" class="btn-secondary" wire:click="cancelUpdatePassword" />
                    <x-button label="Change" class="btn-primary" type="submit" spinner="updatePassword" />
                </x-slot:actions>
            </x-form>
        </x-tab>
    </x-tabs>
</div>
