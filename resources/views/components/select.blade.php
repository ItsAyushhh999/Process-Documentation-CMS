@props(['multiple' => false])

@if($multiple)
  <select class="w-full mb-5 border-gray-300
  dark:bg-stone-800 dark:text-slate-200
  rounded-md shadow-sm form-select focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 multiselect" name="assignees[]" multiple="multiple"   {{ $attributes }}>>
  {{ $slot }}
  </select>
@else
  <select class="rounded-md shadow-sm 
  border-gray-300 dark:border-gray-700 
  focus:border-indigo-300 focus:ring focus:ring-blue-200 dark:focus:ring-gray-700 focus:ring-opacity-50 
  dark:bg-stone-800
  dark:text-slate-200
  "
  {{ $attributes }}>
    {{ $slot }}
  </select>
@endif
<!-- 
<style>

</style> -->


