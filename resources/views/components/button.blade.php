

@props(['to' => null, 'outline' => false])
@php
$classes = "px-3 py-2 inline-flex gap-3 border items-center justify-center rounded-lg shadow-md transition-all capitalize";
$fillClassess = "border-darkblue dark:border-skyblue bg-darkblue dark:bg-skyblue text-white hover:bg-lightblue hover:border-lightblue";
$outlineClassess = "border-darkblue text-darkblue hover:bg-darkblue hover:text-white hover:border-darkblue dark:border-blue-500 dark:text-blue-400 ";

@endphp

@if ($to)
    <a {{ $attributes->merge(['href' => $to, 'class' =>  ($outline ? $outlineClassess : $fillClassess) . ' '. $classes]) }}>
    {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'button', 'class' => ($outline ? $outlineClassess : $fillClassess) . ' '. $classes]) }}>
    {{ $slot }}
    </button>
@endif