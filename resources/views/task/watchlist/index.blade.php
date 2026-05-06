@extends('layouts.app')

@section('content')
<x-header heading="Task WatchList">
</x-header>
    {{-- @dd($listTaskWatchLists) --}}
    @if ($listTaskWatchLists->count()>0)
<div class="py-5 bg-white dark:bg-neutral-800 border dark:border-gray-700 rounded-lg">
    <table class="w-full text-base cTable display nowrap ">
        <thead>
            <th></th>
            <th class="min-w-[90px]">Task Number</th>
            <th class="min-w-[300px]">Title</th>
            <th class="min-w-[150px]">Project</th>
            <th class="min-w-[200px]">Status</th>
            <!-- <th>Priority</th> -->
            @if(!$routeFlag)<th class="min-w-[120px]">Assignee(s)</th>@endif
            <th>Reviewer(s)</th>
            <!-- <th class="min-w-[120px]">Created At/By</th> -->
        </thead>
        <tbody>
            @foreach ( $listTaskWatchLists as $taskWatchList )
            <tr>
                <td>
                    <x-input type="checkbox" class="remove_watchlist" id="checkbox_{{ $taskWatchList->id}}"  data-modal="modal" data-target="#openTaskWatchListRemoveModal" data-id="{{ $taskWatchList->id}}"></x-input>
                </td>
                <td><a href="{{ route('tasks.edit', $taskWatchList->id) }}" class="text-darkblue font-semibold">{{ $taskWatchList->id }}</a></td>
                <td>
                    <div class="flex flex-col  gap-4">
                        <span class="font-semibold">
                            {{ $taskWatchList->title }}
                        </span>
                        <div class="flex items-center gap-x-1">
                            @if($taskWatchList->priority == 2)
                            <x-chip color="red">Urgent</x-chip>
                            @elseif($taskWatchList->priority == 1)
                            <x-chip color="yellow">High</x-chip>
                            @else
                            <x-chip color="green">Normal</x-chip>
                            @endif
                            @if($taskWatchList->createdBy)
                            <x-chip color="sky">
                                <?php
                                $userName = explode(' ', $taskWatchList->createdBy);
                                echo ($userName[0] . '.' . substr($userName[1], 0, 1));
                                ?>
                            </x-chip>
                            <span class="ml-5 whitespace-nowrap text-gray-500 dark:text-gray-400">
                                {{ $taskWatchList->created_at->format('m/d/Y h:i A') }}
                            </span>
                            @endif
                        </div>
                    </div>
                </td>

                <td>{{ $taskWatchList->project?->name }}</td>
                <td>
                    @if($taskWatchList->status == '0')
                    <x-chip color="red">{{ $taskWatchList->taskStatus }}</x-chip>
                    @elseif($taskWatchList->status == '1')
                    <x-chip color="violet">{{ $taskWatchList->taskStatus }}</x-chip>
                    @elseif($taskWatchList->status == '2')
                    <x-chip color="yellow">{{  $taskWatchList->taskStatus }}</x-chip>
                    @elseif($taskWatchList->status == '3')
                    <x-chip color="blue">{{ $taskWatchList->taskStatus }}</x-chip>
                    @elseif($taskWatchList->status == '4')
                    <x-chip color="cyan">{{  $taskWatchList->taskStatus }}</x-chip>
                    @elseif($taskWatchList->status == '5')
                    <x-chip color="green">{{ $taskWatchList->taskStatus }}</x-chip>
                    @elseif($taskWatchList->status == '6')
                    <x-chip color="lime">{{  $taskWatchList->taskStatus }}</x-chip>
                    @elseif($taskWatchList->status == '7')
                    <x-chip color="orange">{{  $taskWatchList->taskStatus }}</x-chip>
                    @elseif($taskWatchList->status == '8')
                    <x-chip color="orange">{{  $taskWatchList->taskStatus }}</x-chip>
                    @elseif($taskWatchList->status == '9')
                    <x-chip color="purple">{{  $taskWatchList->taskStatus }}</x-chip>
                    @elseif($taskWatchList->status == '10')
                    <x-chip color="teal">{{  $taskWatchList->taskStatus }}</x-chip>
                    @elseif($taskWatchList->status == '11')
                    <x-chip color="green">{{  $taskWatchList->taskStatus }}</x-chip>
                    @else
                    <x-chip color="sky">{{  $taskWatchList->taskStatus }}</x-chip>
                    @endif
                </td>

                @if(!$routeFlag)
                <td>
                    @foreach($taskWatchList->collaborators as $collaborator)
                    @if($collaborator->flag == '0')
                    <div class="p-1">
                        <x-chip color="sky" title="{{$collaborator->collaborator}}">
                            <?php
                            $userName = explode(' ', $collaborator->collaborator);
                            echo ($userName[0] . '.' . substr($userName[count($userName)-1], 0, 1));
                            ?>
                        </x-chip>
                    </div>
                    @endif
                    @endforeach
                </td>
                @endif
                <td>
                    @foreach($taskWatchList->collaborators as $collaborator)
                    @if($collaborator->flag == '1')
                    <div class="p-1">
                        <x-chip color="sky" title="{{$collaborator->collaborator}}">
                            <?php
                            $userName = explode(' ', $collaborator->collaborator);
                            echo ($userName[0] . '.' . substr($userName[1], 0, 1));
                            ?>
                        </x-chip>
                    </div>
                    @endif
                    @endforeach
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<p class='text-center'>Task's WatchList not found.</p>
@endif
<div>
    <x-modal-view target="openTaskWatchListRemoveModal">
            <x-slot:header>
                Task Watch List
            </x-slot>
            <form action="{{ route('task.watchlists.destroy')}}" method="post">
                @csrf
                <div class="grid grid-rows-2 bg-gray-100 rounded px-3 pt-3 self-start mb-3">
                        <div class="">
                            <p>Are you sure you want to remove this task from your WatchList?</p>
                        </div>
                        <div>
                            <div class="grid grid-cols-1 mb-2">
                              <x-input id="watchlist_remove_task_id" name="remove_task_id" class="hidden" placeholder="TaskId"></x-input>
                            </div>
                            <div class="grid grid-cols-2 mb-2">
                                <div> 
                                    <x-button type="submit">Remove From WatchList</x-button>
                                </div>
                                <div class="text-end">
                                     <x-button class="bg-red-500 hover:none" id="cancleWatchListModal">Cancel</x-button>
                                </div>
                            </div>
                        </div>
                </div>
            </form>
    </x-modal-view>
</div>
@endsection
<script>
    document.addEventListener('DOMContentLoaded', function() {
    let modalEle = $('#openTaskWatchListModal');
    let removeWatchList = document.querySelectorAll('.remove_watchlist');
    let watchListTaskId = document.getElementById('watchlist_remove_task_id');
    let watchListModalCancelButton = document.getElementById('cancleWatchListModal');
    removeWatchList.forEach(function(element) {
        element.addEventListener('change', function(e) {
            e.preventDefault();
            let taskId = this.getAttribute('data-id'); // Get the value of data-id attribute
            watchListTaskId.value = taskId;
        });
    });

    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.toggle('hidden');
    }

    watchListModalCancelButton.addEventListener('click', function () {
        console.log('click');
        toggleModal('openTaskWatchListRemoveModal');
        removeWatchList.forEach(function(checkbox) {
             checkbox.checked = false;
        });
    });

    $(document).on('click', '[data-dismiss="modal"]', function () {
        let modal = $(this).closest('.trigger_model');
        modal.addClass('hidden'); // Assuming this class hides the modal
        removeWatchList.forEach(function(checkbox) {
             checkbox.checked = false;
        });
    });
});

</script>