@props(['heading' => null])
<header>
    <div class="flex justify-between mb-2 items-center mt-2 dark:bg-neutral-800">
        <div class="flex flex-col">
            <h3 class="text-xl font-bold mb-2 text-gray-800 dark:text-gray-300">{{$heading}}</h3>
                <nav>
                    <ol class="flex items-center gap-x-1.5">
                         <svg xmlns="http://www.w3.org/2000/svg" width="12.5" height="12.5" fill="currentColor" class="bi bi-house" viewBox="0 0 16 16">
                            <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.707 1.5ZM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l5-5 5 5Z"/>
                        </svg>
                        <li class="breadcrumb-item">
                            <a class="flex items-center text-gray-500 dark:text-gray-300 cursor-pointer text-sm" href="/dashboard">Dashboard</a>
                        </li>
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-chevron-right dark:text-gray-300" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                        </svg>
                        <li class="breadcrumb-item font-bold text-gray-500 dark:text-gray-300 text-[12px]" aria-current="page">{{$heading}}</li>
                    </ol>
                </nav>
            </div>
        <div>
    {{ $slot }}
    </div>
    </div>
</header>    