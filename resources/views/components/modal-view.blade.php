@props(['header', 'footer', 'target'])

<!-- overlay -->
<div id={{$target}} class="hidden fixed z-40 inset-0 bg-black bg-opacity-30 h-screen w-full flex justify-center items-start md:items-center pt-10 md:pt-0 trigger_model" data-dismiss="modal">
<!-- modal -->
<div class="modal opacity-0 transform scale-90  relative min-w-[360px]   max-h-[90vh] bg-white dark:bg-neutral-900 dark:border dark:border-gray-700 rounded overflow-hidden shadow-lg transition-all z-50">
    @if(isset($header))
    <div class="flex justify-between items-center pt-5 px-5 pb-2">
        <h1 class='text-xl text-gray-800 font-bold dark:text-gray-300 '>
        {{$header}}
        </h1>
        <a class=" text-primary-600 rounded-md text-2xl cursor-pointer p-1 dark:text-gray-400" data-dismiss="modal">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
        </a>
    </div>
    @endIf

    <div class="overflow-auto max-h-[calc(90vh-160px)] px-5">
        {{$slot}}
    </div>
    @if(isset($footer))
    <footer class="flex justify-end gap-x-3 p-5 items-center bg-gray-50">
         <a class=" text-gray-400 cursor-pointer hover:text-gray-600 py-2 px-4" data-dismiss="modal">Close</a>
        <div>
            {{$footer}}
        </div>
    </footer>
    @endif
</div>
</div>
