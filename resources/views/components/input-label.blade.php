@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-neutral-700 dark:text-neutral-300 mb-1']) }}>
    {{ $value ?? $slot }}
</label>
