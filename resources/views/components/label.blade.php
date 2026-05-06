@props(['for' => null, 'required' => false])
<label for="{{$for}}" class="font-semibold mb-1 text-slate-600 dark:text-slate-300">
   {{ $slot }}
   @if($required)
      <span class="text-red-600">*</span>
   @endif
</label>