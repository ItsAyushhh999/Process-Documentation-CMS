@props(['image', 'name'])

@php
  $a=['red', 'green', 'blue', 'yellow', 'orange', 'emerald'];
  
  $bgColor='bg-' . $a[rand(0, 5)] . '-50';
  // dump($bgColor);
  $defaultClass ='inline-flex items-center gap-2 rounded-full pr-4';
@endphp

<div {{ $attributes->merge(['class' =>  $bgColor . ' '. $defaultClass]) }}>
  <div
    class="h-7 w-7 bg-blue-500 rounded-full shadow-lg overflow-hidden relative z-0 text-center text-white text-xs flex items-center justify-center">
    @if($image)
    <img src="/storage/profiles/{{$image }}" class="w-7 h-7 object-cover object-center" src="path/to/profile/image.jpg"
      alt="{{$image}}" />
    @else
    <span class=" tracking-wider">
      <?php
      $userName = explode(' ',$name);
      echo substr($userName[0], 0, 1) . substr($userName[1], 0, 1);
      ?>
    </span>
    @endif
  </div>
  <span class=" tracking-wider">
    <!-- {{$name}} -->
    <?php
      $userName = explode(' ',$name);
      echo $userName[0] . '.' . substr($userName[1], 0, 1);
      ?>
  </span>
</div>