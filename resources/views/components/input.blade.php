@props(['disabled' => false, 'type' => 'text'])

@php
$classes = 'rounded border-gray-300 dark:border-gray-700  dark:bg-stone-800 dark:text-slate-200 ';
$typeFile = 'file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-base file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 focus:ring-0 focus:outline-0 file:cursor-pointer';
$typeText = 'focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:focus:ring-gray-700';
@endphp


<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => ($type == "file" ? $typeFile : $typeText) . ' '. $classes, 'type'=> $type]) !!} />
