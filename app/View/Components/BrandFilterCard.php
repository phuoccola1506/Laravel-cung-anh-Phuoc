<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Brand;

class BrandFilterCard extends Component
{
    public $brand;

    /**
     * Create a new component instance.
     */
    public function __construct(Brand $brand)
    {
        $this->brand = $brand;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.brand-filter-card');
    }
}
