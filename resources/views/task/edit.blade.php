@extends('layouts.app')
@section('content')
<div class="grid lg:grid-cols-2 grid-cols-1 gap-4">
    <div class="col-span-1 ">
        <div class="p-5 border border-primary dark:border-gray-700 rounded-lg bg-white dark:bg-neutral-800">
            <div class="flex items-center justify-between">
                <h3 class="text-2xl font-bold">Task Details page</h3>
                @php
                    $user_id = auth()->user()->id;
                    $isAsignee = in_array($user_id, $assigneesIds);
                    $isReviewer = in_array($user_id, $reviewersIds) || $user_id == 3;

                    $onlyReviewerAllowedStatuses = [
                        'Live - Ready to upload',
                        'Live - Uploaded',
                        'Staging - Ready to upload',
                        'Staging - Uploaded',
                        'Dev - Ready to upload',
                        'Dev - uploaded',
                    ];

                    $hasDevlopmentPermission =  true; //in_array('DEVELOPMENT', $userPermissions); need to update later by mukesh
                    $hasStagingPermission =  true; //in_array('STAGING', $userPermissions); need to update later by mukesh
                    $hasProductionPermission =  true; //in_array('PRODUCTION', $userPermissions); need to update later by mukesh
                @endphp

                <div class="w-1/2 grid grid-cols-3 gap-3">
                    @if ($deploy_permission_granted)
                                <div class="col-span-3 flex gap-2 justify-end">
                                    <x-button class="float-right deployModal btn btn-success" data-modal="modal" data-target="#openDeployModal"
                                        id="deployModal">Deploy
                                    </x-button>

                                <x-button class="float-right deployModal btn btn-success" data-modal="modal" data-target="#openDeployLogsModal"
                                    data-log-id="{{ $task->project->id }}" id="deployLogsModal">Deploy Logs
                                </x-button>
                            </div>
                    @endif
                    <div></div>
                    <div class="col-span-2 flex gap-2 justify-end">
                        @if(in_array(auth()->user()->id, $permissions))
                        @if($task->project && !$task->project->sub_projects)
                            <div>
                                <x-select id="repositorySelect">
                                    <option selected disabled>Select Repository</option>
                                    @foreach($task->project->subprojects as $subproject)
                                    @if($subproject->repository_name)
                                    <option value="{{$subproject->repository_name}}">{{$subproject->repository_name}}</option>
                                    @endif
                                    @endforeach
                                </x-select>
                            </div>
                        @endif
                            <div>
                                <x-button class="whitespace-nowrap" data-id="{{$task->project->repository_name}}" data-modal="modal" data-target="#openPrModal" id="pull">pull Requests</x-button>
                            </div>
                        @endif
                    </div>
                </div>
                <x-modal-view target="openPrModal" style="width: 500px;">
                    <x-slot:header>
                        Pull Requests
                    </x-slot:header>

                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Pr No.</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider w-56">Title</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider w-56">Description</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Source</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Target</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Created By</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Action</th>
                            </tr>
                            </thead>
                        <tbody id="pullRequestTableBody">

                        </tbody>
                    </table>

                </x-modal-view>
            </div>
            <div class="flex flex-row justify-end mt-3">
                @if(!$watchListTask) 
                    <div class="bg-gray-200 rounded px-3 py-2">
                        <label for="task_watchlists">Task WatchList</label>
                        <x-input type="checkbox"  data-modal="modal" data-target="#openTaskWatchListModal"  id="watchlistmodal" data-id="{{$task->id}}"></x-input>
                    </div>
                @endif  
            </div>
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
                        {{-- @if ($task->status == '0') --}}
                        @foreach ( $taskStatus as $status )
                        @if ($status->value == $task->status)
                        {{-- <span class="px-3 py-1 text-red-900 bg-red-200 rounded-full text-sm font-semibold">
                           {{ $status->name }}
                        </span> --}}
                        @if($task->status == '0')
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
                        @else
                        <span
                            class="px-3 py-1 text-teal-900 bg-red-200 rounded-full text-base font-semibold whitespace-nowrap">
                            {{ $status->name }}
                        </span>
                        @endif
                        @endif
                        @endforeach
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
                            <option value="{{ $project->id }}"  {{ ($task->project_id == $project->id) ? 'selected' : '' }}>{{ $project->name }}</option>
                            {{-- <optgroup label="{{ $project->name }}"> --}}
                                @foreach ($project->subprojects as $subproject)
                                    <option value="{{ $subproject->id }}" class="pl-4"
                                            {{ in_array($task->project_id, [$subproject->id]) ? 'selected' : '' }}>
                                            <span>&nbsp;&nbsp;{{ $subproject->name }}</span>
                                    </option>
                                @endforeach
                            {{-- </optgroup> --}}
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
                        ((in_array($task->status, ['0','1', '2', '3', '4','6']) || in_array($task->taskStatus, $onlyReviewerAllowedStatuses)) && in_array(auth()->user()->id, $reviewersIds
                        ))))))
                        <div class="grid mb-4 mt-5">
                            <x-label for="title">Status:</x-label>
                            <x-select name="status">

                                @foreach ($taskStatus as $status)

                                    @if (in_array($status->value, [0, 1, 2]))
                                        <option value="{{$status->value}}"
                                            @if ($task->status == $status->value) selected @endif
                                            @if (!$isReviewer && $status->value == 0  && !$isSuperAdmin) disabled @endif
                                            @if (!$isAsignee && $status->value == 1  && !$isSuperAdmin ) disabled @endif
                                            @if (($task->status != 1 || !$isAsignee  && !$isSuperAdmin) && $status->value == 2) disabled @endif
                                        >
                                            {{$status->name}}
                                        </option>
                                    @endif

                                    @if($isSuperAdmin || $isReviewer)
                                        @if (($status->value == 3 || $status->value == 4 || $status->value == 7 || $status->value == 6))
                                            <option value="{{$status->value}}"
                                                @if ($task->status == $status->value) selected @endif
                                                @if ($status->value == 7) disabled @endif
                                                @if(!$isSuperAdmin && (!$isReviewer) && in_array($status->value, [3, 4])) disabled @endif
                                            >
                                                {{$status->name}}
                                            </option>
                                        @endif
                                    @endif

                                    @if (in_array($status->name, $onlyReviewerAllowedStatuses))
                                        <option value="{{$status->value}}"
                                            @if ($task->status == $status->value) selected @endif
                                            @if (!$isReviewer && in_array($status->name, $onlyReviewerAllowedStatuses)) disabled @endif
                                            @if (!$hasStagingPermission && in_array($status->name, ['Staging - Ready to upload', 'Staging - Uploaded'])) disabled @endIf
                                            @if (!$hasDevlopmentPermission && in_array($status->name, ['Dev - Ready to upload', 'Dev - uploaded'])) disabled @endIf
                                            @if (!$hasProductionPermission && in_array($status->name, ['Live - Ready to upload', 'Live - Uploaded'])) disabled @endIf
                                        >
                                            {{$status->name}}
                                    </option>
                                    @endif

                                    {{-- in_array($status->value, ['8', '9', '10', '11', '5'] --}}
                                    @if($isSuperAdmin && !in_array($status->value, [0, 1, 2, 3, 4, 6, 7]) && !in_array($status->name, $onlyReviewerAllowedStatuses))
                                        <option value="{{$status->value}}"
                                            @if ($task->status == $status->value) selected @endif
                                        >
                                            {{$status->name}}
                                        </option>
                                    @endif
                                @endforeach

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
                        <div class="flex flex-col items-center mt-4">
                            @if(isset($comment->profie_picture) && !empty($comment->profie_picture))
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white">
                                    <img src="{{asset('storage/profiles/'.$comment->profie_picture)}}"  title="{{$comment->user}}" alt="profile picture"  class="rounded-full w-full h-full object-center object-cover cursor-pointer">
                                </div>
                            @else
                                <span class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white">
                                    <?php
                                        $userName = explode(' ', $comment->user);
                                        echo count($userName) > 1 ? substr($userName[0], 0, 1) . substr($userName[1], 0, 1) : 'Bot';
                                    ?>
                                </span>
                            @endif
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
                                        <span class="dark:text-slate-300 description break-words ">
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
                        <div class="flex flex-col items-center mt-4">
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
                                        @else
                                            @foreach ($taskStatus as $status)
                                                @if ($comment->currentStatus == $status->value)
                                                    <span class="text-teal-600">
                                                        <strong>{{ $status->name }}</strong>
                                                    </span>
                                                @endif
                                            @endforeach
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
                                        @else
                                        @foreach ($taskStatus as $status)
                                            @if ($comment->previousStatus == $status->value)
                                                <span class="text-teal-600">
                                                    <strong>{{ $status->name }}</strong>
                                                </span>
                                            @endif
                                        @endforeach
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
<x-modal-view target="confirmation" style="width: 500px;">
    <a
        class=" text-primary-600 rounded-md text-2xl float-right cursor-pointer p-2 dark:text-gray-400 cancelMergeButton">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </a>
    <div class="p-2">
        <h2 class="font-bold text-2xl border-b pb-3 mb-2">Merge PR</h2>
        <div class="flex">
            <label class="mr-2 text-gray-500">PR Title:</label>
            <h3 class="font-semibold" id="Title"></h3>
        </div>
        <h7 class='text-sm text-gray-800 font-semibold  dark:text-gray-300 '>
            Are you sure you want to Merge?
        </h7>

        </div>

    <form action="{{ route('merge-pull-request') }}" method="post">
        @csrf
        @method('GET')
        <x-input type="hidden" name="reviewersIds" value="{{implode(',',$permissions)}}"></x-input>
        <x-input type="hidden" name="repository_name" id="repository_name"></x-input>
        <x-input type="hidden" name="pull_request" id="pull_request"></x-input>
        <x-input type="hidden" name="taskId" id="taskId"></x-input>

        <div class="flex items-center gap-2 pt-4 pb-4 justify-end rounded-b dark:border-gray-600">
            <x-button
                class="py-2.5 px-5 ms-3 text-sm font-medium bg-red-500 hover:bg-red-400  hover:border-red-500 border-red-600 cancelMergeButton ">
                Cancel
            </x-button>
            <x-button type="submit">
                Yes
            </x-button>
    </form>

    </div>
</x-modal-view>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div>
    <x-modal-view target="openDeployModal">
            <x-slot:header>
            Deploy
            </x-slot>
            <form id="requestDeployForm" onsubmit="event.preventDefault();" class="w-full border border-gray-300 mb-5 p-5 bg-gray-50 rounded-md">
                @csrf

                <div class="w-[480px]">
                    <div class="mb-3">
                        @if($task->project->sub_projects)
                            <input type="hidden" name="taskInfo" value="{{ json_encode(['projectId' => $task->project->id, 'taskId' => $task->id, 'project_branch' => $task->project->branch]) }}">
                        @else
                            <label class="mb-2">Select Project </label>
                                <select name="taskInfo" class="w-full">
                                    @foreach($projects->where('sub_projects', $task->project_id) as $project)
                                        <option value="{{ json_encode(['projectId' => $project->id, 'taskId' => $task->id, 'project_branch' => $project->branch]) }}">
                                            {{$project->name}}
                                        </option>
                                    @endforeach
                                </select>

                        @endif
                    </div>
                    <span>Select Deployment Branch</span>
                    <div class="mb-3">
                        <select name="stage_name" class="w-full">
                            {{-- @if($hasDevlopmentPermission) --}}
                                <option value="development">Development</option>
                            {{-- @endIf --}}
                            {{-- @if($hasStagingPermission) --}}
                                <option value="staging">Staging</option>
                            {{-- @endIf --}}
                            {{-- @if($hasProductionPermission)) --}}
                                <option value="production">Production</option>
                            {{-- @endIf --}}
                        </select>
                    </div>
                    <div class="flex justify-end">
                        <x-button id="submitButtonId" type="submit" class="text-end requestDeploy">Request</x-button>
                    </div>
                </div>
            </form>
            <div id="requestDetailPreview" class="max-w-screen mt-5">
            </div>
    </x-modal-view>
