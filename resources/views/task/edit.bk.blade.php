@extends('layouts.app')

@section('content')
<div class="grid lg:grid-cols-2 grid-cols-1 gap-4">
    <div class="col-span-1 ">
        <div class="p-5 border border-primary dark:border-gray-700 rounded-lg bg-white dark:bg-neutral-800">
            <h3 class="text-2xl font-bold mb-5">Task Details page</h3>
            @if (!$isSuperAdmin)
            <div>
                <div class="mb-3 grid grid-cols-4 gap-x-4">
                    <x-label for="project_id" class="col-span-1">Project Name:</x-label>
                    <span class="dark:text-gray-300 col-span-3">
                    {{ $task->project?->parentProject->name ?? '' }}

                    @if ($task->project?->parentProject)
                     /
                    @endif
                    <strong>{{ $task->projectName }}</strong>
                    </span>
                </div>


                <div class="mb-3 grid grid-cols-4 gap-x-4">
                    <x-label for="project_id" class="col-span-1">Title:</x-label>
                    <span class="dark:text-gray-300 col-span-3">{{ $task->title }}</span>
                </div>
                <div class="mb-3 grid grid-cols-4 gap-x-4">
                    <x-label for="status" class="col-span-1">Status:</x-label>
                    <span class="col-span-3">
                        @if ($task->status == '0')
                        <span class="px-3 py-1 text-red-900 bg-red-200 rounded-full text-sm font-semibold">
                            Assigned
                        </span>
                        @elseif($task->status == '1')
                        <span
                            class="px-3 py-1 text-violet-700 bg-violet-200 rounded-full text-base font-semibold whitespace-nowrap">
                            In Progress
                        </span>
                        @elseif($task->status == '2')
                        <span
                            class="px-3 py-1 text-yellow-800 bg-yellow-100 rounded-full text-base font-semibold whitespace-nowrap">
                            Assigned for Review
                        </span>
                        @elseif($task->status == '3')
                        <span class="px-3 py-1 text-blue-900 bg-blue-200 rounded-full text-base font-semibold">
                            Reviewing
                        </span>
                        @elseif($task->status == '4')
                        <span class="px-3 py-1 text-cyan-200 bg-cyan-800 rounded-full text-base font-semibold">
                            Reviewed
                        </span>
                        @elseif($task->status == '5')
                        <span class="px-3 py-1 text-green-200 bg-green-800 rounded-full text-base font-semibold">
                            Completed
                        </span>
                        @elseif($task->status == '6')
                        <span class="px-3 py-1 text-slate-200 bg-slate-800 rounded-full text-base font-semibold">
                            Closed
                        </span>
                        @elseif($task->status == '7')
                        <span class="px-3 py-1 text-gray-900 bg-gray-200 rounded-full text-base font-semibold">
                            Created
                        </span>
                        @elseif($task->status == '8')
                        <span
                            class="px-3 py-1 text-purple-900 bg-purple-100 rounded-full text-base font-semibold whitespace-nowrap">
                            Staging - Ready to Upload
                        </span>
                        @elseif($task->status == '9')
                        <span
                            class="px-3 py-1 text-purple-900 bg-purple-300 rounded-full text-base font-semibold whitespace-nowrap">
                            Staging - Uploaded
                        </span>
                        @elseif($task->status == '10')
                        <span
                            class="px-3 py-1 text-teal-900 bg-teal-200 rounded-full text-base font-semibold whitespace-nowrap">
                            Live - Ready to Upload
                        </span>
                        @elseif($task->status == '11')
                        <span
                            class="px-3 py-1 text-teal-900 bg-teal-400 rounded-full text-base font-semibold whitespace-nowrap">
                            Live - Uploaded
                        </span>
                        @endif
                    </span>
                </div>
                <div class="mb-3 grid grid-cols-4 gap-x-4">
                    <x-label for="priority" class="col-span-1">Priority:</x-label>
                    <span class="col-span-3">
                        @if ($task->priority == '0')
                        <span class="px-3 py-1 text-blue-900 bg-blue-200 rounded-full text-base font-semibold">
                            Normal
                        </span>
                        @elseif($task->priority == '1')
                        <span class="px-3 py-1 text-yellow-900 bg-yellow-200 rounded-full text-base font-semibold">
                            High
                        </span>
                        @else
                        <span class="px-3 py-1 text-red-900 bg-red-200 rounded-full text-base font-semibold">
                            Urgent
                        </span>
                        @endif
                    </span>
                </div>
                <div class="mb-3 grid grid-cols-4 gap-x-4">
                    <x-label for="project_id" class="col-span-1">Description:</x-label>
                    <span class="dark:text-gray-300 col-span-3 border dark:border-gray-700 rounded-lg p-2">
                        <!-- {!! $task->description !!} -->
                        <?php
                            // regular expression to open the links in comments
                            $re = '#(?<!"|">)(https?:\/\/.*?)(?=\s|[\s]?<)#m';
                            $subst = '<a href="$1" target="_blank" class="text-blue-500">$1</a>';
                            $result = preg_replace($re, $subst, $task->description);
                            ?>
                        <div class="description whitespace-normal">
                            {!! $result !!}
                        </div>
                    </span>
                </div>
                <div class="mb-3 grid grid-cols-4 gap-x-4">
                    <x-label for="project_id" class="col-span-1">Assignees:</x-label>
                    <div class="col-span-3">
                        @foreach ($assignees as $assignee)
                        <span class="dark:text-gray-300">{{ $assignee->collaboratorName }}</span><br />
                        @endforeach
                    </div>
                </div>
                <div class="mb-3 grid grid-cols-4 gap-x-4">
                    <x-label for="project_id" class="col-span-1">Reviewers:</x-label>
                    <div class="col-span-3">
                        @foreach ($reviewers as $reviewer)
                        <span class="dark:text-gray-300">{{ $reviewer->collaboratorName }}</span><br />
                        @endforeach
                    </div>
                </div>
                <div class="mb-3 grid grid-cols-4 gap-x-4">
                    <x-label for="project_id" class="col-span-1">Deadline:</x-label>
                    <span class="dark:text-gray-300 col-span-3">{{ isset($task->deadline) ?
                        \Carbon\Carbon::parse($task->deadline)->format('m/d/Y h:i A') : null }}</span>
                </div>
                <div class="mb-3 grid grid-cols-4 gap-x-4">
                    <x-label for="project_id" class="col-span-1">Created At/By:</x-label>
                    <span class="dark:text-gray-300 col-span-3">
                        <span class="mr-2">{{ isset($task->created_at) ?
                            \Carbon\Carbon::parse($task->created_at)->format('m/d/Y h:i A') : null }}</span>
                        <span
                            class="px-3 py-1 rounded-full text-base whitespace-nowrap bg-sky-200 text-sky-900 font-semibold"
                            title="{{ $task->createdBy }}">
                            <?php
                                $userName = explode(' ', $task->createdBy);
                                echo $userName[0] . '.' . substr($userName[1], 0, 1);
                                ?>
                        </span>
                    </span>
                </div>
                <div class="mb-3 grid grid-cols-4 gap-x-4">
                    <x-label for="project_id" class="col-span-1">Last Updated At:</x-label>
                    <span class="dark:text-gray-300 col-span-3">
                        <span class="mr-2">
                            {{ isset($task->updated_at) ? \Carbon\Carbon::parse($task->updated_at)->format('m/d/Y h:i
                            A') : null }}
                        </span>
                        <span
                            class="px-3 py-1 rounded-full text-base whitespace-nowrap bg-sky-200 text-sky-900 font-semibold"
                            title="{{ $task->updatedBy }}">
                            <?php
                                $userName = explode(' ', $task->updatedBy);
                                echo $userName[0] . '.' . substr($userName[1], 0, 1);
                                ?>
                        </span>
                    </span>
                </div>
                @if (count($attachments))
                <div class="mb-3 grid grid-cols-4 gap-x-4">
                    <x-label for="project_id" class="col-span-1">Attachments:</x-label>
                    <ul class="flex flex-wrap gap-2 list-none col-span-3">
                        @foreach ($attachments as $key => $attachment)
                        <li>
                            <a href=" {{ asset('storage/tasks/' . $attachment->name) }}"
                                class="inline-flex items-center px-3 py-1 mr-2 space-x-2 text-base transition-all border rounded-full hover:bg-primary-100 text-primary-700 border-primary-700 dark:text-gray-300 dark:border-gray-600"
                                target="_blank">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    aria-hidden="true" role="img" class="w-3 h-3 iconify iconify--bi" width="32"
                                    height="32" preserveAspectRatio="xMidYMid meet" viewBox="0 0 16 16">
                                    <g fill="currentColor">
                                        <path
                                            d="M.002 3a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-12a2 2 0 0 1-2-2V3zm1 9v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V9.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71l-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12zm5-6.5a1.5 1.5 0 1 0-3 0a1.5 1.5 0 0 0 3 0z">
                                        </path>
                                    </g>
                                </svg>
                                <span class="max-w-[120px] text-ellipsis overflow-hidden whitespace-pre ">{{
                                    $attachment->name }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
            @else
            <form action=" {{ route('tasks.update', $task->id) }}" method="post" id="taskForm"
                enctype="multipart/form-data">
                @csrf
                @if (isset($task))
                @method('PUT')
                @endif

                <div class="grid mb-5" style='margin-top: 10px;'>
                    <x-label for="project_id" required>Project:</x-label>
                    <x-select name="project_id" class="w-full mb-4 border-gray-300 rounded-md shadow-sm form-select focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50" required>
                        @foreach ($projects as $project)
                            @if ($project->sub_projects == '0')
                            <optgroup label="{{ $project->name }}">
                                @foreach ($project->subprojects as $subproject)
                                    <option value="{{ $subproject->id }}" class="pl-4"
                                            {{ in_array($task->project_id, [$subproject->id]) ? 'selected' : '' }}>
                                            &nbsp; &nbsp;{{ $subproject->name }}
                                    </option>
                                @endforeach'
                            </optgroup>
                            @endif
                        @endforeach
                    </x-select>
                </div>


                <div class="grid mb-5">
                    <x-label for="title" required>Title:</x-label>
                    <x-input type="text" id="title" name="title" :value="$task->title" required />
                </div>
                <div class="grid mb-5">
                    <x-label for="description" required>Description:</x-label>
                    <x-editor name='description' :value="isset($task) ? $task->description : ''"  > </x-editor>
                    </textarea>
                </div>
                <div class="grid-5 mb-5">
                    <x-label for="task_type" required>Task Type(s):</x-label>
                    <div class="mt-1">
                        <select
                            class="w-full mb-5 border-gray-300 rounded-md shadow-sm form-select focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 multiselect"
                            name="task_type[]" multiple="multiple" required>
                            @foreach ($task_types as $type)
                            <option value="{{ $type->id }}" <?php if (in_array($type->id, $task_typeIds)) {
                                echo 'selected';
                                } ?>>{{ $type->type }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid-5 mb-5">
                    <x-label for="Assignee">Assignee(s):</x-label>
                    <div class="mt-1">
                        <select
                            class="w-full mb-5 border-gray-300 rounded-md shadow-sm form-select focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 multiselect"
                            name="assignees[]" multiple="multiple">
                            @foreach ($users as $assignee)
                            <option value="{{ $assignee->id }}" <?php if (in_array($assignee->id, $assigneesIds)) {
                                echo 'selected';
                                } ?>>{{ $assignee->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid-5 mb-5">
                    <x-label for="Reviewer" required>Reviewer(s):</x-label>
                    <div class="mt-1">
                        <select
                            class="w-full mb-5 border-gray-300 rounded-md shadow-sm form-select focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 multiselect"
                            name="reviewers[]" multiple="multiple" required>
                            @foreach ($users as $reviewer)
                            <option value="{{ $reviewer->id }}" <?php if (in_array($reviewer->id, $reviewersIds)) {
                                echo 'selected';
                                } ?>>{{ $reviewer->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-5">
                    <div class="grid col-span-1">
                        <x-label for="Deadline">Deadline:</x-label>
                        <x-input id="dateTimePicker" type="text" name="deadline"
                            value="{{ isset($task->deadline) ? \Carbon\Carbon::parse($task->deadline)->format('m/d/Y h:i A') : null }}"
                            autocomplete="off" />
                    </div>
                    <div class="grid col-span-1">
                        <x-label for="priority" required>Priority:</x-label>
                        <x-select name="priority" required>
                            <option value="0" <?php if ($task->priority == '0') {
                                echo 'selected';
                                } ?>>Normal</option>
                            <option value="1" <?php if ($task->priority == '1') {
                                echo 'selected';
                                } ?>>High</option>
                            <option value="2" <?php if ($task->priority == '2') {
                                echo 'selected';
                                } ?>>Urgent</option>
                        </x-select>
                    </div>
                </div>

                @if (count($attachments))
                <div class="grid mb-5">
                    <x-label>Attachments:</x-label>
                    <ul class="flex flex-wrap gap-2 list-none">
                        @foreach ($attachments as $key => $attachment)
                        <li>
                            <a href=" {{ asset('storage/tasks/' . $attachment->name) }}"
                                class="inline-flex items-center px-3 py-1 space-x-2 text-base transition-all border rounded-full hover:bg-primary-100 text-primary-700 border-primary-700"
                                target="_blank">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    aria-hidden="true" role="img" class="dark:text-gray-300 w-3 h-3 iconify iconify--bi"
                                    preserveAspectRatio="xMidYMid meet" viewBox="0 0 16 16">
                                    <g fill="currentColor">
                                        <path
                                            d="M.002 3a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-12a2 2 0 0 1-2-2V3zm1 9v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V9.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71l-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12zm5-6.5a1.5 1.5 0 1 0-3 0a1.5 1.5 0 0 0 3 0z">
                                        </path>
                                    </g>
                                </svg>
                                <span
                                    class="max-w-[110px] text-ellipsis overflow-hidden whitespace-pre dark:text-gray-300">{{
                                    $attachment->name }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <div class="flex justify-end">
                    <x-button type="submit">Update</x-button>
                </div>
            </form>
            @endif
        </div>
    </div>
    <div class="col-span-1 gap-4 flex flex-col">
        <div class="p-5 border dark:border-gray-600 border-primary rounded-lg bg-white dark:bg-neutral-800 ">
            <form id="commentForm" action="{{route('tasks.comments.store')}}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="taskId" value="{{$task->id}}">
                <x-label for="comment" required>Add Comment:</x-label>
                <!-- <textarea name="comment" data-quilljs placeholder="Please enter text"></textarea> -->
                <x-editor name='comment' id='comment' height='180px'> </x-editor>
                <div class="grid grid-cols-1 gap-4">
                    <div class="col-span-1 grid">
                        @if(($isSuperAdmin ||
                        (!$isSuperAdmin &&
                        (in_array($task->status, ['0','1', '2']) && (in_array(auth()->user()->id, $assigneesIds )) ||
                        (in_array($task->status, ['0','1', '2', '3', '4']) && in_array(auth()->user()->id, $reviewersIds
                        ))))) && $task->status != '6')
                        <div class="grid mb-4 mt-5">
                            <x-label for="title">Status:</x-label>
                            <x-select name="status">
                                @if($isSuperAdmin || in_array(auth()->user()->id, $reviewersIds))
                                <option value="7" disabled @if($task->status == '7') selected @endif>
                                    Created
                                </option>
                                @endif
                                <option value="0" @if(!$isSuperAdmin && !in_array(auth()->user()->id, $reviewersIds))
                                    disabled @endif @if($task->status == '0') selected @endif>
                                    Assigned
                                </option>
                                <option value="1" @if(!$isSuperAdmin && (!in_array(auth()->user()->id, $assigneesIds ))
                                    ) disabled @endif @if($task->status == '1') selected @endif>
                                    In Progress
                                </option>
                                <option value="2" @if(!$isSuperAdmin && ($task->status != '1' ||
                                    !in_array(auth()->user()->id, $assigneesIds))) disabled @endif @if($task->status ==
                                    '2') selected @endif>
                                    Assigned for Review
                                </option>
                                @if($isSuperAdmin || in_array(auth()->user()->id, $reviewersIds))
                                <option value="3" @if(!$isSuperAdmin && (!in_array(auth()->user()->id, $reviewersIds)))
                                    disabled @endif @if($task->status == '3') selected @endif>
                                    Reviewing
                                </option>
                                <option value="4" @if(!$isSuperAdmin && (!in_array(auth()->user()->id, $reviewersIds)))
                                    disabled @endif @if($task->status == '4') selected @endif>
                                    Reviewed
                                </option>
                                @endif
                                @if($isSuperAdmin)
                                <option value="8" @if($task->status == '8') selected @endif>
                                    Staging - Ready to upload
                                </option>
                                <option value="9" @if($task->status == '9') selected @endif>
                                    Staging - Uploaded
                                </option>
                                <option value="10" @if($task->status == '10') selected @endif>
                                    Live - Ready to upload
                                </option>
                                <option value="11" @if($task->status == '11') selected @endif>
                                    Live - Uploaded
                                </option>
                                <option value="5" @if($task->status == '5') selected @endif>
                                    Completed
                                </option>
                                @endif
                            </x-select>
                        </div>
                        @endif
                        <div class="grid mb-5">
                            <x-label for="attachments">Attachments:</x-label>
                            <x-input type="file" id="attachments" name="attachments[]"
                                accept="application/pdf,image/jpeg,image/png, .csv" multiple />
                        </div>
                    </div>
                </div>
                <x-button type="submit">Comment</x-button>
            </form>
        </div>
        @if ($deploy_permission_granted)
        <div class="bg-white py-3 px-3 rounded shadow">
            <x-button class="float-right deployModal btn btn-success" data-modal="modal" data-target="#openDeployModal"
                id="deployModal">Deploy</x-button>
            <x-button class="float-right deployModal btn btn-success mr-3" data-modal="modal" data-target="#openDeployLogsModal"
                data-log-id="{{ $task->project->name }}" id="deployLogsModal">Deploy Logs
            </x-button>
            <div>
                <x-modal-view target="openDeployModal">
                    <x-slot:header>
                        Deploy Status
                        </x-slot>
                        <x-button class="float-right deployModal btn btn-success mb-3 mt-3 hidden" data-modal="modal"
                            data-target="#openDeployStatusModal" id="refreshModal"
                            data-id="{{ json_encode(['project_name' => $task->project->name, 'taskId' => $task->id, 'project_branch' => $task->project->branch, 'development_pipeline' => $task->project->development_pipeline, 'sub_project' => $task->project->sub_projects, 'production_Pipeline' => $task->project->production_Pipeline, 'staging_pipeline' => $task->project->staging_pipeline]) }}">
                            Refresh</x-button>
                        <div id="openDeployModal" class="">
                            <div class="bg-white rounded py-3 px-3 w-94">
                                <select name="stage_name" class="" id="deploy_stage_id"
                                    data-id="{{ json_encode(['project_name' => $task->project->name, 'taskId' => $task->id, 'project_branch' => $task->project->branch, 'development_pipeline' => $task->project->development_pipeline, 'sub_project' => $task->project->sub_projects, 'production_Pipeline' => $task->project->production_Pipeline, 'staging_pipeline' => $task->project->staging_pipeline]) }}">
                                    <option>--------Select Branch--------</option>
                                    <option value="production">Production</option>
                                    <option value="development">Development</option>
                                    <option value="stagging">Staging</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-1" id="pipelinedetail">
                                <h2 class="mb-3 hidden" id="hide_pipeline_name">Pipeline Name: <span id="pipeline_name"></span>
                                </h2>
                                <h2 class="mb-3 hidden" id="hide_pipeline_summary">Pipeline Summary: </h2>
                            </div>
                            <table class="min-w-full leading-normal" id="pipelinetable" hidden>
                                <thead>
                                    <tr>
                                        <th
                                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            stageName
                                        </th>
                                        <th
                                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Action Name
                                        </th>
                                        <th
                                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th
                                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Summary
                                        </th>
                                        <th
                                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Updated Date
                                        </th>
                                        <th
                                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Entity Url
                                        </th>
                                        <th
                                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Revision Url
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="pipelineTableData">

                                </tbody>
                            </table>
                            <div class="change_deploy bg-white shadow rounded mt-3 mb-3 px-3 py-3" id="deploy_data" hidden>
                                <form action="{{ route('deploy') }}" method="post">
                                    @method('post')
                                    @csrf
                                    <x-input id="deployProjectId" type="text" name="deployProjectId" hidden/>
                                    <x-input id="deploy_token" type="text" name="deploy_token" hidden />
                                    <x-input id="deploy_taskId" type="text" name="deploy_taskId" hidden />
                                    <x-input id="deploy_projectName" type="text" name="deploy_projectName" hidden />
                                    <x-input id="deploy_pipeline_name" type="text" name="deploy_pipeline_name" hidden />
                                    <x-input id="deploy_stage_name" type="text" name="deploy_stage_name" hidden />
                                    <x-input id="deploy_action_name" type="text" name="deploy_action_name" hidden />
                                    <div class="mx-auto max-w-sm text-center flex flex-wrap justify-center">
                                        <div class="flex items-center mr-4 mb-4">
                                            <label for="approve" class="flex items-center cursor-pointer"> Approve</label>
                                            <x-input id="approve" type="radio" name="deploy" value="Approved" />
                                        </div>

                                        <div class="flex items-center mr-4 mb-4">
                                            <label for="reject" class="flex items-center cursor-pointer">Reject</label>
                                            <x-input id="reject" type="radio" name="deploy" value="Rejected" />
                                        </div>
                                    </div>
                                    <x-button type="submit" class="mt-4 text-end mb-3">Deploy</x-button>
                                </form>
                            </div>
                        </div>
                </x-modal-view>
            </div>
            <div>
                <x-modal-view target="openDeployLogsModal">
                    <x-slot:header>
                        Deploy Logs
                        </x-slot>
                        <div id="openDeployLogsModal" class="">
                            <table class="min-w-full leading-normal" id="deploylogsTable">
                                <thead>
                                    <tr>
                                        <th
                                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Created By
                                        </th>

                                        <th
                                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Deploy Pull Request
                                        </th>

                                        <th
                                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Deploy Summary
                                        </th>
                                        <th
                                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Task Id
                                        </th>
                                        <th
                                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Project Name
                                        </th>
                                        <th
                                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Deploy
                                        </th>
                                        <th
                                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Log Created Date
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="deploylogDetails">

                                </tbody>
                            </table>

                            <div class="change_deploy bg-white shadow rounded mt-3 mb-3 px-3 py-3" id="deploy_data">
                                <form action="{{ route('deploy') }}" method="post">
                                    @method('post')
                                    @csrf
                                    <x-input id="deploy_token" type="text" name="deploy_token" hidden />

                                    <div class="mx-auto max-w-sm text-center flex flex-wrap justify-center">
                                        <div class="flex items-center mr-4 mb-4">
                                            <label for="approve" class="flex items-center cursor-pointer">
                                                Approve</label>
                                            <x-input id="approve" type="radio" name="deploy" value="Approved" />
                                        </div>

                                        <div class="flex items-center mr-4 mb-4">
                                            <label for="reject" class="flex items-center cursor-pointer">Reject</label>
                                            <x-input id="reject" type="radio" name="deploy" value="Rejected" />
                                        </div>
                                    </div>
                                    <x-button type="submit" class="mt-4 text-end mb-3">Deploy</x-button>
                                </form>
                            </div>
                        </div>
                </x-modal-view>
            </div>
        </div>
        @endif
        @if ($activities->count())
        {{-- pinned start --}}
        @if($activities->where('isPinned')->count())
        <div class="px-5 py-1 border border-primary dark:border-gray-600 rounded-lg bg-white dark:bg-neutral-800">
            <div id="view-pinned" class="cursor-pointer flex gap-x-4 items-end mb-2 justify-between">
                <h3 class="text-xl font-bold">Pinned:</h3>
                <div
                    class="flex flex-row items-center mt-4 font-medium transition-all duration-200 ease-in-out cursor-pointer w-fit group text-darkblue dark:text-blue-300 hover:text-lightblue">
                    <svg class="h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                        <path
                            d="M246.6 470.6c-12.5 12.5-32.8 12.5-45.3 0l-160-160c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L224 402.7 361.4 265.4c12.5-12.5 32.8-12.5 45.3 0s12.5 32.8 0 45.3l-160 160zm160-352l-160 160c-12.5 12.5-32.8 12.5-45.3 0l-160-160c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L224 210.7 361.4 73.4c12.5-12.5 32.8-12.5 45.3 0s12.5 32.8 0 45.3z" />
                    </svg>
                </div>
            </div>
            <hr class="collapsePinned border-gray-300 dark:bg-gray-700">
            <div class="collapsePinned py-5 rounded overflow-y-auto max-h-[80vh] pr-1">
                <div class="flex flex-col space-y-4">
                    @foreach ($activities->where('isPinned') as $key => $comment)
                    @if (isset($comment->comments))
                    <div class="flex space-x-6">
                        <div
                            class="border border-gray-300 dark:border-gray-600 w-20 flex grow relative rounded-lg mt-4">
                            <div class="z-20 w-full h-full overflow-hidden rounded-lg">
                                <div
                                    class="flex justify-between items-center bg-gray-100 dark:bg-neutral-700 py-2 px-3">
                                    <span class="text-base dark:text-gray-300">
                                        <strong>{{ $comment->user }} </strong>
                                        <span class="text-xs text-gray-400">({{ $comment->pinnedBy }} pinned a comment)
                                        </span>
                                    </span>
                                </div>

                                <div class="px-3 py-2">
                                    <span class="dark:text-slate-300 description">
                                        <?php
                                                        // regular expression to open the links in comments
                                                        $re = '#(?<!"|">)(https?:\/\/.*?)(?=\s|[\s]?<)#m';
                                                        $subst = '<a href="$1" target="_blank" class="text-blue-500 text-sm">$1</a>';
                                                        $result = preg_replace($re, $subst, $comment->comments);
                                                        echo $result;
                                                        ?>
                                    </span>

                                    <div class="mt-1">
                                        @foreach ($comment->getCommentImage as $image)
                                        <a href="{{ asset('storage/tasks/' . $image->name) }}"
                                            class="inline-flex items-center px-3 py-1 mb-2 space-x-2 text-base transition-all border rounded-full hover:bg-primary-100 text-primary-700 border-primary-700 dark:text-slate-300"
                                            target="_blank">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img"
                                                class="w-3 h-3 iconify iconify--bi" preserveAspectRatio="xMidYMid meet"
                                                viewBox="0 0 16 16">
                                                <g fill="currentColor">
                                                    <path
                                                        d="M.002 3a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-12a2 2 0 0 1-2-2V3zm1 9v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V9.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71l-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12zm5-6.5a1.5 1.5 0 1 0-3 0a1.5 1.5 0 0 0 3 0z">
                                                    </path>
                                                </g>
                                            </svg>
                                            <span class="max-w-[110px] text-ellipsis overflow-hidden whitespace-pre">{{
                                                $image->name }}</span>
                                        </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        {{-- pinned end --}}
        <div class="p-5 border border-primary dark:border-gray-600 rounded-lg bg-white dark:bg-neutral-800">
            <div class="flex gap-x-4 items-end mb-2">
                <h3 class="text-xl font-bold">Activities:</h3>
                @if ($task->status == '6')
                <span
                    class="px-3 py-1 text-green-900 bg-green-300 rounded-full text-base font-semibold whitespace-nowrap">Task
                    Closed</span>
                @endif
            </div>
            <hr class=" border-gray-300 dark:bg-gray-700">
            <div class="py-5 rounded overflow-y-auto max-h-[80vh] pr-1">
                <div class="flex flex-col space-y-4">
                    @foreach ($activities as $key => $comment)
                    @if (isset($comment->comments))
                    <div class="flex space-x-6">
                        <div class="flex flex-col items-center">
                            <span
                                class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white">

                                <?php
                                            $userName = explode(' ', $comment->user);
                                            echo substr($userName[0], 0, 1) . substr($userName[1], 0, 1);
                                            ?>
                            </span>
                            <span class="w-[1px] block bg-gray-300 dark:bg-gray-600 grow mt-2"></span>
                        </div>
                        <div
                            class="border border-gray-300 dark:border-gray-600 w-20 flex grow relative rounded-lg mt-4">
                            <div
                                class="absolute border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-neutral-700 w-4 h-4 rotate-45 top-3 -left-2 z-10">
                            </div>
                            <div class="z-20 w-full h-full overflow-hidden rounded-lg">
                                <div
                                    class="flex justify-between items-center bg-gray-100 dark:bg-neutral-700 py-2 px-3">
                                    <div class="flex gap-3">
                                        <span class="text-base dark:text-gray-300">
                                            <strong>{{ $comment->user }} </strong>
                                        </span>
                                        {{-- pin button start --}}
                                        {{-- @if(!$comment->isPinned) --}}
                                        <a href="{{route('tasks.pinComment', $comment->id)}}">
                                            <button onclick="pinModal({{$comment->id}})" value="{{ $comment->id }}"
                                                title="@if($comment->isPinned) Unpin @else Pin  @endif">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="h-6 rotate-45 rounded-full p-1 hover:scale-125 transition-all duration-500 @if($comment->isPinned) fill-gray-400 hover:bg-red-500 hover:fill-white @else fill-gray-700 hover:bg-blue-300 hover:fill-white  @endif"
                                                    viewBox="0 0 384 512">
                                                    <path
                                                        d="M32 32C32 14.3 46.3 0 64 0H320c17.7 0 32 14.3 32 32s-14.3 32-32 32H290.5l11.4 148.2c36.7 19.9 65.7 53.2 79.5 94.7l1 3c3.3 9.8 1.6 20.5-4.4 28.8s-15.7 13.3-26 13.3H32c-10.3 0-19.9-4.9-26-13.3s-7.7-19.1-4.4-28.8l1-3c13.8-41.5 42.8-74.8 79.5-94.7L93.5 64H64C46.3 64 32 49.7 32 32zM160 384h64v96c0 17.7-14.3 32-32 32s-32-14.3-32-32V384z" />
                                                </svg>
                                            </button>
                                        </a>
                                        {{-- @endif --}}
                                        {{-- pin button end --}}
                                    </div>

                                    <div class="px-3 py-2 text-end flex items-center gap-2">
                                        <span class="text-base text-gray-500 dark:text-gray-300 pr-3">
                                            {{ $comment->created_at->format('m/d/Y h:i A') }}
                                        </span>
                                        <button class="py-2 px-6 bg-blue-500 text-white rounded hover:bg-blue-700"
                                            value="{{ $comment->id }}" id="reply_mode_on" onclick="CommentReplyModal()">
                                            Reply
                                        </button>
                                    </div>
                                </div>
                                <div class="px-3 py-2">
                                    <div class="dividerforcommentandreply  scrolling-touch overflow-x-auto scroll-none">
                                        <span class="dark:text-slate-300 description  ">
                                            <?php
                                                        // regular expression to open the links in comments
                                                        $re = '#(?<!"|">)(https?:\/\/.*?)(?=\s|[\s]?<)#m';
                                                        $subst = '<a href="$1" target="_blank" class="text-blue-500 text-sm">$1</a>';
                                                        $result = preg_replace($re, $subst, $comment->comments);
                                                        echo $result;
                                                        ?>
                                        </span>
                                    </div>

                                    <div class="mt-1">
                                        @foreach ($comment->getCommentImage as $image)
                                        <a href="{{ asset('storage/tasks/' . $image->name) }}"
                                            class="inline-flex items-center px-3 py-1 mb-2 space-x-2 text-base transition-all border rounded-full hover:bg-primary-100 text-primary-700 border-primary-700 dark:text-slate-300"
                                            target="_blank">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img"
                                                class="w-3 h-3 iconify iconify--bi" preserveAspectRatio="xMidYMid meet"
                                                viewBox="0 0 16 16">
                                                <g fill="currentColor">
                                                    <path
                                                        d="M.002 3a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-12a2 2 0 0 1-2-2V3zm1 9v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V9.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71l-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12zm5-6.5a1.5 1.5 0 1 0-3 0a1.5 1.5 0 0 0 3 0z">
                                                    </path>
                                                </g>
                                            </svg>
                                            <span class="max-w-[110px] text-ellipsis overflow-hidden whitespace-pre">{{
                                                $image->name }}</span>
                                        </a>
                                        @endforeach
                                    </div>
                                    <div class="">
                                        @foreach ($comment->replies->sortBy('id') as $comment_reply)
                                        <hr>
                                        <div
                                            class="flex justify-between items-center bg-gray-100 dark:bg-neutral-700 py-2 px-3">
                                            <span class="text-base dark:text-gray-300">
                                                <strong>{{ $comment_reply->reply_creator->name }}
                                                </strong>
                                            </span>

                                            <span class="text-base text-gray-500 dark:text-gray-300">
                                                {{ \Carbon\Carbon::parse($comment_reply->created_at)->format('m/d/Y h:i
                                                A') }}
                                            </span>
                                        </div>
                                        <div class="">
                                            <?php
                                            $re = '#(?<!"|">)(https?:\/\/.*?)(?=\s|[\s]?<)#m';
                                            $subst = '<a href="$1" target="_blank" class="text-blue-500 text-sm">$1</a>';
                                            $result = preg_replace($re, $subst, $comment_reply->comments);
                                            ?>
                                        </div>

                                        <div class="bg-white mb-1 px-3 py-2 scrolling-touch overflow-x-auto scroll-none">
                                            <?php echo $result; ?>
                                        </div>
                                        @if ($comment_reply->getReplyImage->isNotEmpty())
                                        @foreach ($comment_reply->getReplyImage as $replied)
                                        <a href="{{ asset('storage/tasks/' . $replied->name) }}"
                                            class="inline-flex items-center px-3 py-1 mb-2 space-x-2 text-base transition-all border rounded-full hover:bg-primary-100 text-primary-700 border-primary-700 dark:text-slate-300"
                                            target="_blank">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img"
                                                class="w-3 h-3 iconify iconify--bi" preserveAspectRatio="xMidYMid meet"
                                                viewBox="0 0 16 16">
                                                <g fill="currentColor">
                                                    <path
                                                        d="M.002 3a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-12a2 2 0 0 1-2-2V3zm1 9v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V9.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71l-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12zm5-6.5a1.5 1.5 0 1 0-3 0a1.5 1.5 0 0 0 3 0z">
                                                    </path>
                                                </g>
                                            </svg>
                                            <span class="max-w-[110px] text-ellipsis overflow-hidden whitespace-pre">{{
                                                $replied->name }}</span>
                                        </a>
                                        @endforeach
                                        @endif
                                        <hr>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <!-- Tasks Status -->

                    @if ($comment->currentStatus == '6')
                    <div class="flex space-x-6">
                        <div class="flex flex-col items-center">
                            <span class="w-10 block"></span>
                            <span class="w-[1px] block bg-gray-300 dark:bg-gray-600 grow"></span>
                        </div>
                        <div class="border border-green-500 dark:border-gray-600 w-20 flex grow relative rounded-lg">
                            <div
                                class="flex grow justify-between items-center py-2 px-3  bg-green-200 dark:bg-green-200 text-green-900 rounded-lg">
                                <span> Task has been closed. </span>
                                <span class="text-base">{{ $comment->created_at->format('m/d/Y h:i A') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="flex space-x-6">
                        <div class="flex flex-col items-center">
                            <span
                                class="w-10 h-10 border border-gray-300 rounded-lg flex items-center justify-center text-gray-500 dark:text-gray-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5" />
                                </svg>
                            </span>
                            <span class="w-[1px] block bg-gray-300 dark:bg-gray-600 grow mt-2"></span>
                        </div>
                        <div
                            class="border border-gray-300 dark:border-gray-600 w-20 flex grow relative rounded-lg mt-4">

                            <div class="z-20 w-full h-full overflow-hidden rounded-lg">
                                <div
                                    class="flex justify-between items-center py-2 px-3 bg-gray-100 dark:bg-neutral-700">
                                    <span class="text-base dark:text-slate-300">
                                        <strong>{{ $comment->createdBy }} </strong>
                                    </span>

                                    <span class="text-base text-gray-500 dark:text-slate-300">
                                        {{ $comment->created_at->format('m/d/Y h:i A') }}
                                    </span>
                                </div>

                                <div class="px-3 py-2 flex items-center dark:text-slate-300">
                                    @if ($comment->currentStatus == $comment->previousStatus)
                                    @if ($comment->currentStatus == '7')
                                    <span class="flex flex-wrap items-center">
                                        <span class="text-orange-500 p-1">
                                            Created
                                        </span>
                                        a task.
                                    </span>
                                    @else
                                    <span class="flex flex-wrap items-center">
                                        <span class="text-orange-500 p-1">
                                            Created
                                        </span>
                                        <span class="p-1">and</span>
                                        <span class="text-blue-500 p-1">
                                            Assigned
                                        </span>
                                        <span>a task.</span>
                                    </span>
                                    @endif
                                    @else
                                    <span class="flex flex-wrap items-center gap-x-1">
                                        <span class="gap-1 m-1">Changed status to</span>

                                        @if ($comment->currentStatus == '0')
                                        <span class="text-red-400">
                                            Assigned
                                        </span>
                                        @elseif($comment->currentStatus == '1')
                                        <span class="text-violet-700">
                                            In Progress
                                        </span>
                                        @elseif($comment->currentStatus == '2')
                                        <span class="text-yellow-500">
                                            Assigned for Review
                                        </span>
                                        @elseif($comment->currentStatus == '3')
                                        <span class="text-blue-500">
                                            Reviewing
                                        </span>
                                        @elseif($comment->currentStatus == '4')
                                        <span class="text-cyan-500">
                                            Reviewed
                                        </span>
                                        @elseif($comment->currentStatus == '5')
                                        <span class="text-green-500">
                                            Completed
                                        </span>
                                        @elseif($comment->currentStatus == '6')
                                        <span>
                                            Closed
                                        </span>
                                        @elseif($comment->currentStatus == '7')
                                        <span class="text-gray-500">
                                            Created
                                        </span>
                                        @elseif($comment->currentStatus == '8')
                                        <span class="text-purple-900">
                                            <strong>Staging - Ready to upload</strong>
                                        </span>
                                        @elseif($comment->currentStatus == '9')
                                        <span class="text-purple-700">
                                            <strong>Staging - uploaded</strong>
                                        </span>
                                        @elseif($comment->currentStatus == '10')
                                        <span class="text-teal-600">
                                            <strong>Live - Ready to upload</strong>
                                        </span>
                                        @elseif($comment->currentStatus == '11')
                                        <span class="text-teal-900">
                                            <strong>Live - uploaded</strong>
                                        </span>
                                        @endif
                                        from
                                        @if ($comment->previousStatus == '0')
                                        <span class="text-red-400">
                                            Assigned
                                        </span>
                                        @elseif($comment->previousStatus == '1')
                                        <span class="text-violet-700">
                                            In Progress
                                        </span>
                                        @elseif($comment->previousStatus == '2')
                                        <span class="text-yellow-500">
                                            Assigned for Review
                                        </span>
                                        @elseif($comment->previousStatus == '3')
                                        <span class="text-blue-500">
                                            Reviewing
                                        </span>
                                        @elseif($comment->previousStatus == '4')
                                        <span class="text-cyan-500">
                                            Reviewed
                                        </span>
                                        @elseif($comment->previousStatus == '5')
                                        <span class="text-green-500">
                                            Completed
                                        </span>
                                        @elseif($comment->previousStatus == '6')
                                        <span>
                                            Closed
                                        </span>
                                        @elseif($comment->previousStatus == '7')
                                        <span class="text-gray-500">
                                            Created
                                        </span>
                                        @elseif($comment->previousStatus == '8')
                                        <span class="text-purple-700">
                                            <strong>Staging - Ready to Upload</strong>
                                        </span>
                                        @elseif($comment->previousStatus == '9')
                                        <span class="text-purple-900">
                                            <strong>Staging - Uploaded</strong>
                                        </span>
                                        @elseif($comment->previousStatus == '10')
                                        <span class="text-teal-600">
                                            <strong>Live - Ready to Upload</strong>
                                        </span>
                                        @elseif($comment->previousStatus == '11')
                                        <span class="text-teal-900">
                                            <strong>Live - Uploaded</strong>
                                        </span>
                                        @endif
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
    <div>
        <div class="fixed z-50 top-0 w-2/3 left-1/6 hidden" id="commentReply">
            <div class="flex items-center justify-center min-height-100vh pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity">
                    <div class="absolute inset-0 bg-gray-900 opacity-75" />
                </div>
                <div class="inline-block align-center bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle "
                    role="dialog" aria-modal="true" aria-labelledby="modal-headline"
                    style="width: 40%; margin-top: 100px;">
                    <div class="bg-white px-4 sm:p-6 sm:pb-4">
                        <form id="commentForm" action="{{ route('tasks.comments.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="text" id="comment_id" name="comment_id" class="hidden"><br>
                            <input type="text" class="hidden" name="taskId" value="{{ $task->id }}">
                            <x-label for="comment" required>Add Reply:</x-label>
                            <div style="max-height: 200px; overflow-y: auto;">
                                <!-- <textarea name="comment" id="reply" class="reply" data-quilljs
                                    placeholder="Please enter text"></textarea> -->
                                    <x-editor name='comment' id="reply" class="reply" height='130px'> </x-editor>

                            </div>
                            <div class="grid my-5 ">
                                <x-label for="attachments">Attachments:</x-label>
                                <x-input type="file" id="attachments" name="attachments[]"
                                    accept="application/pdf,image/jpeg,image/png,.csv" multiple />
                            </div>
                            <div class=" px-4 py-3 text-right">
                                <x-button type="button" id="cancle_reply" onclick="CommentReplyModal()">Cancel
                                </x-button>
                                <x-button type="submit">Reply</x-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">
    function CommentReplyModal() {
            document.getElementById('commentReply').classList.toggle('hidden');
        }

        $(document).ready(function() {
            $('.multiselect').select2();
            $(document).on('click','#reply_mode_on',function() {
                $data = $(this).val();
                $('#comment_id').val($data);
            });

            $(document).on('click','#cancle_reply',function(){
                console.log('Empty reply field');
                $('#reply').val('');
            });

            $(document).on('click','#deployLogsModal',function() {
               let project_name = $(this).data('log-id');
               $.ajax({
                   url: "/deploy/log_list",
                   method: "get",
                   data: {project_name: project_name},
                   success: function(response) {
                       $('#deploylogDetails').html('');
                        response.forEach(function(item) {
                            let date = new Date(item.created_at);
                            let localDateString = date.toLocaleDateString();
                            let localTimeString = date.toLocaleTimeString();
                           let logsDetails =
                               `<tr>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-600 whitespace-no-wrap">${item.created_by}</p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-600 whitespace-no-wrap">${item.pull_request ?? '---'}</p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-600 whitespace-no-wrap">${item.summary ?? '---'}</p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-600 whitespace-no-wrap">${item.task_id}</p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-600 whitespace-no-wrap">${item.project_name}</p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-600 whitespace-no-wrap">${item.deploy}</p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-600 whitespace-no-wrap">${localDateString + ' ' +localTimeString }</p>
                                </td>
                            </tr>`;
                           $('#deploylogDetails').append(logsDetails);
                       });
                   }
               });
               console.log(project_name,'Merry John');
            });
            $(document).on('click','#refreshModal',function() {
                $('#vvv').addClass('hidden');
            });
            $(document).on('change','#deploy_stage_id',function() {
                let taskId = $(this).data('id');
                let stagging_name = $(this).val();
                $.ajax({
                    url: '/deploy',
                    data: {taskInfo: taskId, stagging: stagging_name},
                    method: 'get',
                    success: function (response) {
                        console.log(response.ParentprojectId);
                        let listdata = response.stageStates;
                        let pipelineError = response.pipeline_error;
                        let statusSuccess = response.success;
                        let statusError = response.error;
                        let parentProjectId = response.ParentprojectId;
                        if (response.success) {
                            window.location.reload(); // Refresh the page
                        }
                        if(pipelineError) {
                            $('#dispaly_error_message').removeClass('hidden');
                            $('#dispaly_error_message').addClass('text-white','bg-red-500');
                            $('#dispaly_error_message').html(pipelineError);
                            setTimeout(function() {
                                $('#dispaly_error_message').addClass('hidden');
                            }, 3000);
                        }
                        if(statusSuccess) {
                            $('#dispaly_success_message').removeClass('hidden');
                            $('#dispaly_success_message').html(statusSuccess);
                            setTimeout(function() {
                                $('#dispaly_success_message').addClass('hidden');
                            }, 3000);
                        }
                        if(statusError) {
                            $('#dispaly_error_message').removeClass('hidden');
                            $('#dispaly_error_message').addClass('text-white','bg-red-500');
                            $('#dispaly_error_message').html(statusError);
                            setTimeout(function() {
                                $('#dispaly_error_message').addClass('hidden');
                            }, 3000);
                        }
                        let source_summary_detail = response.stageStates[0].actionStates ?? [];
                        source_summary_detail.forEach((summary) => {
                            summary_info = summary.latestExecution.summary;
                            $('#hide_pipeline_summary').removeClass('hidden');
                            $('#hide_pipeline_summary').html('Summary : '+ summary_info).addClass('text-bold font-size-lg');
                        });
                         if (response.pipeline_name)
                        {
                            $('#pipeline_name').html(response.pipeline_name);
                        }
                        if(listdata) {
                            $('#deploy_stage_id').addClass('hidden');
                            $('#refreshModal').removeClass('hidden');
                            $('#hide_pipeline_name').removeClass('hidden');
                            $('#pipelineTableData').html('');
                            $('#pipelinetable').show();
                            listdata.forEach((item) => {
                                item.actionStates.forEach((actionstate) => {
                                if(actionstate.latestExecution.status  === 'InProgress') {
                                            $('#deploy_data').show();
                                            $('#deployProjectId').val(parentProjectId);
                                            $('#deploy_taskId').val(taskId.taskId);
                                            $('#deploy_projectName').val(taskId.project_name);
                                            $('#deploy_pipeline_name').val(response.pipeline_name);
                                            $('#deploy_stage_name').val(item.stageName);
                                            $('#deploy_action_name').val(actionstate.actionName);
                                            $pipeline_token = actionstate.latestExecution.token;
                                            if ($pipeline_token) {
                                                $('#deploy_token').val($pipeline_token);
                                            } else {
                                                $('#deploy_token').val('');
                                            }
                                    }
                                        let UtcDate = actionstate.latestExecution.lastStatusChange;
                                        let localTimeString = new Date(UtcDate).toLocaleTimeString();
                                        let localDateString = new Date(UtcDate).toLocaleDateString();
                                        let tableData = `
                                        <tr>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <p class="text-gray-600 whitespace-no-wrap">${item.stageName}</p>
                                        </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <p class="text-gray-600 whitespace-no-wrap">${actionstate.actionName}</p>
                                        </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <p class="text-gray-600 whitespace-no-wrap">${actionstate.latestExecution.status}</p>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <p class="text-gray-600 whitespace-no-wrap">${actionstate.latestExecution.summary ?? null}</p>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <p class="text-gray-600 whitespace-no-wrap">${actionstate.latestExecution.lastStatusChange ? localDateString + ' ' + localTimeString: null}</p>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <p class="text-gray-600 whitespace-no-wrap">${actionstate.entityUrl ?? null}</p>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <p class="text-gray-600 whitespace-no-wrap">
                                                    <a href="${actionstate.revisionUrl}" target="_blank"> ${actionstate.revisionUrl ?? null }</a></p>
                                        </td>
                                        </tr>
                                    `;
                                        $('#pipelineTableData').append(tableData)
                                });
                            });
                        }
                    }
                })
            });
        });

        $("#view-pinned").click(function () {
            $(".collapsePinned").slideToggle();
        });
</script>

<style>
    .collapsePinned {
        display: none;
    }
</style>
@endsection
