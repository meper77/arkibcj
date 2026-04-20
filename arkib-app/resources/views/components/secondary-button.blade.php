<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-white border border-uitm-purple-300 rounded-md font-semibold text-xs text-uitm-purple-700 uppercase tracking-widest shadow-sm hover:bg-uitm-purple-50 hover:border-uitm-purple-400 focus:outline-none focus:ring-2 focus:ring-uitm-purple-200 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
