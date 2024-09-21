<div class="w-full h-fit flex flex-col space-x-0 space-y-4">
    <x-collapse>
        <x-slot:heading>
            Profile
        </x-slot:heading>
        <x-slot:content>
            <div class="w-full h-fit">
                <x-card title="Update Profile" subtitle="Update your profile" separator progress-indicator="updateProfile">
                    <x-form wire:submit="updateProfile">
                        <x-file wire:model="avatar" label="Avatar" hint="Select an image" accept="image/*" crop-after-change>
                            <img src="{{ $avatar ?? asset('storage/avatars/default-avatar-white.svg') }}" class="h-40 rounded-lg" />
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
                </x-card>
            </div>
        </x-slot:content>
    </x-collapse>
    <x-collapse>
        <x-slot:heading>
            Password
        </x-slot:heading>
        <x-slot:content>
            <div x-data="{ show_password: false, show_password_confirmation: false }" class="w-full h-fit">
                <x-card title="Change Password" subtitle="Change your password" separator progress-indicator="updatePassword">
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
                </x-card>
            </div>
        </x-slot:content>
    </x-collapse>
</div>
