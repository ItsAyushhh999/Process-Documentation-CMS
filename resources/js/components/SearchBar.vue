<script setup></script>

<template>
    <div
        class="w-full lg:block hidden justify-center border border-gray-300 rounded-full dark:border-gray-400 dark:bg-stone-800 dark:border focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:focus:ring-gray-700"
    >
        <form method="POST" action="http://127.0.0.1:8000/search" class="mb-0" id="searchByForm">
            <!-- <meta name="csrf-token" content="np7P1TDUoHpdAXyfTwmOm0U1X3Ysd0uzRvqdPvEA"> -->
            <input type="hidden" name="_token" value="np7P1TDUoHpdAXyfTwmOm0U1X3Ysd0uzRvqdPvEA" />
            <input type="hidden" name="_method" value="GET" />
            <div
                class="w-full sm:flex space-x-2 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:focus:ring-gray-700"
            >
                <span class="relative w-full flex mb-2 sm:mb-0 ml-3 sm:ml-0 items-center">
                    <input
                        class="w-full form-control rounded-l-full bg-transparent border-transparent placeholder:text-gray-400 dark:text-gray-400 focus:outline-0 focus:ring-0 focus:border-transparent focus:ring-blue-200 focus:ring-opacity-50 dark:focus:ring-gray-700"
                        type="search"
                        name="search"
                        placeholder="Search..."
                        aria-label="Search Projects"
                        required=""
                        value=""
                    />
                    <span class="border border-slate-300 h-6"></span>
                </span>
                <select
                    class="flex-grow-0 flex-shrink-0 w-auto text-sm bg-transparent border-transparent dark:text-gray-400"
                    id="searchBy"
                    name="type"
                >
                    <option value="projects">Projects</option>
                    <option value="documents">Documents</option>
                </select>
                <button
                    type="submit"
                    class="border-darkblue dark:border-skyblue bg-darkblue dark:bg-skyblue text-white hover:bg-lightblue hover:border-lightblue px-3 py-2 inline-flex gap-3 border items-center justify-center rounded-lg shadow-md transition-all capitalize px-4 rounded-r-full"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="14"
                        height="14"
                        viewBox="0 0 16 16"
                        role="img"
                        class="fill-current"
                    >
                        <path
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M10.6002 12.0498C9.49758 12.8568 8.13777 13.3333 6.66667 13.3333C2.98477 13.3333 0 10.3486 0 6.66667C0 2.98477 2.98477 0 6.66667 0C10.3486 0 13.3333 2.98477 13.3333 6.66667C13.3333 8.15637 12.8447 9.53194 12.019 10.6419C12.0265 10.6489 12.0338 10.656 12.0411 10.6633L15.2935 13.9157C15.6841 14.3063 15.6841 14.9394 15.2935 15.33C14.903 15.7205 14.2699 15.7205 13.8793 15.33L10.6269 12.0775C10.6178 12.0684 10.6089 12.0592 10.6002 12.0498ZM11.3333 6.66667C11.3333 9.244 9.244 11.3333 6.66667 11.3333C4.08934 11.3333 2 9.244 2 6.66667C2 4.08934 4.08934 2 6.66667 2C9.244 2 11.3333 4.08934 11.3333 6.66667Z"
                        ></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</template>
