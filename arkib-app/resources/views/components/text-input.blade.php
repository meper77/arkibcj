@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-stone-300 focus:border-uitm-purple-700 focus:ring-uitm-purple-200 rounded-md shadow-sm transition duration-150 ease-in-out disabled:bg-stone-100 disabled:text-stone-500 disabled:cursor-not-allowed placeholder:text-stone-400']) }}>
