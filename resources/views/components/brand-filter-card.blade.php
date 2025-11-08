@props(['brand'])

<label class="filter-checkbox">
    <input type="checkbox" name="brand" value="{{ $brand->id }}">
    <span>{{ $brand->name }}</span>
    <span class="count">({{ $brand->products_count }})</span>
</label>
