<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\ArchiveBox;
use App\Models\File;
use App\Models\Profile;
use App\Models\User;
use App\Policies\ArchiveBoxPolicy;
use App\Policies\FilePolicy;
use App\Policies\ProfilePolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Profile::class => ProfilePolicy::class,
        ArchiveBox::class => ArchiveBoxPolicy::class,
        File::class => FilePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
