@props(['color' => 'blue'])

@php
    $classes = 'px-3 py-1 rounded-full text-sm font-semibold whitespace-nowrap';
    $chipColor = 'text-'.$color.'-900'.' '.'bg-'.$color.'-200';
@endphp
<span {{ $attributes->merge([ 'class' =>  $chipColor.' '. $classes]) }}>
    {{$slot}}
</span>