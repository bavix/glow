<?php

namespace App\Observers;

use App\Jobs\ViewChain;
use App\Jobs\ViewDrop;
use App\Models\View;

class ViewObserver
{

    /**
     * @param View $view
     */
    public function created(View $view): void
    {
        ViewChain::dispatch($view);
    }

    /**
     * @param View $view
     */
    public function updated(View $view): void
    {
        ViewChain::dispatch($view);
    }

    /**
     * @param View $view
     * @return bool
     */
    public function deleting(View $view): bool
    {
        ViewDrop::dispatch($view);
        return false;
    }

}
