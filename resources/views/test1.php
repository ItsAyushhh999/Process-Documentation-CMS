@extends('layouts.app')
<script>
    setTimeout(function () {
        location.reload();
    }, 600000); //1000 milli second = 1second
</script>

@section('content')
<div class="grid gap-4 pt-4">
    <!-- <div class="py-5 border rounded-lg col-span-1 overflow-auto bg-white dark:bg-neutral-800 dark:border-neutral-700">
      
   </div> -->
    <!-- <div class="flex bg-red-50 min-h-[60vh]"> -->
    <div class="grid grid-cols-3 min-h-[60vh] gap-5 overflow-x-auto">

        <div class="min-w-[320px] p-3 border border-dashed">
            <div
                class="flex items-center gap-4 border-b-2 border-red-500 pb-2 sticky top-0 bg-white/50 backdrop-blur z-10"
            >
                <h4 class="text-lg font-medium sm:text-xl dark:text-gray-200 uppercase">
                    Assigned
                </h4>
                <div class="border rounded-full px-6 p-1">1</div>
            </div>
            <div class="grid gap-5 mt-4">
               @foreach ( $data as $key => $task )
                <div class="bg-white rounded-lg border grid pt-5 gap-y-6">
                    <div class="flex items-start flex-col gap-2 px-5">
                        <div class="flex gap-1 items-center justify-between w-full">
                            <!-- <x-chip color="red">Urgent</x-chip>
                           <div class="w-8 h-8 bg-blue-300 rounded-full flex justify-center items-center">SD</div> -->
                            <span class="text-darkblue"><strong>#11327</strong></span>
                            <x-chip color="yellow">Normal</x-chip>
                        </div>
                        <div class="">
                            <h4 class="pb-1 text-lg font-semibold capitalize dark:text-gray-200">
                                Dashboard UI update to Kanban Board style
                            </h4>
                            <span class="text-gray-500">Warehouse Management System API</span>
                        </div>
                    </div>

                    <div class="px-5 grid gap-2">
                        <div class="grid grid-cols-5">
                            <div class="col-span-2">
                                <span
                                    class="text-gray-500 whitespace-normal dark:text-gray-300 leading-6"
                                >
                                    Created By
                                </span>
                            </div>
                            <div class="col-span-3 inline-flex items-center gap-2">
                                <div
                                    class="h-8 w-8 bg-blue-300 rounded-full border-2 shadow border-white overflow-hidden relative z-0"
                                >
                                    <img
                                        src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=2080&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                        alt=""
                                        class="w-full h-full object-cover object-center"
                                    />
                                </div>
                                <span class="text-gray-600 whitespace-normal dark:text-gray-300">
                                    Suman Sunuwar
                                </span>
                            </div>
                        </div>
                        <div class="grid grid-cols-5">
                            <div class="col-span-2">
                                <span class="text-gray-500 whitespace-normal dark:text-gray-300">
                                    Created At
                                </span>
                            </div>
                            <div class="col-span-3">
                                <span class="text-gray-600 whitespace-normal dark:text-gray-300">
                                    12/29/2023 10:52 AM
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-5">
                            <div class="col-span-2">
                                <span class="text-gray-500 whitespace-normal dark:text-gray-300">
                                    Deadline
                                </span>
                            </div>
                            <div class="col-span-3">
                                <span class="text-gray-600 whitespace-normal dark:text-gray-300">
                                    12/29/2023 10:52 AM
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="px-5 pb-5 pt-2 flex justify-between border-t">
                        <div class="flex flex-col gap-1">
                            <span class="text-sm text-gray-500">Assignee:</span>
                            <div class="flex flex-row-reverse">
                                <div
                                    class="h-8 w-8 bg-blue-300 rounded-full border-2 shadow border-white overflow-hidden relative z-0"
                                >
                                    <img
                                        src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=2080&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                        alt=""
                                        class="w-full h-full object-cover object-center"
                                    />
                                </div>
                                <div
                                    class="h-8 w-8 bg-blue-300 rounded-full border-2 shadow border-white overflow-hidden -mr-2 relative z-0"
                                >
                                    <img
                                        src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=2080&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                        alt=""
                                        class="w-full h-full object-cover object-center"
                                    />
                                </div>
                                <div
                                    class="h-8 w-8 bg-blue-300 rounded-full border-2 shadow border-white overflow-hidden -mr-2 relative z-0"
                                >
                                    <img
                                        src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=2080&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                        alt=""
                                        class="w-full h-full object-cover object-center"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-sm text-gray-500">Reviewer:</span>
                            <div class="flex flex-row-reverse">
                                <div
                                    class="h-8 w-8 bg-blue-300 rounded-full border-2 shadow border-white overflow-hidden relative z-0"
                                >
                                    <img
                                        src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=2080&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                        alt=""
                                        class="w-full h-full object-cover object-center"
                                    />
                                </div>
                                <div
                                    class="h-8 w-8 bg-blue-300 rounded-full border-2 shadow border-white overflow-hidden -mr-2 relative z-0"
                                >
                                    <img
                                        src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=2080&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                        alt=""
                                        class="w-full h-full object-cover object-center"
                                    />
                                </div>
                                <div
                                    class="h-8 w-8 bg-blue-300 rounded-full border-2 shadow border-white overflow-hidden -mr-2 relative z-0"
                                >
                                    <img
                                        src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=2080&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                        alt=""
                                        class="w-full h-full object-cover object-center"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
               @endforeach
            </div>
        </div>

        <div class="min-w-[320px] p-3 border border-dashed">
            <div
                class="flex items-center gap-4 border-b-2 border-sky-500 pb-2 sticky top-0 bg-white/50 backdrop-blur z-10"
            >
                <h4 class="text-lg font-medium sm:text-xl dark:text-gray-200 uppercase">
                    In process
                </h4>
                <div class="border rounded-full px-6 p-1">1</div>
            </div>
            <div class="grid gap-5 mt-4">
               @foreach ( $data as $key => $task )
                <div class="bg-white rounded-lg border grid py-5 gap-y-5">
                    <div class="flex items-start flex-col gap-2 px-5">
                        <div class="flex gap-1 items-center justify-between w-full">
                            <!-- <x-chip color="red">Urgent</x-chip>
                           <div class="w-8 h-8 bg-blue-300 rounded-full flex justify-center items-center">SD</div> -->
                            <span class="text-darkblue"><strong>#11327</strong></span>
                            <x-chip color="yellow">Normal</x-chip>
                        </div>
                        <div class="">
                            <h4 class="pb-1 text-lg font-semibold capitalize dark:text-gray-200">
                                Dashboard UI update to Kanban Board style
                            </h4>
                            <span class="text-gray-500 text-sm"
                                >Warehouse Management System API</span
                            >
                        </div>
                    </div>
                    <div class="px-5">
                        <p class="line-clamp-2 text-gray-500 whitespace-normal dark:text-gray-300">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Error iure a
                            deserunt eaque quia maiores dolor blanditiis quam nulla cumque.
                        </p>
                    </div>

                    <div class="px-5 grid gap-x-3 gap-y-2 grid-cols-2">
                        <!-- <div class="flex flex-col">
                              <span class="text-gray-500 whitespace-normal dark:text-gray-300 text-sm">
                                 Created By
                              </span>
                           <div class="inline-flex items-center gap-2">
                              <div class="h-8 w-8 bg-blue-300 rounded-full border-2 shadow border-white overflow-hidden relative z-0">
                                 <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=2080&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="" class="w-full h-full object-cover object-center">
                              </div>
                              <span class="text-gray-600 whitespace-normal dark:text-gray-300 ">
                                 Suman Sunuwar
                              </span>
                           </div>
                        </div> -->
                        <div class="flex flex-col">
                            <span
                                class="text-gray-500 whitespace-normal dark:text-gray-300 text-sm"
                            >
                                Created At
                            </span>
                            <span class="text-gray-600 whitespace-normal dark:text-gray-300">
                                12/29/2023 10:52 AM
                            </span>
                        </div>
                        <div class="flex flex-col">
                            <span
                                class="text-gray-500 whitespace-normal dark:text-gray-300 text-sm"
                            >
                                Deadline
                            </span>
                            <span class="text-gray-600 whitespace-normal dark:text-gray-300">
                                12/29/2023 10:52 AM
                            </span>
                        </div>

                        <div class="flex flex-col">
                            <span
                                class="text-gray-500 whitespace-normal dark:text-gray-300 text-sm"
                            >
                                Assignee
                            </span>
                            <!-- <span class="text-gray-600 whitespace-normal dark:text-gray-300 ">
                              12/29/2023 10:52 AM
                           </span> -->
                            <div class="flex">
                                <div class="flex flex-row-reverse">
                                    <div
                                        class="h-8 w-8 bg-blue-300 rounded-full border-2 shadow border-white overflow-hidden relative z-0"
                                    >
                                        <img
                                            src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=2080&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                            alt=""
                                            class="w-full h-full object-cover object-center"
                                        />
                                    </div>
                                    <div
                                        class="h-8 w-8 bg-blue-300 rounded-full border-2 shadow border-white overflow-hidden -mr-2 relative z-0"
                                    >
                                        <img
                                            src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=2080&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                            alt=""
                                            class="w-full h-full object-cover object-center"
                                        />
                                    </div>
                                    <div
                                        class="h-8 w-8 bg-blue-300 rounded-full border-2 shadow border-white overflow-hidden -mr-2 relative z-0"
                                    >
                                        <img
                                            src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=2080&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                            alt=""
                                            class="w-full h-full object-cover object-center"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <span
                                class="text-gray-500 whitespace-normal dark:text-gray-300 text-sm"
                            >
                                Reviewers
                            </span>
                            <!-- <span class="text-gray-600 whitespace-normal dark:text-gray-300 ">
                              12/29/2023 10:52 AM
                           </span> -->
                            <div class="flex">
                                <div class="flex flex-row-reverse">
                                    <div
                                        class="h-8 w-8 bg-blue-300 rounded-full border-2 shadow border-white overflow-hidden relative z-0"
                                    >
                                        <img
                                            src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=2080&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                            alt=""
                                            class="w-full h-full object-cover object-center"
                                        />
                                    </div>
                                    <div
                                        class="h-8 w-8 bg-blue-300 rounded-full border-2 shadow border-white overflow-hidden -mr-2 relative z-0"
                                    >
                                        <img
                                            src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=2080&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                            alt=""
                                            class="w-full h-full object-cover object-center"
                                        />
                                    </div>
                                    <div
                                        class="h-8 w-8 bg-blue-300 rounded-full border-2 shadow border-white overflow-hidden -mr-2 relative z-0"
                                    >
                                        <img
                                            src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=2080&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                            alt=""
                                            class="w-full h-full object-cover object-center"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- <div class="min-w-[320px] p-3 border border-dashed">
            <div
                class="flex items-center gap-4 border-b-2 border-sky-500 pb-2 sticky top-0 bg-white/50 backdrop-blur z-10"
            >
                <h4 class="text-lg font-medium sm:text-xl dark:text-gray-200 uppercase">
                    Assgined to review
                </h4>
                <div class="border rounded-full px-6 p-1">1</div>
            </div>
            <div class="grid gap-5 mt-4">
               @foreach ( $data as $key => $task )
                <div class="bg-white rounded-lg border grid py-5 gap-y-3">
                    <div class="flex items-start flex-col gap-1 px-5">
                        <div class="flex gap-1 items-center justify-between w-full">
                            <span class="text-gray-500 text-sm"
                                >Warehouse Management System API</span
                            >
                            <span class="text-darkblue"><strong>#11327</strong></span>
                        </div>
                        <div class="">
                            <h4 class="pb-1 text-lg font-semibold capitalize dark:text-gray-200">
                                Dashboard UI update to Kanban Board style
                            </h4>
                        </div>
                    </div>
                    <div class="px-5">
                        <p class="line-clamp-2 text-gray-500 whitespace-normal dark:text-gray-300">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Error iure a
                            deserunt eaque quia maiores dolor blanditiis quam nulla cumque.
                        </p>
                    </div>

                    <div class="flex items-center px-5 justify-between">
                        <x-chip color="yellow">Normal</x-chip>

                        <div class="flex flex-row-reverse">
                            <div
                                class="h-8 w-8 bg-blue-300 rounded-full border-2 shadow border-white overflow-hidden relative z-0"
                            >
                                <img
                                    src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=2080&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                    alt=""
                                    class="w-full h-full object-cover object-center"
                                />
                            </div>
                            <div
                                class="h-8 w-8 bg-blue-300 rounded-full border-2 shadow border-white overflow-hidden -mr-2 relative z-0"
                            >
                                <img
                                    src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=2080&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                    alt=""
                                    class="w-full h-full object-cover object-center"
                                />
                            </div>
                            <div
                                class="h-8 w-8 bg-blue-300 rounded-full border-2 shadow border-white overflow-hidden -mr-2 relative z-0"
                            >
                                <img
                                    src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=2080&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                    alt=""
                                    class="w-full h-full object-cover object-center"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 px-5 border-t pt-2">
                        <div class="flex flex-col">
                            <span
                                class="text-gray-500 whitespace-normal dark:text-gray-300 text-sm"
                            >
                                Created At
                            </span>
                            <span class="text-gray-600 whitespace-normal dark:text-gray-300">
                                12/29/2023 10:52 AM
                            </span>
                        </div>
                        <div class="flex flex-col">
                            <span
                                class="text-gray-500 whitespace-normal dark:text-gray-300 text-sm"
                            >
                                Deadline
                            </span>
                            <span class="text-gray-600 whitespace-normal dark:text-gray-300">
                                12/29/2023 10:52 AM
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div> -->


       
        <div class="min-w-[320px] p-3 border border-dashed">
            <div
                class="flex items-center gap-4 border-b-2 border-sky-500 pb-2 sticky top-0 bg-white/50 backdrop-blur z-10"
            >
                <h4 class="text-lg font-medium sm:text-xl dark:text-gray-200 uppercase">
                    Reviewing
                </h4>
                <div class="border rounded-full px-6 p-1">1</div>
            </div>
            <div class="grid gap-5 mt-4">
               @foreach ( $data as $key => $task )
                <div class="bg-white rounded-lg border grid pt-5">
                    <div class="flex items-start flex-col gap-1 px-5 mb-6">
                        <div class="flex items-center gap-x-2 w-full divide-x">
                            <span class="text-darkblue"><strong>#11327</strong></span>
                            <span class="text-gray-500 text-sm pl-2 line-clamp-1"
                                >Warehouse Management System API</span
                            >
                        </div>
                        <div class="">
                            <h4
                                class="mb-2 text-lg font-semibold capitalize dark:text-gray-200"
                            >
                            Add feature to search for amazon prduct and see product detals in adminportal
                            </h4>

                            <p
                                class="line-clamp-2 text-gray-500 whitespace-normal dark:text-gray-300"
                            >
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Error iure
                                a deserunt eaque quia maiores dolor blanditiis quam nulla cumque.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center px-5 justify-between mb-2">
                        <x-chip color="yellow">Normal</x-chip>

                        <!-- <div class="flex flex-col gap-1"> -->
                        <div class="flex flex-row-reverse">
                            <div
                                class="h-8 w-8 bg-blue-300 rounded-full border-2 shadow border-white overflow-hidden relative z-0"
                            >
                                <img
                                    src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=2080&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                    alt=""
                                    class="w-full h-full object-cover object-center"
                                />
                            </div>
                            <div
                                class="h-8 w-8 bg-blue-300 rounded-full border-2 shadow border-white overflow-hidden -mr-2 relative z-0"
                            >
                                <img
                                    src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=2080&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                    alt=""
                                    class="w-full h-full object-cover object-center"
                                />
                            </div>
                            <div
                                class="h-8 w-8 bg-blue-300 rounded-full border-2 shadow border-white overflow-hidden -mr-2 relative z-0"
                            >
                                <img
                                    src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=2080&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                    alt=""
                                    class="w-full h-full object-cover object-center"
                                />
                            </div>
                        </div>
                        <!-- </div> -->
                    </div>

                    <div class="flex items-center justify-between gap-2 px-5 border-t py-2">
                        <div class="inline-flex items-center gap-2">
                            <div
                                class="h-8 w-8 bg-blue-300 rounded-full border-2 shadow border-white overflow-hidden relative z-0"
                            >
                                <img
                                    src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=2080&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                    alt=""
                                    class="w-full h-full object-cover object-center"
                                />
                            </div>
                            <span class="text-gray-600 whitespace-normal dark:text-gray-300">
                                Surndra.D
                            </span>
                        </div>

                        <span class="text-gray-600 whitespace-normal dark:text-gray-300">
                            12/29/2023 10:52 AM
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- </div> -->
</div>

@endsection
