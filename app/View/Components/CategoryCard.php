<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Category;

class CategoryCard extends Component
{
    public $category;

    /**
     * Create a new component instance.
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.category-card');
    }
}
