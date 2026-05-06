@extends('layouts.app')

@section('content')
<x-header heading="Task">
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
            <th class="min-w-[120px]">Assignee(s)</th>
            <th class="min-w-[120px]">Reviewer(s)</th>
            <th class="min-w-[120px]">Deadline</th>
        </thead>
        <tbody>
            @foreach ( $tasks as $task )
            <tr>
                <td><a href="{{ route('tasks.edit', $task->id) }}" class="text-darkblue font-semibold">{{ $task->id
                        }}</a></td>

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

                <td>{{ $task->project->name }}</td>

                <td>
                    @if($task->status == '0')
                    <x-chip color="red">Assigned</x-chip>
                    @elseif($task->status == '1')
                    <x-chip color="violet">In Progress</x-chip>
                    @elseif($task->status == '2')
                    <x-chip color="yellow">Assigned for Review</x-chip>
                    @elseif($task->status == '3')
                    <x-chip color="blue">Reviewing</x-chip>
                    @elseif($task->status == '4')
                    <x-chip color="cyan">Reviewed</x-chip>

                    @elseif($task->status == '6')
                    <x-chip color="lime">closed</x-chip>
                    @elseif($task->status == '7')
                    <x-chip color="orange">Created</x-chip>
                    @elseif($task->status == '8')
                    <x-chip color="orange">Staging - Ready to Upload</x-chip>
                    @elseif($task->status == '9')
                    <x-chip color="purple">Staging - Uploaded</x-chip>
                    @elseif($task->status == '10')
                    <x-chip color="teal"> Live - Ready to Upload</x-chip>
                    @elseif($task->status == '11')
                    <x-chip color="green"> Live - Upload</x-chip>
                    @endif
                </td>

                <td>{{ $task->types }}</td>

                <td>
                    @foreach($task->collaborators as $collaborator)
                    @if($collaborator->flag == '0')
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

                <td>
                    <span class="whitespace-nowrap">
                        @php
                        echo($task->deadline ?
                        \Carbon\Carbon::parse($task->deadline)->format('m/d/Y h:i A') : null);
                        @endphp
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<p class='text-center'>No tasks.</p>
@endif

@endsection