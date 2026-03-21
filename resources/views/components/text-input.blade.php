@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-neutral-300 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 focus:border-black dark:focus:border-white focus:ring-black dark:focus:ring-white rounded-xl shadow-sm']) }}>
