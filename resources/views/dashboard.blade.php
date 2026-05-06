@extends('layouts.app')
<script>
    setTimeout(function () {
        location.reload();
    }, 600000); //1000 milli second = 1second
</script>
@php
$assignedTask = $data->where('status', '0');
$inProcessTask = $data->where('status', '1');
$assignedForReviewTask = $data->where('status', '2');
$reviewingTask = $data->where('status', '3');
// dump($data);
@endphp

@section('content')
<div id="root" class="gap-4 flex h-[calc(100vh_-_69px)] overflow-x-auto overflow-y-hidden bg-white">
    <div class="flex gap-5 px-5 pt-4 items-start">
        
        <div class="min-w-[360px] max-w-[420px] h-full flex flex-col bg-gray-100 rounded-lg overflow-hidden">
            <div class="inline-flex items-center gap-x-3  pt-4 pb-3 px-5 border-b border-b-white">
                <div class="inline-flex gap-3 items-center">
                    <div class="bg-gray-500 w-3 h-3 rounded-full"></div>
                    <h4 class="text-lg font-semibold sm:text-xl dark:text-gray-200 uppercase ">
                        Assigned
                    </h4>
                </div>
                
                <div class="rounded-full h-6  bg-gray-300 flex items-center justify-center font-semibold text-sm  px-3">
                    <span>1</span>
                </div>
            </div>
            @if (count($assignedTask) > 0)
            <div class="grid gap-y-3 overflow-y-auto hide_scrollbar p-4">
                @foreach ( $assignedTask as $key => $task )
                <x-task-card :data="$task"></x-task-card>
                @endforeach
            </div>
            @endif
        </div>

        
        <div class="min-w-[360px] max-w-[420px] h-full flex flex-col bg-sky-50 rounded-lg overflow-hidden">
            <div class="inline-flex items-center gap-x-3  pt-4 pb-3 px-5 border-b border-b-white">
                <div class="inline-flex gap-3 items-center">
                    <div class="bg-sky-500 w-3 h-3 rounded-full"></div>
                   
                    <h4 class="text-lg font-semibold sm:text-xl dark:text-gray-200 uppercase ">
                        On Process
                    </h4>
                </div>
                
                <div class="rounded-full h-6  bg-sky-300 flex items-center justify-center font-semibold text-sm  px-3">
                    <span>1</span>
                </div>
            </div>
            @if (count($inProcessTask) > 0)
            <div class="grid gap-y-3 overflow-y-auto hide_scrollbar p-4">
                @foreach ( $inProcessTask as $key => $task )
                <x-task-card :data="$task"></x-task-card>
                @endforeach
            </div>
            @endif
        </div>

       
        <div class="min-w-[360px] max-w-[420px] h-full flex flex-col bg-lime-50 rounded-lg overflow-hidden">
            <div class="inline-flex items-center gap-x-3  pt-4 pb-3 px-5 border-b border-b-white">
                <div class="inline-flex gap-3 items-center">
                    <div class="bg-lime-500 w-3 h-3 rounded-full"></div>
                   
                    <h4 class="text-lg font-semibold sm:text-xl dark:text-gray-200 uppercase ">
                        Assigned For Review
                    </h4>
                </div>
                
                <div class="rounded-full h-6  bg-lime-300 flex items-center justify-center font-semibold text-sm  px-3">
                    <span>1</span>
                </div>
            </div>
            @if (count($assignedForReviewTask) > 0)
            <div class="grid gap-y-3 overflow-y-auto hide_scrollbar p-4">
                @foreach ( $assignedForReviewTask as $key => $task )
                <x-task-card :data="$task"></x-task-card>
                @endforeach
            </div>
            @endif
        </div>

        
        <div class="min-w-[360px] max-w-[420px] h-full flex flex-col bg-orange-50 rounded-lg overflow-hidden">
            <div class="inline-flex items-center gap-x-3  pt-4 pb-3 px-5 border-b border-b-white">
                <div class="inline-flex gap-3 items-center">
                    <div class="bg-orange-500 w-3 h-3 rounded-full"></div>
                   
                    <h4 class="text-lg font-semibold sm:text-xl dark:text-gray-200 uppercase ">
                        Reviewing
                    </h4>
                </div>
                
                <div class="rounded-full h-6  bg-orange-300 flex items-center justify-center font-semibold text-sm  px-3">
                    <span>1</span>
                </div>
            </div>
            @if (count($assignedForReviewTask) > 0)
            <div class="grid gap-y-3 overflow-y-auto hide_scrollbar p-4">
                @foreach ( $assignedForReviewTask as $key => $task )
                <x-task-card :data="$task"></x-task-card>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

<x-modal-drawer target="task_card_modal_slider"></x-modal-drawer>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

@endsection

<style>
    /* Hide scrollbar for Chrome, Safari and Opera */
    .hide_scrollbar::-webkit-scrollbar {
        display: none;
    }

    /* Hide scrollbar for IE, Edge and Firefox */
    .hide_scrollbar {
        -ms-overflow-style: none;
        /* IE and Edge */
        scrollbar-width: none;
        /* Firefox */
    }
</style>