@php
$approve = false;
$pipeline_token = null;
$action_name = null;
$deploy_stage_name = null;
@endphp

<div class="flex justify-end m-5 mb-2 mr-0">
    <x-button id="refreshDetail" type="submit" class="requestDeploy">
        Refresh
    </x-button>
</div>

<div class="max-w-[90vw] mb-5">
    <div class="overflow-x-auto">
        <table class="min-w-full max-w-[80vw] border border-gray-200 divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Stage Name
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Action Name
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Summary
                    </th>
                    <th class="py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        Changes
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Entity Url
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Revision Url
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Updated Date
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($stageStates as $item)
                @foreach($item['actionStates'] as $actionstate)
                @php
                if(data_get($actionstate, 'latestExecution.status') == 'InProgress')
                {
                $pipeline_token = data_get($actionstate, 'latestExecution.token');
                $deploy_stage_name = data_get($item, 'stageName');
                $action_name = data_get($actionstate, 'actionName');
                $approve = $pipeline_token ? true : false;
                }
                @endphp
                <tr>
                    <td class="px-6 py-4 whitespace-normal max-w-20">
                        <p class="text-sm text-gray-900 capitalize">{{data_get($item,'stageName')}}</p>
                    </td>
                    <td class="px-6 py-4 whitespace-normal max-w-20">
                        <p class="text-sm text-gray-900 capitalize">{{data_get($actionstate, 'actionName')}}</p>
                    </td>
                    <td class="px-6 py-4 whitespace-normal max-w-20">
                        <span class="text-sm text-gray-900">{{data_get($actionstate, 'latestExecution.status')}}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-normal">
                        <p class="text-sm text-gray-900">{{data_get($actionstate, 'latestExecution.summary')}}</p>
                    </td>
                    <td>
                        @php
                            $url = data_get($actionstate, 'revisionUrl', '');
                            $request = Request::create($url);
                            $commit = $request->query('Commit');
                            $repositoryId = $request->query('FullRepositoryId');
                        @endphp
                            <div class="mb-2">
                                @if($repositoryId)
                                <x-chip color="blue">{{$repositoryId}}</x-chip>
                                @endif
                            </div>
                        <div class="text-center">
                            @if($commit)
                            <x-chip color="green">{{Str::substr($commit, 0, 10)}}</x-chip>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-normal max-w-20">
                        {{-- <p class="text-sm text-gray-900">{{data_get($actionstate, 'entityUrl')}}</p> --}}
                        <button title="Copy Entity Url"
                            onclick="copyFunction('{{data_get($actionstate, 'entityUrl')}}')">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                            </svg>

                        </button>
                    </td>
                    <td class="px-6 py-4 whitespace-normal max-w-[20%]">

                        <button title="Copy Revision Url"
                            onclick="copyFunction('{{data_get($actionstate, 'revisionUrl')}}')">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                            </svg>

                        </button>
                        {{-- <a href="{{data_get($actionstate, 'revisionUrl')}}" target="_blank"
                            class="text-blue-500 hover:underline">{{data_get($actionstate,
                            'revisionUrl' )}}
                        </a> --}}
                    </td>
                    <td class="px-6 py-4 whitespace-normal max-w-20">
                        <p class="text-sm text-gray-900">
                            @php
                            $timeString = data_get($actionstate, 'latestExecution.lastStatusChange');
                            $time = $timeString ? \Carbon\Carbon::parse($timeString)
                            ->setTimezone('Asia/Kathmandu')
                            ->format('d/m/Y h:i A') : '-';
                            echo($time);
                            @endphp
                        </p>
                    </td>
                </tr>
                @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@if($approve)
<div class="bg-gray-50 border-gray-300 border rounded-md mb-5">
    <div class="rounded mb-3 px-3 py-3 max-w-[480px]">
        <form action="{{ route('deploy') }}" method="post">
            @method('post')
            @csrf
            <x-input id="deployProjectId" value="{{$ParentprojectId}}" type="text" name="deployProjectId" hidden />
            <x-input id="deploy_token" value="{{$pipeline_token}}" type="text" name="deploy_token" hidden />
            <x-input id="deploy_taskId" value="{{$taskId}}" type="text" name="deploy_taskId" hidden />
            <x-input id="deploy_projectName" value="{{$task_details}}" type="text" name="deploy_projectName" hidden />
            <x-input id="project_id" value="{{$projectId}}" type="text" name="project_id" hidden />
            <x-input id="deploy_pipeline_name" value="{{$pipeline_name}}" type="text" name="deploy_pipeline_name"
                hidden />
            <x-input id="deploy_stage_name" value="{{$deploy_stage_name}}" type="text" name="deploy_stage_name"
                hidden />
            <x-input id="deploy_stage_name" value="{{$stage_name}}" type="text" name="stage_name"
                     hidden />
            <x-input id="deploy_action_name" value="{{$action_name}}" type="text" name="deploy_action_name" hidden />

            <span>Approve or Reject the Deployment Changes</span>
            <div class="flex items-center gap-5">
                <div class="grid w-[40rem] grid-cols-2 gap-2 rounded-xl bg-gray-200 p-2">
                    <div>
                        <input type="radio" name="deploy" id="approved" value="Approved" class="peer hidden" required/>
                        <label for="approved"
                            class="border border-blue-300 block cursor-pointer select-none rounded-xl p-2 text-center peer-checked:bg-blue-500 peer-checked:font-bold hover:bg-blue-500 peer-checked:text-white hover:text-white ">Approve</label>
                    </div>
                    <div>
                        <input type="radio" name="deploy" id="rejected" value="Rejected" class="peer hidden"/>
                        <label for="rejected"
                            class="border border-red-300 block cursor-pointer select-none rounded-xl p-2 text-center peer-checked:bg-red-500 hover:bg-red-500 peer-checked:font-bold peer-checked:text-white hover:text-white ">
                            <button type="button" id="2" {{-- onclick="removeChildren()" --}}>
                                Reject</button></label>
                    </div>
                </div>
                <x-button type="submit" class="mt-4 text-end mb-3">Deploy</x-button>
            </div>
        </form>
    </div>
</div>
@endif


<script>
    // copy function
    function copyFunction(url) {
        navigator.clipboard.writeText(`${url}`);

        html = `<div class="fade -translate-x-1/2 fixed z-50 w-full max-w-sm p-3 transition-all transform bg-green-700 rounded-lg shadow-xl bounce left-1/2 top-3"
            role="alert" x-data="{alertOpen: true}" x-show="alertOpen">
            <strong class="text-white">URL copied.</strong>
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

            $('.fade').fadeOut(4000);
    }

    // refresh button
    $('#refreshDetail').on('click', function(event) {
        $(this).html("Processing...");
        $('.requestDeploy').attr("disabled", "");
        event.preventDefault();

        var formData = $('#requestDeployForm').serialize()

        $('#requestDetailPreview').html(`
            <div class="flex items-center justify-center w-[76vw] h-[50vh]">
                <div class="relative">
                    <div class="h-24 w-24 rounded-full border-t-8 border-b-8 border-gray-200"></div>
                    <div class="absolute top-0 left-0 h-24 w-24 rounded-full border-t-8 border-b-8 border-blue-500 animate-spin">
                    </div>
                </div>
            </div>`
        )

        $.ajax({
                type: 'get',
                url: '/deploy',
                data: formData,
                success: function(response) {
                    $('#requestDetailPreview').html(response);
                    $('#submitButtonId').prop('disabled', false);
                    $('#submitButtonId').html('Request');
                    },
                error: function(xhr, status, error) {

                    }
                }
            );
    });
</script>
