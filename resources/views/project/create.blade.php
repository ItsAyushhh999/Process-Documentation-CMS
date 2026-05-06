@extends('layouts.app')

@section('content')
<div class="p-5 border border-primary rounded w-1/3 dark:border-gray-700 bg-white dark:bg-neutral-800">
    <h3 class="text-2xl font-bold mb-5"> {{ isset($project) ? 'Edit Project' : 'Add Project' }}</h3>
    <form action=" {{ isset($project) ? route('projects.update',$project->id) : route('projects.store') }}" method="post" id="projectForm">
        @csrf
        @if (isset($project))
        @method('PUT')
        @endif
        <div class="grid mb-5">
            <x-label for="name" required>Name:</x-label>
            <x-input type="text" id="name" name="name" placeholder="Project name" :value="isset($project) ? $project->name : ''" required />
        </div>

        <div class="grid mb-5">
            <x-label for="description">Description:</x-label>
            <x-editor id="description" name="description" :value="isset($project) ? $project->description : ''" >
            </x-editor>
        </div>

        <div class="grid mb-5">
            <x-label for="url" >Url:</x-label>
            <x-input type="url" id="url" name="url"  placeholder="Project url" :value="isset($project) ? $project->url : ''"  />
        </div>


        @if(isset($project))
        @if($project->sub_projects)
        <div class="grid mb-5">
            <x-label for="repository">Repository:</x-label>
            <x-input type="text" name="repository" id="repository" placeholder="Repository name" :value="isset($project) ? $project->repository_name : ''">
            </x-input>
        </div>
        @endif
        @endif

        <div class="grid mb-5 " id="developmet">
            <x-label for="pipelineID">Development PipeLine:</x-label>
            <x-input type="text"  name="development_pipeline" placeholder="Development pipeline name"  :value="isset($project) ? $project->development_pipeline : ''" />
        </div>
        <div class="grid mb-5" id="staging">
            <x-label for="pipelineID">Staging PipeLine:</x-label>
            <x-input type="text" name="staging_pipeline" placeholder="Staging pipeline name" class="hide" :value="isset($project) ? $project->staging_pipeline : ''"   />
        </div>
        <div class="grid mb-5" id="production">
            <x-label for="pipelineID">Production PipeLine:</x-label>
            <x-input type="text" name="production_Pipeline" placeholder="Production pipeline name"  class="hide"  :value="isset($project) ? $project->production_Pipeline : ''"  />
        </div>

        <div class="grid mb-5">
            <x-button type="submit">
                {{ isset($project) ? 'Update' : 'Add'  }}
            </x-button>
        </div>

    </form>
</div>
     <script type="text/javascript">

        $(document).ready(function() {
            //Select branch for storing Pipeline Name
            // $('#project_branch').on('change',function() {

            //     let branch = $(this).val();

            //     switch (branch) {
            //         case "stagging" :
            //             $('#staging_pipeline').removeClass('hidden');
            //             $('#production_Pipeline').addClass('hidden');
            //             $('#developmet_pipeline').addClass('hidden');
            //             break;
            //         case "production" :
            //             $('#production_Pipeline').removeClass('hidden');
            //             $('#staging_pipeline').addClass('hidden');
            //             $('#developmet_pipeline').addClass('hidden');
            //             break;
            //         case "development" :
            //             $('#developmet_pipeline').removeClass('hidden');
            //             $('#production_Pipeline').addClass('hidden');
            //             $('#staging_pipeline').addClass('hidden');
            //             break;
            //         default :
            //             $('#staging_pipeline').addClass('hidden');
            //             $('#developmet_pipeline').addClass('hidden');
            //             $('#production_Pipeline').addClass('hidden');
            //             break;
            //     }
            // });
        });
    </script>
@endsection
