<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

/** Merender layout utama aplikasi. */
class AppLayout extends Component
{
    /** Menentukan view layout utama beserta datanya. */
    public function render(): View
    {
        return view('layouts.app');
    }
}









