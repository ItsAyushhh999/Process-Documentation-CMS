@extends('layouts.app')

@section('content')

<x-header heading="Project">
    {{-- check for admin --}}
    @if(auth()->user()->is_super_admin == 1)
    <x-button to="{{ route('projects.create') }}" class="float-right btn btn-success">Add New Projects</x-button>
    @endif
</x-header>

@if ($projects->count()>0)
<div class="grid lg:grid-cols-4 grid-cols-2 gap-5 gap-y-10">
    @foreach ($projects as $project )
    <div
        class="border p-4 bg-white rounded-lg flex flex-col justify-between dark:bg-neutral-800 dark:border-neutral-700">
        {{-- <div class="font-bold xl:col-span-11 col-span-12 dark:text-white">{{ $project->name }}</div> --}}
        <div class="flex flex-col">
            <div class="grid pb-8 gap-x-5 xl:grid-flow-col grid-flow-row items-center xl:gap-y-0 gap-y-2">
                <div class="xl:col-span-1 w-28 col-span-12"> <img src="{{url('/img/logo.png')}}" alt=""
                        class="object-cover dark:invert dark:mix-blend-plus-lighter dark:brightness-200"></div>
                <div class="font-bold xl:col-span-11 col-span-12 dark:text-white">{{ $project->name }}</div>

                <x-modal-view target="add_sub_projects" id="sub_project_modal">
                    <x-slot:header>
                        Sub Projects
                        </x-slot>
                        <div id="add_sub_projects">
                            <form method="post" action="{{route('subProjects')}}">
                                @csrf
                                <input type="hidden" name="project_id_for_sub_project" id="project_id_for_sub_project">
                                <div class="grid mb-5">
                                    <x-label for="name" required>Name:</x-label>
                                    <x-input type="text" name="name"  id="sub_project_id" autocomplete="off" placeholder="Sub project name">
                                    </x-input>
                                </div>

                                <div class="grid mb-5">
                                    <x-label for="description">Description:</x-label>
                                    <x-editor name="description" id="sub_Project_description" height='180px'>
                                    </x-editor>
                                </div>

                                <div class="grid mb-5">
                                    <x-label for="url">Url:</x-label>
                                    <x-input type="text" name="url" id="sub_project_url" autocomplete="off" placeholder="Sub project url">
                                    </x-input>
                                </div>

                                   <div class="grid mb-5">
                                   <x-label for="repository">Repository:</x-label>
                                   <x-input type="text" name="repository" id="repository" placeholder="repository name" :value="isset($project) ? $project->repository_name : ''">
                                   </x-input>
                                   </div>

                                <div class="grid mb-5" id="developmet_pipelines">
                                    <x-label for="pipelineID">Development PipeLine:</x-label>
                                    <x-input type="text" name="development_pipeline"
                                    id="development_pipeline_name" autocomplete="off" placeholder="Development pipeline name" />
                                </div>
                                <div class="grid mb-5" id="staging_pipelines">
                                    <x-label for="pipelineID">Staging PipeLine:</x-label>
                                    <x-input type="text" name="staging_pipeline" id="staging_pipeline_name" placeholder="Staging pipeline name" />
                                </div>
                                <div class="grid mb-5" id="production_Pipelines">
                                    <x-label for="pipelineID">Production PipeLine:</x-label>
                                    <x-input type="text" name="production_Pipeline" autocomplete="off" id="production_Pipeline_name"
                                        placeholder="Production pipeline name" />
                                </div>

                                <div class="grid gird-cols-3 mb-5">
                                    <div></div>
                                    <div class="text-center w-34">
                                        <x-button type="submit" class="mb-3 text-center" id="button_add_sub_project"
                                            data-modal="modal" data-target="#add_sub_projects">Create Sub Project
                                        </x-button>
                                    </div>
                                    <div></div>
                                </div>
                            </form>
                        </div>
                </x-modal-view>
            </div>
            <div class="pb-5 text-gray-500 dark:text-slate-300 justify-self-start flex truncate ">{!!
                $project->description !!}</div>
            <div class="pb-5 text-gray-500 dark:text-slate-300 justify-self-start flex truncate ">Staging PipeLine Name:
                {{ $project->staging_pipeline ?? '----'}}</div>
            <div class="pb-5 text-gray-500 dark:text-slate-300 justify-self-start flex truncate ">Development PipeLine
                Name: {{ $project->development_pipeline ?? '----' }}</div>
            <div class="pb-5 text-gray-500 dark:text-slate-300 justify-self-start flex truncate ">Production PipeLine
                Name: {{ $project->production_Pipeline ?? '----' }}</div>
        </div>

        <div class="flex items-center gap-x-2">
            <!-- <x-button to="{{ route('documents.index', $project->id) }}"
                class="btn btn-success btn-sm whitespace-nowrap">View Docs </x-button> -->

                <!-- project/1/document/1 -->
                <x-button to="project/1/document/1"
                    class="btn btn-success btn-sm whitespace-nowrap">View Docs </x-button> 
            <x-button to="{{ route('projects.edit', $project->id) }}" class="btn btn-success btn-sm whitespace-nowrap">
                Edit  </x-button>
            <a href="{{  $project->url }}" target="_blank"
                class="text-darkblue font-semibold dark:text-skyblue truncate">{{ $project->url }}</a>
        </div>

        @if(auth()->user()->is_super_admin == 1)
        <div class="flex items-center gap-x-2 mt-4">
            <div class="text-start">
                <x-button class="float-right addTaskType bg-cyan-400 py-3 border" data-modal="modal"
                    data-target="#add_sub_projects" id="display_sub_form" data-id="{{$project->id}}">Add Sub Project
                </x-button>
            </div>
            <div>
                <a class="float-right addTaskType btn btn-success bg-cyan-400 rounded py-3 px-3 text-white shadow"
                    href="{{ route('view.sub-projects',$project->id) }}" id="view_sub_projects">View Sub Projects</a>
            </div>
        </div>
        @endif
    </div>
    @endforeach

    @else
    <p class='text-center'>No projects.</p>
    @endif

    <script type="text/javascript">
        $(document).ready(function() {
        $(document).on('click', '#display_sub_form', function() {
            let project_id = $(this).data('id');
            console.log('merry john',project_id);

            $('#project_id_for_sub_project').val(project_id);
    });
});
    </script>
    @endsection
