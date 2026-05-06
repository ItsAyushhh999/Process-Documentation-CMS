@extends('layouts.app')

@section('content')
<x-header heading="Task Status">
    {{-- check for admin --}}
    @if(auth()->user()->is_super_admin == 1)
    <x-button class="addStatus float-right btn btn-success" data-modal="modal" data-target="#add_status">Add
        Status
    </x-button>
    @endif
    {{-- Button for Getting active or in active statuses --}}
    <x-button  class="float-right mr-3" type="submit" to="{{ route('taskStatuses.index', ['status' => $status == '1' ? '0' : '1' ]) }}">
        {{ $status == '1' ? 'Inactive' : 'Active' }}
    </x-button>
</x-header>

@if ($taskStatuses->count()>0)
<div class="py-5 bg-white dark:bg-neutral-800 border dark:border-gray-700 rounded-lg">
    <table class="w-full text-base cTable display nowrap" id="sortableTable">
        <thead>
            <th>Name</th>
            <th>Status</th>
            <th>Created By</th>
            <th>Updated By</th>
            @if(auth()->user()->is_super_admin == 1)
            <th data-orderable="false">Action</th>
            @endif
        </thead>
        <tbody>
            @foreach ( $taskStatuses as $status )
            <tr draggable="true" class="draggable">
                <td>
                    <div class="inline-flex gap-x-2 items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width=16 height=16
                    ><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#cccccc" d="M128 136c0-22.1-17.9-40-40-40L40 96C17.9 96 0 113.9 0 136l0 48c0 22.1 17.9 40 40 40H88c22.1 0 40-17.9 40-40l0-48zm0 192c0-22.1-17.9-40-40-40H40c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40H88c22.1 0 40-17.9 40-40V328zm32-192v48c0 22.1 17.9 40 40 40h48c22.1 0 40-17.9 40-40V136c0-22.1-17.9-40-40-40l-48 0c-22.1 0-40 17.9-40 40zM288 328c0-22.1-17.9-40-40-40H200c-22.1 0-40 17.9-40 40l0 48c0 22.1 17.9 40 40 40h48c22.1 0 40-17.9 40-40V328zm32-192v48c0 22.1 17.9 40 40 40h48c22.1 0 40-17.9 40-40V136c0-22.1-17.9-40-40-40l-48 0c-22.1 0-40 17.9-40 40zM448 328c0-22.1-17.9-40-40-40H360c-22.1 0-40 17.9-40 40v48c0 22.1 17.9 40 40 40h48c22.1 0 40-17.9 40-40V328z"/></svg>
                    {{ $status->name }}
                </div>
                </td>
                <td>{{ $status->status == '1' ? 'Active' : 'Inactive' }}</td>
                <td>{{ ($status->createdBy) ? $status->created_by?->name : NULL }}</td>
                <td>{{ ($status->updatedBy) ? $status->updated_by?->name : NULL}}</td>
                @if(auth()->user()->is_super_admin == 1)
                <td>
                    <x-icon-button class="editStatus" data-id="{{$status->id}}" color="blue" data-modal="modal" data-target="#edit_status" />
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<p class='text-center'>No Task Statuses.</p>
@endif

<div id="dispaly_success_message" class="hidden alert alert-success text-white"></div>
<div id="dispaly_error_message" class="hidden alert alert-danger text-white"></div>


<x-modal-view target="add_status">
    <x-slot:header>
        Add Status
        </x-slot>
        <div id="addStatus">

        </div>
</x-modal-view>

<x-modal-view target="edit_status">
    <x-slot:header>
        Edit Status
        </x-slot>
        <div id="editStatus"> </div>
</x-modal-view>

<script>
    $(document).ready(function() {
        $('.addStatus').click(function() {
            $.ajax({
                url: 'taskStatuses/create',
                type: 'GET',
                success: function(data) {
                    $('#addStatus').html(data);
                }
            })
        })
        $('.editStatus').click(function() {
            var id = $(this).data('id');
            $.ajax({
                url: '/taskStatuses/' + id + '/edit',
                type: 'GET',
                success: function(data) {
                    $('#editStatus').html(data);
                }
            })
        });
    });
