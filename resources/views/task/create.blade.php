@extends('layouts.app')

@section('content')
<div class="p-5 border border-primary rounded-lg w-1/2 dark:border-gray-700 bg-white dark:bg-neutral-800">
    <div class="mb-4">
        <h3 class="text-2xl font-bold mb-1">Create New Task</h3>
        <span class="text-gray-500 dark:text-gray-400">Please make sure that all the required fields are filled out.</span>
    </div>
    <form action=" {{ isset($task) ? route('tasks.update',$task->id) : route('tasks.store') }}" method="post" id="taskForm" enctype="multipart/form-data">
        @csrf
        @if (isset($task))
        @method('PUT')
        @endif
        {{-- Project section --}}
        <div class="grid mb-4">
            <x-label for="project_id" required>Project:</x-label>
            <x-select name="project_id" id="project_id"  class="w-full mb-4 border-gray-300 rounded-md shadow-sm form-select focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 " required>
                @if (isset($task))
                    <option value="{{ $task->project->id }}" selected>{{ $task->project->name }}</option>
                @else
                    <option value="" disabled selected>Select--</option>
                @endif

                @foreach($projects as $project)
                    @if ($project->sub_projects == '0')
                        <option value="{{ $project->id }}"> {{ $project->name }}</option>
                        {{-- <optgroup label="{{ $project->name }}"> --}}
                        @foreach ($project->subprojects as $subproject)
                            <option value="{{ $subproject->id }}" style="background: red">
                                 <span>&nbsp;&nbsp;{{ $subproject->name }}</span>
                            </option>
                        @endforeach
                        {{-- </optgroup> --}}
                    @endif
                @endforeach
            </x-select>
        </div>


<div class="grid mb-4" id="subproject_form" style="display: none;">
    <label for="subproject_id" required>Subproject:</label>
    <x-select id="subproject_id" name="subproject_id" >
        <option value="" disabled selected>Select--</option>

    </x-select>
</div>

        <div class="grid mb-4">
            <x-label for="title" required>Title:</x-label>
            <x-input type="text" id="title" name="title" :value="isset($task) ? $task->title : ''" required />
        </div>

        <div class="grid mb-4">
            <x-label for="description" required>Description:</x-label>
            <!-- <input type="hidden" name="description" />
            <x-label for="description" required>Description:</x-label> -->
           <!-- <div id="editor-container" class="h-[200px] dark:text-gray-200 rounded-b text-base" cols="30" rows="10"></div> -->
            <!-- <div class="element" id="element"></div> -->
            <x-editor name='description' :value="isset($task) ? $task->description : ''">
            </x-editor>
        </div>
        <div class="grid mb-10">
            <x-label for="task_type" required>Task type:</x-label>
            <div>
                <select class="w-full mb-4 border-gray-300 rounded-md shadow-sm form-select focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 multiselect" name="task_type[]" multiple="multiple" required>
                    @foreach($task_types as $type)
                    <option value="{{$type->id}}">{{$type->type}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="pb-3">
            <span class="text-gray-500 dark:text-gray-400">Assign suitable member(s) to the task and also choose reviewer(s) for it.</span>
        </div>
        <div class="grid mb-4 grid-cols-2 gap-4">
            <div class="col-span-1">
                <x-label for="Assignee">Assignee:</x-label>
                <div class="mt-1">
                    <select class="w-full border-gray-300 rounded-md shadow-sm form-select focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 multiselect" name="assignees[]" multiple="multiple">
                        @foreach($users as $assignee)
                        <option value="{{$assignee->id}}">{{$assignee->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-span-1">
                <x-label for="Reviewer" required>Reviewer:</x-label>
                <div class="mt-1">
                    <select class="w-full border-gray-300 rounded-md shadow-sm form-select focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 multiselect" name="reviewer[]" multiple="multiple" required>
                        @foreach($users as $reviewer)
                        <option value="{{$reviewer->id}}">{{$reviewer->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="grid mb-4 grid-cols-2 gap-4">
            <div class="grid col-span-1">
                <x-label for="Deadline" required>Deadline:</x-label>
                <x-input id="dateTimePicker" class="datepicker" type="text" name="deadline" autocomplete="off"></x-input>
            </div>
            <div class="grid col-span-1">
                <x-label for="priority" required>Priority:</x-label>
                <x-select name="priority" required>
                    <option value="0">Normal</option>
                    <option value="1">High</option>
                    <option value="2">Urgent</option>
                </x-select>
            </div>
        </div>

        <div class="grid gap-2 items-center mb-10">
            <x-label for="attachments">Attachments:</x-label>
            <div class="block">
                <x-input type="file" id="attachments" name="attachments[]" accept="application/pdf,image/jpeg,image/png,.csv" multiple />
            </div>
        </div>
        <x-button type="submit">
            {{ isset($task) ? 'Update Task' : 'Add Task'  }}
        </x-button>


        <!-- <div class="grid mb-4">
    </div> -->
    </form>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>



    <script type="text/javascript">
        $(document).ready(function() {
            var max_fields = 10; //maximum input boxes allowed
            var wrapper = $(".input_fields_wrap"); //Fields wrapper
            var add_button = $(".add_field_button"); //Add button ID

            $(wrapper).on("click", ".remove_field", function(e) { //user click on remove text
                e.preventDefault();
                $(this).parents('tr').remove();
            })
            $('.multiselect').select2();

        });

    </script>
    <!-- <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script> -->
    <script>
        // var quill = new Quill('#editor-container', {
        //     modules: {
        //         toolbar: [
        //             ['bold', 'italic'],
        //             ['link', 'blockquote', 'code-block', 'image'],
        //             [{
        //                 list: 'ordered'
        //             }, {
        //                 list: 'bullet'
        //             }]
        //         ]
        //     },
        //     placeholder: 'Compose an epic...',
        //     theme: 'snow'
        // });
        // const form = document.querySelector("#taskForm");
        // form.onsubmit = function(e) {
        //     e.prevertDefault()
        //     const input = document.querySelector("input[name=description]");

        //     if ((quill.root.innerText).replaceAll(/\s/g, '')) { //checks if empty description
        //         input.value = quill.root.innerHTML;
        //     } else
        //         input.value = null;
        // }
    </script>
    <style>
    .myClass { background:#000; color:#fff}

    </style>
    @endsection