</div>
<div>
    <x-modal-view target="openTaskWatchListModal">
            <x-slot:header>
                Task Watch List
            </x-slot>
            <form action="{{ route('task.watchlists.store')}}" method="post">
                @csrf
                <div class="grid grid-rows-2 bg-gray-100 rounded px-3 pt-3 self-start mb-3">
                        <div class="">
                            <p>Are you sure you want to add this task to your WatchList?</p>
                        </div>
                        <div>
                            <div class="grid grid-cols-1 mb-2">
                              <x-input id="watchlist_task_id" name="task_id" class="hidden" placeholder="TaskId"></x-input>
                            </div>
                            <div class="grid grid-cols-2 mb-2">
                                <div> 
                                    <x-button type="submit">Add to Watchlist</x-button>
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
            </div>
    </x-modal-view>
</div>
<script type="text/javascript">
function CommentReplyModal() {
    document.getElementById('commentReply').classList.toggle('hidden');
}

$(document).ready(function() {
    $('.multiselect').select2();
    $(document).on('click', '#reply_mode_on', function() {
        $data = $(this).val();
        $('#comment_id').val($data);
    });

    $(document).on('click', '#cancle_reply', function() {
        console.log('Empty reply field');
        $('#reply').val('');
    });

    $(document).on('click', '#deployLogsModal', function() {
        let project_id = $(this).data('log-id');
        let task_id =  @json(request()->route('task'));
        $.ajax({
            url: "/deploy/log_list",
            method: "get",
            data: { project_id: project_id, task_id: task_id },
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
                                <p class="text-gray-600 whitespace-no-wrap">${localDateString + ' ' + localTimeString }</p>
                            </td>
                        </tr>`;
                    $('#deploylogDetails').append(logsDetails);
                });
            }
        });
        // console.log(project_name, 'Merry John');
    });

    $(document).on('click', '#refreshModal', function() {
        $('#vvv').addClass('hidden');
    });

    function getDeployDetail(formData){
        $.ajax({
                type: 'get',
                url: '/deploy',
                data: formData,
                success: function(response) {
                    $('#requestDetailPreview').html(response);
                    $('#submitButtonId').prop('disabled', false);
                    $('#submitButtonId').html('Request');

                    if (response && (response.pipeline_error || response.message)) {
                        flashNotification(response.pipeline_error ?? response.message)
                        }
                    },

                error: function(xhr, status, error) {
                    }
            }
        );
    }

    function flashNotification(message)
    {
        html = `<div
            class="fade -translate-x-1/2 fixed z-50 w-full max-w-sm p-3 transition-all transform bg-red-700 rounded-lg shadow-xl bounce left-1/2 top-3"
            role="alert" x-data="{alertOpen: true}" x-show="alertOpen">
            <strong class="text-white">${message}</strong>
            <a class="absolute inline-flex items-center justify-center w-5 h-5 text-white transition-all duration-100 bg-black/[.05] rounded-full cursor-pointer top-2 right-2"
                @click="alertOpen = false">
                <svg style="width:16px;height:16px" viewBox="0 0 24 24">
                    <path fill="currentColor"
                        d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" />
                </svg>
            </a>
        </div>
        `

        $('.flash').append(html);

        $('.fade').fadeOut(5000);
    }

    $('#requestDeployForm').submit(function(event) {
        event.preventDefault();
        var formData = $(this).serialize();

        if ( $('#requestDetailPreview').children().length > 0 ) {
            $('#requestDetailPreview').html(`
                <div class="flex items-center justify-center w-[76vw] h-[50vh]">
                    <div class="relative">
                        <div class="h-24 w-24 rounded-full border-t-8 border-b-8 border-gray-200"></div>
                        <div class="absolute top-0 left-0 h-24 w-24 rounded-full border-t-8 border-b-8 border-blue-500 animate-spin">
                        </div>
                    </div>
                </div>`
            )
        }
        // $('#submitButtonId').prop('disabled', true);
        $('.requestDeploy').attr("disabled", "");
        // Send an AJAX POST request
        getDeployDetail(formData)
    });
// });

    // view pinned comments
    $("#view-pinned").click(function() {
        $(".collapsePinned").slideToggle();
    });


    function removeChildren() {
        $('#requestDetailPreview').empty(); // Removes all child elements of the element with id "container"
        $('#openDeployModal').addClass('hidden')
    }

// $(document).ready(function() {

    var data = {!! json_encode($task) !!};

    // Function to handle repository selection change
    $('#repositorySelect').change(function() {
        var selectedRepository = $(this).val();
        $('#pull').attr('data-id', selectedRepository);
    });

    // Click event for the pull button
    $(document).on('click', '#pull', function() {
        let repository_name =document.getElementById('pull').getAttribute('data-id');
        console.log('Pulling requests for repository:', repository_name);
        let assigneesIds = {!! json_encode($assigneesIds) !!};
        let reviewersIds = {!! json_encode($permissions) !!};
        $('#pullRequestTableBody').empty();
        var loadingSpinner = `
        <tr id="loading">
            <td colspan="8" style="text-align: center; padding: 20px;">
                <div class="flex items-center justify-center w-full h-[30vh]">
                    <div class="relative">
                        <div class="h-24 w-24 rounded-full border-t-8 border-b-8 border-gray-200"></div>
                        <div class="absolute top-0 left-0 h-24 w-24 rounded-full border-t-8 border-b-8 border-blue-500 animate-spin">
             </div>
                    </div>
                </div>
            </td>
        </tr>
    `;
    $('#pullRequestTableBody').append(loadingSpinner);
        $.ajax({
            url: "/pull_requests",
            method: "get",
            data: {
                repository_name: repository_name,
                assigneesIds: assigneesIds,
                reviewersIds: reviewersIds
            },
            success: function(response) {
                if(response.status == 'error'){
                    flashNotification(response.message)
                    return
                }
                // Handle the response here
                console.log('response data length:', response.data);
                populatePullRequestTable(response.data, repository_name);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching pull requests:', error);
            }
        });
    });

    // Function to populate the pull request table
    function populatePullRequestTable(pullRequests, repository_name) {
        var tableBody = $('#pullRequestTableBody');
        if (pullRequests.length === 0) {
            var noDataMessageRow = `
            <tr id="noDataMessage">
                <td colspan="8" class="text-center py-5 px-5 text-lg">No pull requests available</td>
            </tr>`;
        tableBody.append(noDataMessageRow);
        $('#loading').hide();
        } else {
            $('#noDataMessage').hide(); // Hide the no data message
            $('#loading').hide();
            pullRequests.forEach(function(pr) {
                var row = $('<tr>');
                row.append($('<td  class="px-5 py-5 border-b border-gray-200 bg-white text-sm">').text(pr.number));
                row.append($('<td  class="px-5 py-5 border-b border-gray-200 bg-white text-sm">').text(pr.title));
                row.append($('<td  class="px-5 py-5 border-b border-gray-200 bg-white text-sm">').text(pr.body));
                row.append($('<td  class="px-5 py-5 border-b border-gray-200 bg-white text-sm">').text(pr.source.replace(":", "/")));
                row.append($('<td  class="px-5 py-5 border-b border-gray-200 bg-white text-sm">').text(pr.target.replace(":","/")));
                row.append($('<td  class="px-5 py-5 border-b border-gray-200 bg-white text-sm">').text(pr.username)); // Accessing the login name
                var mergeButton = $('<button class="merge-btn bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded" data-modal="modal" data-target="#confirmation">Merge</button>');
                mergeButton.data('repository_name', repository_name);
                mergeButton.data('pull_request', pr);
                row.append($('<td  class="px-5 py-5 border-b border-gray-200 bg-white text-sm">').append(mergeButton));
                tableBody.append(row);
            });
        }

    }

    // Click event for the merge button
    $(document).on('click', '.merge-btn', function() {
        var repository_name = $(this).data('repository_name');
        var pull_request = $(this).data('pull_request');
        var taskId = data.id;
        document.getElementById("repository_name").value = repository_name;
        document.getElementById("pull_request").value =JSON.stringify(pull_request);
        document.getElementById("taskId").value = taskId;
        document.getElementById("Title").innerText = pull_request['title'].length > 100 ? pull_request['title'].slice(0, 100) + '...' : pull_request['title'];

    });

    $('.cancelMergeButton').each(function() {
            $(this).on('click', function() {
                $(this).closest('#confirmation').addClass('hidden');
            });
        });

    });

    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.toggle('hidden');
    }

    let modalEle = $('#openTaskWatchListModal');
    let watchListCheckBox = document.getElementById('watchlistmodal');
    let taskIdForWatchList = document.getElementById('watchlist_task_id');
    let watchListModalCancelButton = document.getElementById('cancleWatchListModal');
    watchListCheckBox.addEventListener('click', function () {
        let taskId = watchListCheckBox.getAttribute('data-id');
        taskIdForWatchList.value = taskId;
    })
    
    watchListModalCancelButton.addEventListener('click', function () {
        toggleModal('openTaskWatchListModal');
        watchListCheckBox.checked = false;
    });

    $(document).on('click', '[data-dismiss="modal"]', function () {
        let modal = $(this).closest('.trigger_model');
        modal.addClass('hidden'); // Assuming this class hides the modal
        watchListCheckBox.checked = false;
    });
</script>

<style>
    .collapsePinned {
        display: none;
    }
</style>
@endsection
