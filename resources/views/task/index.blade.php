@extends('layouts.app')

@section('content')
<x-header heading="Task">
    {{-- check for admin --}}
    <div class="justify-end flex">
        <div class="rounded mr-3 shadow blackbox">
            <form action="{{ route('tasks.index') }}" method="post">
                @csrf
                @method('get')
                <x-input id="dateTimePicker" datepicker class="datepicker" type="text" name="star_from" class="h-10 mr-3" placeholder="Select Start Date" autocomplete="off" ></x-input>
                <x-input id="datepicker2" datepicker2 class="datepicker2" type="text" name="end_to" class="h-10 mr-3" placeholder="Select End Date" autocomplete="off"></x-input>
                <x-button type="submit" class="px-4 py-2">Filter</x-button>
            </form>
        </div>

        <x-button to="{{ route('tasks.create') }}" class="float-right btn btn-success mr-3">Add Task</x-button>

        <x-button outline to="{{ route('tasks.taskList') }}" class="float-right btn btn-success mr-3">Tasks Past Deadline</x-button>
        @if($routeFlag)
        <x-button outline to="{{route('tasks.index')}}" class="float-right btn btn-success">
            Assigned Tasks
        </x-button>
        @elseif(auth()->user()->is_super_admin == 1)
        <x-button outline to="{{route('task.draft')}}" class="float-right btn btn-success">
            Draft Tasks
        </x-button>
        @endif
    </div>
</x-header>

@if ($tasks->count()>0)
<div class="py-5 bg-white dark:bg-neutral-800 border dark:border-gray-700 rounded-lg">
    <table class="w-full text-base cTable display nowrap ">
        <thead>
            <th class="min-w-[90px]">Task Number</th>
            <th class="min-w-[300px]">Title</th>
            <th class="min-w-[150px]">Project</th>
            <th class="min-w-[200px]">Status</th>
            <th class="min-w-[120px]">Type</th>
            <!-- <th>Priority</th> -->
            @if(!$routeFlag)<th class="min-w-[120px]">Assignee(s)</th>@endif
            <th>Reviewer(s)</th>
            @if(!$routeFlag)<th class="min-w-[120px]">Completed In</th>@endif
            <!-- @if(!$routeFlag)<th class="min-w-[120px]">Assigned At/By</th>@endif -->
            @if(!$routeFlag)<th class="min-w-[120px]">Completed At/By</th>@endif
            <!-- <th class="min-w-[120px]">Created At/By</th> -->
        </thead>
        <tbody>
            @foreach ( $tasks as $task )
            <tr>
                <td><a href="{{ route('tasks.edit', $task->id) }}" class="text-darkblue font-semibold">{{ $task->id }}</a></td>
                <td>
                    <div class="flex flex-col  gap-4">
                        <span class="font-semibold">
                            {{ $task->title }}
                        </span>
                        <div class="flex items-center gap-x-1">
                            @if($task->priority == 2)
                            <x-chip color="red">Urgent</x-chip>
                            @elseif($task->priority == 1)
                            <x-chip color="yellow">High</x-chip>
                            @else
                            <x-chip color="green">Normal</x-chip>
                            @endif
                            @if($task->createdBy)
                            <x-chip color="sky">
                                <?php
                                $userName = explode(' ', $task->createdBy);
                                echo ($userName[0] . '.' . substr($userName[1], 0, 1));
                                ?>
                            </x-chip>
                            <span class="ml-5 whitespace-nowrap text-gray-500 dark:text-gray-400">
                                {{ $task->created_at->format('m/d/Y h:i A') }}
                            </span>
                            @endif
                        </div>
                    </div>
                </td>

                <td>{{ $task->project?->name }}</td>
                <td>

                    @if($task->status == '0')
                    <x-chip color="red">{{ $task->taskStatus?->name }}</x-chip>
                    @elseif($task->status == '1')
                    <x-chip color="violet">{{ $task->taskStatus?->name }}</x-chip>
                    @elseif($task->status == '2')
                    <x-chip color="yellow">{{  $task->taskStatus?->name }}</x-chip>
                    @elseif($task->status == '3')
                    <x-chip color="blue">{{ $task->taskStatus?->name }}</x-chip>
                    @elseif($task->status == '4')
                    <x-chip color="cyan">{{  $task->taskStatus?->name }}</x-chip>
                    @elseif($task->status == '5')
                    <x-chip color="green">{{ $task->taskStatus?->name }}</x-chip>
                    @elseif($task->status == '6')
                    <x-chip color="lime">{{  $task->taskStatus?->name }}</x-chip>
                    @elseif($task->status == '7')
                    <x-chip color="orange">{{  $task->taskStatus?->name }}</x-chip>
                    @elseif($task->status == '8')
                    <x-chip color="orange">{{  $task->taskStatus?->name }}</x-chip>
                    @elseif($task->status == '9')
                    <x-chip color="purple">{{  $task->taskStatus?->name }}</x-chip>
                    @elseif($task->status == '10')
                    <x-chip color="teal">{{  $task->taskStatus?->name }}</x-chip>
                    @elseif($task->status == '11')
                    <x-chip color="green">{{  $task->taskStatus?->name }}</x-chip>
                    @else
                    <x-chip color="sky">{{  $task->taskStatus?->name }}</x-chip>
                    @endif
                </td>
                <td>{{ $task->types }}</td>
                @if(!$routeFlag)
                <td>
                    @foreach($task->collaborators as $collaborator)
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
                    @foreach($task->collaborators as $collaborator)
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

                @if(!$routeFlag)<td>
                    @if ($task->completedAt && $task->assignedAt)
                    @if(\Carbon\Carbon::parse($task->completedAt)->gt(\Carbon\Carbon::parse($task->deadline)))
                    <x-chip color="red">
                        <?php
                        $differenceInMinutes = (\Carbon\Carbon::parse($task->completedAt)
                            ->diffInMinutes(\Carbon\Carbon::parse($task->assignedAt)));
                        $differenceInMinutes =  Carbon\CarbonInterval::minutes($differenceInMinutes)->cascade()->forHumans();

                        echo ($differenceInMinutes);
                        ?>
                    </x-chip>
                    @else
                    <x-chip color="green">
                        <?php
                        $differenceInMinutes = (\Carbon\Carbon::parse($task->completedAt)
                            ->diffInMinutes(\Carbon\Carbon::parse($task->assignedAt)));
                        $differenceInMinutes =  Carbon\CarbonInterval::minutes($differenceInMinutes)->cascade()->forHumans();

                        echo ($differenceInMinutes);
                        ?>
                    </x-chip>
                    @endif
                    @endif
                </td>@endif

                @if(!$routeFlag)
                <td>
                    <span class="whitespace-nowrap">
                        {{isset($task->completedAt) ? \Carbon\Carbon::parse($task->completedAt)->format('m/d/Y H:i A') : null}}
                    </span>
                    @if(isset($task->completedBy))
                    <div class="py-1 mt-2">
                        <!-- {{$task->completedBy}} -->
                        <x-chip color="sky" title="{{$task->completedBy}}">
                            <?php
                            $userName = explode(' ', $task->completedBy);
                            echo ($userName[0] . '.' . substr($userName[1], 0, 1));
                            ?>
                        </x-chip>
                    </div>
                    @endif
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<p class='text-center'>No tasks.</p>
@endif

@endsection
