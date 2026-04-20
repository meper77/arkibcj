<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-uitm-purple-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-uitm-sm hover:bg-uitm-purple-800 active:bg-uitm-purple-900 focus:outline-none focus:ring-2 focus:ring-uitm-purple-300 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