</script>

{{-- script for drag and drop sort --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    var table = document.getElementById('sortableTable');
    var rows = table.querySelectorAll('tr.draggable');
    // var rows = table.getElementsByTagName('tr');
    var dragSrcElement = null; // Variable to store the dragged element

    var originalOrder = []; // Array to store the IDs in the original order

    // Store the original order of row IDs
    for (var i = 0; i < rows.length; i++) {
        var idCell = rows[i].querySelector('td:first-child'); // Get the first <td> element (assumed to contain the ID)
        // var idCell = rows[i].querySelector('td:second-child');
        if (idCell) {
            var id = idCell.textContent.trim();
            originalOrder.push(id);
        }
    }

    // Function to handle drag start event
    function handleDragStart(e) {
        dragSrcElement = this;
        // dragSrcElement = this.closest('tr');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', this.innerHTML);
        this.classList.add('dragging');
    }

    // Function to handle drag over event
    function handleDragOver(e) {
        if (e.preventDefault) {
            e.preventDefault(); // Necessary to allow drop
        }
        this.classList.add('drag-over');
        e.dataTransfer.dropEffect = 'move';
        return false;
    }

    // Function to handle drag end event
    function handleDragEnd(e) {
        this.classList.remove('dragging', 'drag-over');
    }

    // Function to handle drop event
    function handleDrop(e) {
        if (e.stopPropagation) {
            e.stopPropagation(); // Stops browser redirect
        }

        if (dragSrcElement !== this) {
            // Reorder the rows visually
            this.parentNode.insertBefore(dragSrcElement, this);

            // Get IDs of all rows in the new order
            var reorderedIds = [];
            var updatedRows = table.getElementsByTagName('tr');
            for (var i = 0; i < updatedRows.length; i++) {
                var idCell = updatedRows[i].querySelector('td:first-child'); // Get the first <td> element (ID)
                if (idCell) {
                    var id = idCell.textContent.trim();
                    reorderedIds.push(id);
                }
            }

            // check if order is changed
            if (arraysEqual(originalOrder, reorderedIds)) {
                this.classList.remove('drag-over');
                return false;
             }

            // Send AJAX request to update the backend with the new order
            var url = '/taskStatuses/sortRowOrder';
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order: reorderedIds })
            })
            .then(response => {
                if (response.ok) {
                    let successMessageElement = document.getElementById('display_success_message');
                    successMessageElement.classList.remove('hidden');
                    successMessageElement.innerHTML = "Row order updated successfully.";
                    setTimeout(function() {
                        successMessageElement.classList.add('hidden');
                    }, 3000);

                } else {
                    let errorMessageElement = document.getElementById('display_error_message');
                    errorMessageElement.classList.remove('hidden');
                    errorMessageElement.classList.add('text-white', 'bg-red-500');
                    errorMessageElement.innerHTML = 'Failed to update row order.';
                    setTimeout(function() {
                        errorMessageElement.classList.add('hidden');
                    }, 3000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        this.classList.remove('drag-over');
        return false;
    }

    // Loop through all rows and add necessary event listeners
    for (var i = 0; i < rows.length; i++) {
        var row = rows[i];

        // Add event listeners for drag and drop events
        row.draggable = true; // Make the row draggable
        row.addEventListener('dragstart', handleDragStart, false);
        row.addEventListener('dragover', handleDragOver, false);
        row.addEventListener('dragend', handleDragEnd, false);
        row.addEventListener('drop', handleDrop, false);
    }
});

function arraysEqual(arr1, arr2) {
    if (arr1.length !== arr2.length) return false;
    for (var i = 0; i < arr1.length; i++) {
        if (arr1[i] !== arr2[i]) return false;
    }
    return true;
}
</script>

<style>
    /* Style for dragging */
    .dragging {
        opacity: 0.6; /* Adjust opacity of the dragging item */
        background-color: #f8f9fa; /* Background color of the dragging item */
    }

    /* Style for drag over */
    .drag-over {
        background-color: #f1f1f1; /* Background color when dragging over */
    }
</style>

@endsection
