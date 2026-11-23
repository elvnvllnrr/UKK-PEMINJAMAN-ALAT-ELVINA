<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

/** Merender layout halaman pengunjung. */
class GuestLayout extends Component
{
    /** Menentukan view layout halaman pengunjung beserta datanya. */
    public function render(): View
    {
        return view('layouts.guest');
    }
}









