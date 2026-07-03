@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 bg-gray-100 text-gray-900 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500 rounded-xl shadow-sm transition']) }}>
