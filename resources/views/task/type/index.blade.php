@extends('layouts.app')

@section('content')
<x-header heading="Tasks Types">
    {{-- check for admin --}}
    @if(auth()->user()->is_super_admin == 1)
    <!-- <x-button to="{{ route('taskTypes.create') }}" class="float-right btn btn-success">Add Task Types</x-button> -->
    <x-button class="float-right addTaskType btn btn-success" data-modal="modal" data-target="#add_task_type">Add Task
        Types</x-button>
    @endif
</x-header>

@if ($task_types->count()>0)
<div class="py-5 bg-white border rounded-lg dark:bg-neutral-800 dark:border-gray-700">
    <table class="w-full text-base cTable display nowrap">
        <thead>
            <th>S.N</th>
            <th>Type</th>
            <th>Created By</th>
            <th>Updated By</th>
            @if(auth()->user()->is_super_admin == 1)
            <th data-orderable="false">Action</th>
            @endif
        </thead>
        <tbody>
            @foreach ( $task_types as $task_type )
            <tr>
                <td>{{ $task_type->id }}</td>
                <td>{{ $task_type->type }}</td>
                <td>{{ ($task_type->createdBy) ? $task_type->createdBy->name : NULL }}</td>
                <td>{{ ($task_type->updatedBy) ? $task_type->updatedBy->name : NULL }}</td>
                @if(auth()->user()->is_super_admin == 1)
                <!-- <td>
                    <x-icon-button to="{{ route('taskTypes.edit', $task_type->id) }}" color="blue" />
                </td> -->
                <td>
                    <x-icon-button class="editTaskType" data-modal="modal" data-target="#edit_task_type" data-id="{{$task_type->id}}" color="blue" />
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>

    <x-modal-view target="edit_task_type">
        <x-slot:header>
            Edit Task Type
            </x-slot>
            <div id="editTaskType">

            </div>
    </x-modal-view>

</div>
@else
<p class='text-center'>No task types.</p>
@endif
<x-modal-view target="add_task_type">
    <x-slot:header>
        Add Task Type
        </x-slot>
        <div id="addTaskType">

        </div>
</x-modal-view>


<script>
    $(document).ready(function() {
        $('.editTaskType').click(function() {
            console.log("sucess")
            var id = $(this).data('id');
            $.ajax({
                url: '/taskTypes/' + id + '/edit',
                type: 'GET',
                success: function(data) {
                console.log(data)
                    $('#editTaskType').html(data);
                }
            })
        });
        $('.addTaskType').click(function() {
            $.ajax({
                url: 'taskTypes/create',
                type: 'GET',
                success: function(data) {
                    $('#addTaskType').html(data);
                }
            })
        });
    })
</script>
@endsection