@extends('layouts.app')

@section('content')
<x-header heading="Titles">
    {{-- check for admin --}}
    @if(auth()->user()->is_super_admin == 1)
    <x-button class="addTitle float-right btn btn-success" data-modal="modal" data-target="#add_title">Add
        Title</x-button>
    <!-- <app-button data-modal="modal" data-target="#add_title">Add Title Add</app-button> -->
    @endif
</x-header>

@if ($titles->count()>0)
<div class="py-5 bg-white dark:bg-neutral-800 border dark:border-gray-700 rounded-lg">
    <table class="w-full text-base cTable display nowrap">
        <thead>
            <th>S.N</th>
            <th>Title Name</th>
            <th>Created By</th>
            <th>Updated By</th>
            @if(auth()->user()->is_super_admin == 1)
            <th data-orderable="false">Action</th>
            @endif
        </thead>
        <tbody>
            @foreach ( $titles as $title )
            <tr>
                <td>{{ $title->id }}</td>
                <td>{{ $title->title_name }}</td>
                <td>{{ ($title->createdBy) ? $title->createdBy->name : NULL }}</td>
                <td>{{ ($title->updatedBy) ? $title->updatedBy->name : NULL }}</td>
                @if(auth()->user()->is_super_admin == 1)
                <td>
                    <x-icon-button class="editTitle" data-id="{{$title->id}}" color="blue" data-modal="modal" data-target="#edit_title" />
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<p class='text-center'>No titles.</p>
@endif


<x-modal-view target="add_title">
    <x-slot:header>
        Add Title
        </x-slot>
        <div id="addTitle"> </div>
</x-modal-view>

<x-modal-view target="edit_title">
    <x-slot:header>
        Edit Title
        </x-slot>
        <div id="editTitle"> </div>
</x-modal-view>


<script>
    $(document).ready(function() {
        $('.addTitle').click(function() {
            $.ajax({
                url: 'titles/create',
                type: 'GET',
                success: function(data) {
                    $('#addTitle').html(data);
                }
            })
        })
        $('.editTitle').click(function() {
            var id = $(this).data('id');
            $.ajax({
                url: '/titles/' + id + '/edit',
                type: 'GET',
                success: function(data) {
                    $('#editTitle').html(data);
                }
            })
        });
    });
</script>

@endsection