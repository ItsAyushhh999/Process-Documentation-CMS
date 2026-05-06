@extends('layouts.app')

@section('content')
    <x-header heading="Feeds">
        <div class="justify-end flex">
        </div>
    </x-header>

    <div class="container">
        <div class="p-5 border border-primary dark:border-gray-600 rounded-lg bg-white dark:bg-neutral-800">
            <div class="flex gap-x-4 items-end mb-2">
                <h3 class="text-xl font-bold dark:text-white">Latest Feeds:</h3>
            </div>
            <hr class=" border-gray-300 dark:bg-gray-700">
            <div id="feed-list">
            </div>

            @foreach($feeds as $feed)
                <div class="py-5 rounded overflow-y-auto max-h-[80vh] pr-1">
                    <div class="flex flex-col space-y-4">
                        <div class="flex space-x-6">
                            <div class="flex flex-col items-center mt-4">
                                <span class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white">
                                    <?php
                                         $userName = explode(' ', $feed->source);
                                         echo (strlen($feed->source) > 3) ? (count($userName) > 1 ? substr($userName[0], 0, 1) . substr($userName[1], 0, 1) : 'Bot') : $feed->source;
                                     ?>
                                </span>
                                <span class="w-[1px] block bg-gray-300 dark:bg-gray-600 grow mt-2"></span>
                            </div>
                            <div class="border border-gray-300 dark:border-gray-600 w-20 flex grow relative rounded-lg mt-4">
                                <div class="absolute border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-neutral-700 w-4 h-4 rotate-45 top-3 -left-2 z-10">
                                </div>
                                <div class="z-20 w-full h-full overflow-hidden rounded-lg">
                                    <div
                                        class="flex justify-between items-center bg-gray-100 dark:bg-neutral-700 py-2 px-3">
                                        <div class="flex gap-3">
                                        <span class="text-base dark:text-gray-300">
                                            <strong>{{$feed->source}}</strong>
                                        </span>
                                        </div>
                                        <div class="px-3 py-2 text-end flex items-center gap-2">
                                        <span class="text-base text-gray-500 dark:text-gray-300 pr-3">
                                           {{ $feed->created_at->format('F jS Y, h:i:s A')}}
                                        </span>
                                        </div>
                                    </div>
                                    <div class="px-3 py-2">
                                        <div class="scrolling-touch overflow-x-auto scroll-none">
                                        <span class="dark:text-slate-300 description  ">
                                            {{$feed->title}}
                                        </span>
                                        </div>
                                    </div>
                                    @include('feeds._partials.chips')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script src="{{ asset('js/moment.js') }}"></script>
    <script src="{{asset('js/feed.js')}}"></script>
@endsection
