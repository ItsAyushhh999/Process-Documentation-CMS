@php
// dump($assignees)
//dump($task)

// @if (count($attachments))
// dump($attachments)
//dump($deploy_permission_granted);
$deploy_permission_granted = true;
@endphp



<div class="h-full w-full overflow-auto ounded-t-lg">
    <div class="" id="displayFrame">
        <div
            class="header border-b p-5 sticky top-0 z-10 bg-white/80 backdrop-blur-xl flex items-center justify-between rounded-t-lg">
            <div class="flex items-center gap-x-2 w-full divide-x">
                <span class="text-darkblue">#{{$task->id}}</span>
                <span class="dark:text-gray-300 col-span-3 pl-2">
                    {{ $task->project?->parentProject->name ?? '' }}
                    @if ($task->project?->parentProject)
                    /
                    @endif
                    <strong>{{ $task->projectName }}</strong>
                </span>
            </div>
            <!-- <div class="flex items-center gap-4 pr-10">
                @if ($isSuperAdmin)
                <x-button type="button" data-modal="modal" data-target="#taskEditModal" outline>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-pencil-square" viewBox="0 0 16 16">
                        <path
                            d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                        <path fill-rule="evenodd"
                            d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                    </svg>
                    Edit
                </x-button>
                @endif
            </div> -->
            @if($deploy_permission_granted)
            <div class=" inline-flex items-center gap-3">
                <x-button class="float-right deployModal btn btn-success" data-modal="modal"
                    data-target="#openDeployModal" id="deployModal">Deploy</x-button>
                <x-button class="float-right deployModal btn btn-success mr-3 whitespace-nowrap" data-modal="modal"
                    data-target="#openDeployLogsModal" data-log-id="{{ $task->project->name }}"
                    id="deployLogsModal" outline>Deploy Logs
                </x-button>
                <div>
                    <x-modal-view target="openDeployLogsModal">
                        <x-slot:header>
                            Deploy Logs
                            </x-slot>
                            <div id="openDeployLogsModal" class="">
                                <table class="w-full leading-normal" id="deploylogsTable">
                                    <thead>
                                        <tr>
                                            <th
                                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">
                                                Created By
                                            </th>

                                            <th
                                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">
                                                Deploy Pull Request
                                            </th>

                                            <th
                                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">
                                                Deploy Summary
                                            </th>
                                            <th
                                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">
                                                Task Id
                                            </th>
                                            <th
                                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">
                                                Project Name
                                            </th>
                                            <th
                                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">
                                                Deploy
                                            </th>
                                            <th
                                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">
                                                Log Created Date
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="deploylogDetails">
                                    </tbody>
                                </table>
                            </div>
                    </x-modal-view>
                    <x-modal-view target="openDeployModal">
                        <x-slot:header>
                            Deploy Status
                            </x-slot>
                            <x-button class="float-right deployModal btn btn-success mb-3 mt-3 hidden"
                                data-modal="modal" data-target="#openDeployStatusModal" id="refreshModal"
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
                                    <h2 class="mb-3 hidden" id="hide_pipeline_name">Pipeline Name: <span
                                            id="pipeline_name"></span>
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
                                <div class="change_deploy bg-white shadow rounded mt-3 mb-3 px-3 py-3"
                                    id="deploy_data" hidden>
                                    <form action="{{ route('deploy') }}" method="post">
                                        @method('post')
                                        @csrf
                                        <x-input id="deploy_token" type="text" name="deploy_token" hidden />
                                        <x-input id="deploy_taskId" type="text" name="deploy_taskId" hidden />
                                        <x-input id="deploy_projectName" type="text" name="deploy_projectName"
                                            hidden />
                                        <x-input id="deploy_pipeline_name" type="text" name="deploy_pipeline_name"
                                            hidden />
                                        <x-input id="deploy_stage_name" type="text" name="deploy_stage_name"
                                            hidden />
                                        <x-input id="deploy_action_name" type="text" name="deploy_action_name"
                                            hidden />
                                        <div class="mx-auto max-w-sm text-center flex flex-wrap justify-center">
                                            <div class="flex items-center mr-4 mb-4">
                                                <label for="approve" class="flex items-center cursor-pointer">
                                                    Approve</label>
                                                <x-input id="approve" type="radio" name="deploy" value="Approved" />
                                            </div>

                                            <div class="flex items-center mr-4 mb-4">
                                                <label for="reject"
                                                    class="flex items-center cursor-pointer">Reject</label>
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
        </div>

        <div class="grid grid-cols-2 gap-16 p-5">
            <div class="">

                <h1 class='text-xl text-gray-800 font-bold dark:text-gray-300 mb-6'>
                    {{$task->title}}
                </h1>
                <div class="grid gap-6 ">
                    <div class="flex items-center">
                        <div class="w-[160px] text-slate-600 dark:text-slate-300 inline-flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-4 h-4 bi bi-calendar4-week" viewBox="0 0 16 16">
                                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v1h14V3a1 1 0 0 0-1-1zm13 3H1v9a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1z"/>
                                <path d="M11 7.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm-3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm-2 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm-3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z"/>
                              </svg>
                            <span>Created At/By</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-slate-600 dark:text-slate-300">
                                {{$task->created_at->format('m/d/Y h:i A')}}
                            </span>
                            <x-user-chip :name="$task->createdBy" image="663358ba356b1.jpg"></x-user-chip>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="w-[160px] text-slate-600 dark:text-slate-300 inline-flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-4 h-4 bi bi-calendar4-week" viewBox="0 0 16 16">
                                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v1h14V3a1 1 0 0 0-1-1zm13 3H1v9a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1z"/>
                                <path d="M11 7.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm-3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm-2 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm-3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z"/>
                              </svg>
                            <span>Deadline</span>
                        </div>
                        <div class="flex items-center">
                            <span class="font-semibold text-slate-600 dark:text-slate-300">
                                {{$task->created_at->format('m/d/Y h:i A')}}
                            </span>
                        </div>
                    </div>
                    @if($assignees)
                    <div class="flex items-start">
                        <div class="w-[160px] text-slate-600 dark:text-slate-300 inline-flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-4 h-4 bi bi-people" viewBox="0 0 16 16">
                                <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4"/>
                              </svg>
                            <span>Assignees</span>
                        </div>                        
                        <div class="flex gap-2 flex-wrap flex-1">
                            @foreach( $assignees as $elem )
                            <x-user-chip :name="$elem->collaboratorName" :image="$elem->profile_picture"></x-user-chip>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    @if($reviewers)
                    <div class="flex items-start">
                        <div class="w-[160px] text-slate-600 dark:text-slate-300 inline-flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-4 h-4 bi bi-people" viewBox="0 0 16 16">
                                <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4"/>
                              </svg>
                            <span>Reviewers</span>
                        </div>  
                        <div class="flex gap-2 flex-wrap flex-1">
                            @foreach( $reviewers as $elem )
                            <x-user-chip :name="$elem->collaboratorName" :image="$elem->profile_picture"></x-user-chip>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="">
                        <div class="text-slate-600 dark:text-slate-300 inline-flex items-center gap-3 mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-4 h-4 bi bi-file-text" viewBox="0 0 16 16">
                                <path d="M5 4a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1zm-.5 2.5A.5.5 0 0 1 5 6h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5M5 8a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1zm0 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1z"/>
                                <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2zm10-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1"/>
                              </svg>
                            <span>Description</span>
                        </div>  
                        <div
                            class="px-4 py-3 rounded-lg border text-gray-500 whitespace-normal dark:text-gray-300 truncate">
                            {!! html_entity_decode($task->description) !!}
                        </div>
                    </div>

                    @if (count($attachments))
                    <div class="">
                        <div class="text-slate-600 dark:text-slate-300 inline-flex items-center gap-3 mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-4 h-4 bi bi-paperclip" viewBox="0 0 16 16">
                                <path d="M4.5 3a2.5 2.5 0 0 1 5 0v9a1.5 1.5 0 0 1-3 0V5a.5.5 0 0 1 1 0v7a.5.5 0 0 0 1 0V3a1.5 1.5 0 1 0-3 0v9a2.5 2.5 0 0 0 5 0V5a.5.5 0 0 1 1 0v7a3.5 3.5 0 1 1-7 0z"/>
                              </svg>
                            <span>Attachments</span>
                        </div>
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

            </div>
            <div class="gap-4 flex flex-col">
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
                                (in_array($task->status, ['0','1', '2']) && (in_array(auth()->user()->id, $assigneesIds
                                ))
                                ||
                                (in_array($task->status, ['0','1', '2', '3', '4']) && in_array(auth()->user()->id,
                                $reviewersIds
                                ))))) && $task->status != '6')
                                <div class="grid mb-4 mt-5">
                                    <x-label for="title">Status:</x-label>
                                    <x-select name="status">
                                        @if($isSuperAdmin || in_array(auth()->user()->id, $reviewersIds))
                                        <option value="7" disabled @if($task->status == '7') selected @endif>
                                            Created
                                        </option>
                                        @endif
                                        <option value="0" @if(!$isSuperAdmin && !in_array(auth()->user()->id,
                                            $reviewersIds))
                                            disabled @endif @if($task->status == '0') selected @endif>
                                            Assigned
                                        </option>
                                        <option value="1" @if(!$isSuperAdmin && (!in_array(auth()->user()->id,
                                            $assigneesIds
                                            ))
                                            ) disabled @endif @if($task->status == '1') selected @endif>
                                            In Progress
                                        </option>
                                        <option value="2" @if(!$isSuperAdmin && ($task->status != '1' ||
                                            !in_array(auth()->user()->id, $assigneesIds))) disabled @endif
                                            @if($task->status
                                            ==
                                            '2') selected @endif>
                                            Assigned for Review
                                        </option>
                                        @if($isSuperAdmin || in_array(auth()->user()->id, $reviewersIds))
                                        <option value="3" @if(!$isSuperAdmin && (!in_array(auth()->user()->id,
                                            $reviewersIds)))
                                            disabled @endif @if($task->status == '3') selected @endif>
                                            Reviewing
                                        </option>
                                        <option value="4" @if(!$isSuperAdmin && (!in_array(auth()->user()->id,
                                            $reviewersIds)))
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
                @if ($activities->count())
                {{-- pinned start --}}
                @if($activities->where('isPinned')->count())
                <div
                    class="border border-primary dark:border-gray-600 rounded-lg bg-white dark:bg-neutral-800 transition-all">
                    <div id="view-pinned" class="cursor-pointer flex gap-x-4 items-end mb-2 justify-between px-5 "
                        onclick="toggleShowHidePinned()">
                        <h3 class="text-xl font-bold">Pinned:</h3>
                        <div
                            class="flex flex-row items-center mt-4 font-medium transition-all duration-200 ease-in-out cursor-pointer w-fit group text-darkblue dark:text-blue-300 hover:text-lightblue">
                            <svg class="h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                <path
                                    d="M246.6 470.6c-12.5 12.5-32.8 12.5-45.3 0l-160-160c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L224 402.7 361.4 265.4c12.5-12.5 32.8-12.5 45.3 0s12.5 32.8 0 45.3l-160 160zm160-352l-160 160c-12.5 12.5-32.8 12.5-45.3 0l-160-160c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L224 210.7 361.4 73.4c12.5-12.5 32.8-12.5 45.3 0s12.5 32.8 0 45.3z" />
                            </svg>
                        </div>
                    </div>
                    <div id="collapsePinned"
                        class="hidden border-t border-gray-300 dark:bg-gray-700 transition-all px-5">
                        <div class="py-5 rounded overflow-y-auto max-h-[80vh] pr-1">
                            <div class="flex flex-col space-y-4">
                                @foreach ($activities->where('isPinned') as $key => $comment)
                                @if (isset($comment->comments))
                                <div class="flex space-x-6">
                                    <div
                                        class="border border-gray-300 dark:border-gray-600 flex grow relative rounded-lg">
                                        <div class="w-full h-full overflow-hidden rounded-lg">
                                            <div
                                                class="flex justify-between items-center bg-gray-100 dark:bg-neutral-700 py-2 px-3">
                                                <span class="text-base dark:text-gray-300">
                                                    <strong>{{ $comment->user }} </strong>
                                                    <span class="text-xs text-gray-400">({{ $comment->pinnedBy }} pinned
                                                        a
                                                        comment)
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
                                                            xmlns:xlink="http://www.w3.org/1999/xlink"
                                                            aria-hidden="true" role="img"
                                                            class="w-3 h-3 iconify iconify--bi"
                                                            preserveAspectRatio="xMidYMid meet" viewBox="0 0 16 16">
                                                            <g fill="currentColor">
                                                                <path
                                                                    d="M.002 3a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-12a2 2 0 0 1-2-2V3zm1 9v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V9.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71l-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12zm5-6.5a1.5 1.5 0 1 0-3 0a1.5 1.5 0 0 0 3 0z">
                                                                </path>
                                                            </g>
                                                        </svg>
                                                        <span
                                                            class="max-w-[110px] text-ellipsis overflow-hidden whitespace-pre">{{
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
                        <div class="flex flex-col gap-y-6">
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
                                    class="border border-gray-300 dark:border-gray-600 w-20 flex grow relative rounded-lg">
                                    <div
                                        class="absolute border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-neutral-700 w-4 h-4 rotate-45 top-3 -left-2 z-0">
                                    </div>
                                    <div class="z-0 w-full h-full overflow-hidden rounded-lg">
                                        <div
                                            class="flex justify-between items-center bg-gray-100 dark:bg-neutral-700 py-2 px-3">
                                            <div class="flex gap-3">
                                                <span class="text-base dark:text-gray-300">
                                                    <strong>{{ $comment->user }} </strong>
                                                </span>
                                                {{-- pin button start --}}
                                                {{-- @if(!$comment->isPinned) --}}
                                                <a href="{{route('tasks.pinComment', $comment->id)}}">
                                                    <button onclick="pinModal({{$comment->id}})"
                                                        value="{{ $comment->id }}"
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
                                                <button
                                                    class="py-2 px-6 bg-blue-500 text-white rounded hover:bg-blue-700"
                                                    value="{{ $comment->id }}" id="reply_mode_on"
                                                    onclick="CommentReplyModal()">
                                                    Reply
                                                </button>
                                            </div>
                                        </div>
                                        <div class="px-3 py-2">
                                            <div
                                                class="dividerforcommentandreply  scrolling-touch overflow-x-auto scroll-none">
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
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                                                        role="img" class="w-3 h-3 iconify iconify--bi"
                                                        preserveAspectRatio="xMidYMid meet" viewBox="0 0 16 16">
                                                        <g fill="currentColor">
                                                            <path
                                                                d="M.002 3a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-12a2 2 0 0 1-2-2V3zm1 9v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V9.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71l-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12zm5-6.5a1.5 1.5 0 1 0-3 0a1.5 1.5 0 0 0 3 0z">
                                                            </path>
                                                        </g>
                                                    </svg>
                                                    <span
                                                        class="max-w-[110px] text-ellipsis overflow-hidden whitespace-pre">{{
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
                                                        {{
                                                        \Carbon\Carbon::parse($comment_reply->created_at)->format('m/d/Y
                                                        h:i
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

                                                <div
                                                    class="bg-white mb-1 px-3 py-2 scrolling-touch overflow-x-auto scroll-none">
                                                    <?php echo $result; ?>
                                                </div>
                                                @if ($comment_reply->getReplyImage->isNotEmpty())
                                                @foreach ($comment_reply->getReplyImage as $replied)
                                                <a href="{{ asset('storage/tasks/' . $replied->name) }}"
                                                    class="inline-flex items-center px-3 py-1 mb-2 space-x-2 text-base transition-all border rounded-full hover:bg-primary-100 text-primary-700 border-primary-700 dark:text-slate-300"
                                                    target="_blank">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                                                        role="img" class="w-3 h-3 iconify iconify--bi"
                                                        preserveAspectRatio="xMidYMid meet" viewBox="0 0 16 16">
                                                        <g fill="currentColor">
                                                            <path
                                                                d="M.002 3a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-12a2 2 0 0 1-2-2V3zm1 9v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V9.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71l-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12zm5-6.5a1.5 1.5 0 1 0-3 0a1.5 1.5 0 0 0 3 0z">
                                                            </path>
                                                        </g>
                                                    </svg>
                                                    <span
                                                        class="max-w-[110px] text-ellipsis overflow-hidden whitespace-pre">{{
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
                                <div
                                    class="border border-green-500 dark:border-gray-600 w-20 flex grow relative rounded-lg">
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
                                    class="border border-gray-300 dark:border-gray-600 w-20 flex grow relative rounded-lg">

                                    <div class="z-0 w-full h-full overflow-hidden rounded-lg">
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
                    <div
                        class="flex items-center justify-center min-height-100vh pt-4 px-4 pb-20 text-center sm:block sm:p-0">
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
    </div>


</div>



@push('vite')
    @vite(['resources/js/tiptap.js'])
@endpush

<!-- <script type="module" src="{!! Vite::asset('resources/js/tiptap.js') !!}" defer></script> -->
<script type="text/javascript" >
    function CommentReplyModal() {
        document.getElementById('commentReply').classList.toggle('hidden');
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Select2
        const multiselectElements = document.querySelectorAll('.multiselect');
        multiselectElements.forEach(element => {
            // Replace this line with the equivalent vanilla JS initialization of Select2, if applicable.
        });
    
        document.addEventListener('click', function (event) {
            if (event.target && event.target.id === 'reply_mode_on') {
                const data = event.target.value;
                document.getElementById('comment_id').value = data;
            }
            
            if (event.target && event.target.id === 'cancle_reply') {
                console.log('Empty reply field');
                document.getElementById('reply').value = '';
            }
    
            if (event.target && event.target.id === 'deployLogsModal') {
                const project_name = event.target.getAttribute('data-log-id');
                fetch(`/deploy/log_list?project_name=${project_name}`)
                    .then(response => response.json())
                    .then(response => {
                        const deploylogDetails = document.getElementById('deploylogDetails');
                        deploylogDetails.innerHTML = '';
                        response.forEach(item => {
                            const date = new Date(item.created_at);
                            const localDateString = date.toLocaleDateString();
                            const localTimeString = date.toLocaleTimeString();
                            const logsDetails = `
                                <tr>
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
                                        <p class="text-gray-600 whitespace-no-wrap">${localDateString + ' ' + localTimeString}</p>
                                    </td>
                                </tr>`;
                            deploylogDetails.insertAdjacentHTML('beforeend', logsDetails);
                        });
                    });
                console.log(project_name, 'Merry John');
            }
    
            if (event.target && event.target.id === 'refreshModal') {
                document.getElementById('vvv').classList.add('hidden');
            }
        });
    
        document.getElementById('deploy_stage_id').addEventListener('change', function () {
            const taskId = this.getAttribute('data-id');
            const stagging_name = this.value;
            fetch(`/deploy?taskInfo=${taskId}&stagging=${stagging_name}`)
                .then(response => response.json())
                .then(response => {
                    const listdata = response.stageStates;
                    const pipelineError = response.pipeline_error;
                    const statusSuccess = response.success;
                    const statusError = response.error;
    
                    if (statusSuccess) {
                        window.location.reload();
                    }
    
                    if (pipelineError) {
                        const errorMessage = document.getElementById('dispaly_error_message');
                        errorMessage.classList.remove('hidden');
                        errorMessage.classList.add('text-white', 'bg-red-500');
                        errorMessage.innerHTML = pipelineError;
                        setTimeout(() => {
                            errorMessage.classList.add('hidden');
                        }, 3000);
                    }
    
                    if (statusSuccess) {
                        const successMessage = document.getElementById('dispaly_success_message');
                        successMessage.classList.remove('hidden');
                        successMessage.innerHTML = statusSuccess;
                        setTimeout(() => {
                            successMessage.classList.add('hidden');
                        }, 3000);
                    }
    
                    if (statusError) {
                        const errorMessage = document.getElementById('dispaly_error_message');
                        errorMessage.classList.remove('hidden');
                        errorMessage.classList.add('text-white', 'bg-red-500');
                        errorMessage.innerHTML = statusError;
                        setTimeout(() => {
                            errorMessage.classList.add('hidden');
                        }, 3000);
                    }
    
                    const source_summary_detail = response.stageStates[0]?.actionStates ?? [];
                    source_summary_detail.forEach(summary => {
                        const summary_info = summary.latestExecution.summary;
                        const pipelineSummary = document.getElementById('hide_pipeline_summary');
                        pipelineSummary.classList.remove('hidden');
                        pipelineSummary.innerHTML = 'Summary : ' + summary_info;
                        pipelineSummary.classList.add('text-bold', 'font-size-lg');
                    });
    
                    if (response.pipeline_name) {
                        document.getElementById('pipeline_name').innerHTML = response.pipeline_name;
                    }
    
                    if (listdata) {
                        document.getElementById('deploy_stage_id').classList.add('hidden');
                        document.getElementById('refreshModal').classList.remove('hidden');
                        document.getElementById('hide_pipeline_name').classList.remove('hidden');
                        document.getElementById('pipelineTableData').innerHTML = '';
                        document.getElementById('pipelinetable').style.display = 'block';
    
                        listdata.forEach(item => {
                            item.actionStates.forEach(actionstate => {
                                if (actionstate.latestExecution.status === 'InProgress') {
                                    document.getElementById('deploy_data').style.display = 'block';
                                    document.getElementById('deploy_taskId').value = taskId;
                                    document.getElementById('deploy_projectName').value = taskId.project_name;
                                    document.getElementById('deploy_pipeline_name').value = response.pipeline_name;
                                    document.getElementById('deploy_stage_name').value = item.stageName;
                                    document.getElementById('deploy_action_name').value = actionstate.actionName;
                                    const pipeline_token = actionstate.latestExecution.token;
                                    if (pipeline_token) {
                                        document.getElementById('deploy_token').value = pipeline_token;
                                    } else {
                                        document.getElementById('deploy_token').value = '';
                                    }
                                }
    
                                const UtcDate = actionstate.latestExecution.lastStatusChange;
                                const localTimeString = new Date(UtcDate).toLocaleTimeString();
                                const localDateString = new Date(UtcDate).toLocaleDateString();
                                const tableData = `
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
                                            <p class="text-gray-600 whitespace-no-wrap">${actionstate.latestExecution.lastStatusChange ? localDateString + ' ' + localTimeString : null}</p>
                                      </td>
                                       <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <p class="text-gray-600 whitespace-no-wrap">${actionstate.entityUrl ?? null}</p>
                                      </td>
                                      <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <p class="text-gray-600 whitespace-no-wrap">
                                                  <a href="${actionstate.revisionUrl}" target="_blank"> ${actionstate.revisionUrl ?? null}</a></p>
                                      </td>
                                    </tr>
                                `;
                                document.getElementById('pipelineTableData').insertAdjacentHTML('beforeend', tableData);
                            });
                        });
                    }
                });
        });
    });

    function toggleShowHidePinned(){
        document.getElementById('collapsePinned').classList.toggle('hidden')
    }
    
</script>