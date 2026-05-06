@extends('layouts.app')

@section('content')
<x-header heading="Departments">
    {{-- check for admin --}}
    @if(auth()->user()->is_super_admin == 1)
    <x-button class="addDepartment float-right btn btn-success" data-modal="modal" data-target="#add_department">Add
        Department</x-button>
    @endif
</x-header>

@if ($departments->count()>0)
<div class="py-5 bg-white dark:bg-neutral-800 border dark:border-gray-700 rounded-lg">
    <table class="w-full text-base cTable display nowrap">
        <thead>
            <th>S.N</th>
            <th>Department Name</th>
            <th>Created By</th>
            <th>Updated By</th>
            @if(auth()->user()->is_super_admin == 1)
            <th data-orderable="false">Action</th>
            @endif
        </thead>
        <tbody>
            @foreach ( $departments as $department )
            <tr>
                <td>{{ $department->id }}</td>
                <td>{{ $department->department_name }}</td>
                <td>{{ ($department->createdBy) ? $department->createdBy->name : NULL }}</td>
                <td>{{ ($department->updatedBy) ? $department->updatedBy->name : NULL}}</td>
                @if(auth()->user()->is_super_admin == 1)
                <td>
                    <x-icon-button class="editDepartment" data-id="{{$department->id}}" color="blue" data-modal="modal" data-target="#edit_department" />
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<p class='text-center'>No departments.</p>
@endif


<x-modal-view target="add_department">
    <x-slot:header>
        Add Department
        </x-slot>
        <div id="addDepartment"> </div>
</x-modal-view>

<x-modal-view target="edit_department">
    <x-slot:header>
        Edit Department
        </x-slot>
        <div id="editDepartment"> </div>
</x-modal-view>

<script>
    $(document).ready(function() {
        $('.addDepartment').click(function() {
            $.ajax({
                url: 'departments/create',
                type: 'GET',
                success: function(data) {
                    $('#addDepartment').html(data);
                }
            })
        })
        $('.editDepartment').click(function() {
            var id = $(this).data('id');
            $.ajax({
                url: '/departments/' + id + '/edit',
                type: 'GET',
                success: function(data) {
                    $('#editDepartment').html(data);
                }
            })
        });
    });
</script>

@endsection