@extends('layouts.app')

@section('content')

<div class="flex justify-end mb-5">
    {{-- check for admin --}}
    @if(auth()->user()->is_super_admin == 1)
    <x-button to="{{ route('tasks.create') }}" class="float-right mb-2 btn btn-success">Add tasks</x-button>
    @endif
    <x-button to="{{ route('tasks.create') }}" class="float-right mb-2 btn btn-success">All Tasks</x-button>

</div>

@if ($tasks->count()>0)
<table class="w-full text-base table-auto">
    <thead>
        <th class="p-4 pt-0 pb-3 pl-8 font-medium text-left border-b dark:border-slate-600 text-slate-400 dark:text-slate-200">Task #</th>
        <th class="p-4 pt-0 pb-3 pl-8 font-medium text-left border-b dark:border-slate-600 text-slate-400 dark:text-slate-200">Title</th>
        <th class="p-4 pt-0 pb-3 pl-8 font-medium text-left border-b dark:border-slate-600 text-slate-400 dark:text-slate-200">Project</th>
        <th class="p-4 pt-0 pb-3 pl-8 font-medium text-left border-b dark:border-slate-600 text-slate-400 dark:text-slate-200">Status</th>
        <th class="p-4 pt-0 pb-3 pl-8 font-medium text-left border-b dark:border-slate-600 text-slate-400 dark:text-slate-200">Priority</th>
        <th class="p-4 pt-0 pb-3 pl-8 font-medium text-left border-b dark:border-slate-600 text-slate-400 dark:text-slate-200">Action</th>
    </thead>
    <tbody>
        @foreach ( $tasks as $task )
        <tr>
            <td class="p-4 pl-8 border-b border-slate-100 dark:border-slate-700 text-slate-500 dark:text-slate-400"><a href="{{ route('tasks.edit', $task->id) }}">{{ $task->id }}</a></td>
            <td class="p-4 pl-8 border-b border-slate-100 dark:border-slate-700 text-slate-500 dark:text-slate-400">{{ $task->title }}</td>
            <td class="p-4 pl-8 border-b border-slate-100 dark:border-slate-700 text-slate-500 dark:text-slate-400">{{ $task->project->name }}</td>
            <td class="p-4 pl-8 border-b border-slate-100 dark:border-slate-700 text-slate-500 dark:text-slate-400">
                @if($task->status == '0')
                <span>
                    Assigned
                </span>
                @elseif($task->status == '1')
                <span>
                    In Progress
                </span>
                @elseif($task->status == '2')
                <span>
                    Assigned for Review
                </span>
                @elseif($task->status == '3')
                <span>
                    Reviewing
                </span>
                @elseif($task->status == '4')
                <span>
                    Reviewed
                </span>
                @elseif($task->status == '5')
                <span>
                    Completed
                </span>
                @endif
            </td>
            <td class="p-4 pl-8 border-b border-slate-100 dark:border-slate-700 text-slate-500 dark:text-slate-400">
            @if($task->priority == '0')
                <span class="px-3 py-1 text-white bg-blue-500 rounded-full text-base">
                    Normal
                </span>
                @elseif($task->priority == '1')
                <span class="px-3 py-1 text-white bg-yellow-400 rounded-full text-base">
                    High
                </span>
                @else
                <span class="px-3 py-1 text-white bg-red-600 rounded-full text-base">
                    Urgent
                </span>
                @endif
            </td>
            <td class="p-4 pl-8 border-b border-slate-100 dark:border-slate-700 text-slate-500 dark:text-slate-400">
                <a href="{{ route('tasks.edit', $task->id) }}" class="mb-2 btn btn-success btn-sm">
                    <x-button>Details</x-button>
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p class='text-center'>No tasks.</p>
@endif

@endsection