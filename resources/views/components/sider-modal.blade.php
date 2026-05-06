
<div id={{$target}} class="hidden fixed z-10 inset-0 bg-opacity-30 h-screen w-full flex justify-center items-start md:items-center pt-10 md:pt-0 trigger_sider">
    <div class="sider opacity-0 translate-x-full absolute right-0 top-0 h-screen w-1/2  block z-20 transition-all duration-150 ease-linear shadow-md bg-white dark:bg-neutral-900 bg-opacity-80 backdrop-filter backdrop-blur-xl overflow-hidden Class Properties
" >
    
        <div class="h-full relative">
            <span class="absolute top-3 right-4 text-primary-600 dark:text-gray-300 rounded-md text-2xl cursor-pointer p-1" data-dismiss="sider">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </span>
            {{$slot}}
        </div>
    </div>
</div>