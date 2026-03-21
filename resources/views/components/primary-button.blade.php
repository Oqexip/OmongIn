<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex justify-center items-center px-4 py-2 bg-black dark:bg-white border border-transparent rounded-xl font-semibold text-sm text-white dark:text-black tracking-wide hover:bg-neutral-800 dark:hover:bg-neutral-200 focus:outline-none focus:ring-2 focus:ring-black dark:focus:ring-white focus:ring-offset-2 dark:focus:ring-offset-neutral-900 transition ease-in-out duration-200 shadow-sm']) }}>
    {{ $slot }}
</button>
