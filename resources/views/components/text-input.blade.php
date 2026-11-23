@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-pink-200 focus:border-pink-400 focus:ring-pink-200 rounded-md shadow-sm']) }}>
