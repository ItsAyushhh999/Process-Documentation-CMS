@props(['target', 'title', 'slot'])

<!-- overlay -->
<div id={{$target}} class="hidden fixed z-40 inset-0 bg-black/10 h-screen w-full trigger_slider_modal transition-opacity duration-200">
  <div class="modal-drawer absolute bg-white right-0 bottom-0 left-0 w-full h-[94vh] transition-all ease-in-out rounded-t-xl">
    <div class="relative">
      <button class="text-primary-600 rounded-md text-2xl cursor-pointer p-1 dark:text-gray-400 z-20 absolute right-4 top-4" data-dismiss="modal-drawer">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
          stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
      <div id="modal_drawer"></div>

    </div>
 

    
   
      <!-- <div class="flex justify-between items-center pt-5 pl-5 pr-10 pb-4 border-b relative">
        @if(isset($header))
          {{$header}}
        @endif
       
      </div>
      
     
      
    
    <div class="overflow-y-auto h-[calc(96vh-62px)] px-5">
      {{$slot}}
    </div> -->

  

  </div>
</div>