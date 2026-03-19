<?php

namespace App\Providers;

use App\Models\NavigationItem;
use App\Models\PageSection;
use App\Policies\NavigationItemPolicy;
use App\Policies\PageSectionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        NavigationItem::class => NavigationItemPolicy::class,
        PageSection::class    => PageSectionPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
