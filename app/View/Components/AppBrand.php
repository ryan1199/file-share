<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AppBrand extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return <<<'HTML'
                <a href="{{ route('home') }}" wire:navigate>
                    <!-- Hidden when collapsed -->
                    <div {{ $attributes->class(["hidden-when-collapsed"]) }}>
                        <div class="flex items-center gap-2">
                            <x-icon name="o-archive-box" class="w-6 -mb-0 text-primary" />
                            <span class="font-bold text-3xl me-3 bg-gradient-to-tr from-primary via-secondary to-accent bg-clip-text text-transparent ">
                                File-Share
                            </span>
                        </div>
                    </div>

                    <!-- Display when collapsed -->
                    <div class="display-when-collapsed hidden mx-5 mt-4 lg:mb-0 h-[28px]">
                        <x-icon name="s-archive-box" class="w-6 -mb-0 mx-auto text-primary" />
                    </div>
                </a>
            HTML;
    }
}
