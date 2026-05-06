@props(['data' => null])
@php
$assignee = $data?->collaborators?->where('flag', '0');
$reviewers = $data?->collaborators?->where('flag', '1');
// dump($data)
@endphp

@if($data)
<div class="bg-white rounded-lg border border-gray-100 grid pt-5 h-auto relative min-w-[360px] max-w-[420px]">
    <div class="absolute top-2 right-0">
        @if($data->priority == '2')
        <div class="text-white bg-red-500 pl-3 pr-4 py-1 rounded-l-full flex gap-x-3 items-center">
            <div class="w-2 h-2 bg-red-100 rounded-full outline outline-red-300 animate-pulse"></div>
            <span class="text-sm font-semibold whitespace-nowrap">
                Urgent
            </span>
        </div>
        @elseif($data->priority == '1')
        <div class="text-white bg-yellow-500 pl-3 pr-4 py-1 rounded-l-full flex gap-x-3 items-center">
            <div class="w-2 h-2 bg-yellow-100 rounded-full outline outline-yellow-300 animate-pulse"></div>
            <span class="text-sm font-semibold whitespace-nowrap">
                High
            </span>
        </div>
        @endif
    </div>
    <div class="flex items-start flex-col gap-1 px-5 mb-4">
        <div class="flex items-center gap-x-2 w-full divide-x">
            <span class="text-gray-500"><strong>#{{$data->id}}</strong></span>
            <span class="text-gray-500 text-sm pl-2 line-clamp-1">{{$data->projectName}}</span>
        </div>
        <div class=" w-full">
            <button class="style-none" data-modal="modal-drawer" data-target="#task_card_modal_slider"
                data-id="{{$data->id}}">
                <h4 class="mb-2 text-lg font-semibold capitalize text-darkblue text-left hover:underline transition-all">
                    {{$data->title}}
                </h4>
            </button>

            <div class="line-clamp-2 text-gray-500 whitespace-normal dark:text-gray-300 truncate break-all">
                {!! html_entity_decode($data->description) !!}
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 px-5 mb-4">
        <div class="flex flex-col">
            <span class="text-gray-500 whitespace-normal dark:text-gray-300 text-sm">
                Assignee
            </span>
            <div class="flex">
                @if($assignee)
                <div class="flex">
                    @foreach( $assignee as $elem )
                    <div
                        class="h-8 w-8 bg-gray-500 rounded-full border-2 shadow border-white overflow-hidden relative z-0 text-center last:mr-0 -mr-2.5 text-white text-xs flex items-center justify-center">

                        @if($elem->profile_picture)
                        <img src="/storage/profiles/{{$elem->profile_picture }}"
                            class="w-10 h-10 rounded-full object-cover object-center" src="path/to/profile/image.jpg"
                            alt="{{$elem->profile_picture}}">
                        @else
                        <span class=" tracking-wider">
                            <?php
                        $userName = explode(' ',$elem->name);
                        echo substr($userName[0], 0, 1) . substr($userName[1], 0, 1);
                        ?>
                        </span>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        <div class="flex flex-col items-end">
            <span class="text-gray-500 whitespace-normal dark:text-gray-300 text-sm">
                Reviewers
            </span>
            <div class="flex">
                @if($reviewers)
                <div class="flex">
                    @foreach( $reviewers as $elem )
                    <div
                        class="h-8 w-8 bg-gray-500 rounded-full border-2 shadow border-white overflow-hidden relative z-0 text-center last:mr-0 -mr-2.5 text-white text-xs flex items-center justify-center">
                        @if($elem->profile_picture)
                        <img src="/storage/profiles/{{$elem->profile_picture }}"
                            class="w-10 h-10 rounded-full object-cover object-center" src="path/to/profile/image.jpg"
                            alt="{{$elem->profile_picture}}">
                        @else
                        <span class=" tracking-wider">
                            <?php
                        $userName = explode(' ',$elem->name);
                        echo substr($userName[0], 0, 1) . substr($userName[1], 0, 1);
                        ?>
                        </span>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between gap-2 px-5 border-t py-3">
        <div class="flex flex-col">
            <span class="text-gray-500 whitespace-normal dark:text-gray-300 text-sm">
                Created At
            </span>
            <span class="text-gray-600 whitespace-normal dark:text-gray-300">
                {{$data->created_at->diffInHours(now())}}
                <!-- {{$data->created_at->format('m/d/Y h:i A')}} -->
            </span>
        </div>
        <div class="flex flex-col">
            <span class="text-gray-500 whitespace-normal dark:text-gray-300 text-sm">
                Deadline
            </span>
            <span class="text-gray-600 whitespace-normal dark:text-gray-300">
                {{$data->deadline ? \Carbon\Carbon::parse($data->deadline)->format('m/d/Y h:i A') : '-'}}
            </span>
        </div>
    </div>

</div>

@endif