@props(['category'])

<div class="mega-menu-category">
    <i class="fa-solid fa-heart"></i>
    <a class="text-dark text-decoration-none" href="{{ route('category.show', $category->id) }}">
        {{ $category->name }}
    </a>
</div>
