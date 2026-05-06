@extends('layouts.app')

@section('content')

    <x-header heading="Project">
        {{-- check for admin --}}
       
    </x-header>
    @if ($sub_projects->count()>0)
        <div class="grid lg:grid-cols-4 grid-cols-2 gap-5 gap-y-10">
            @foreach ( $sub_projects as $sub_project )

                <div class="border p-4 bg-white rounded-lg flex flex-col justify-between dark:bg-neutral-800 dark:border-neutral-700">
                    <div class="xl:col-span-11 col-span-12 dark:text-white">Sub-Project Title: <span class="font-bold"> {{ $sub_project->name }}</span></div>
                    <div class="xl:col-span-11 col-span-12 dark:text-white mt-3 mb-2 bg-white pb-3 rounded shadow px-4">
                        <p class="mt-3">Description: </p>
                        <div>{!! $sub_project->description !!} </div>
                    </div>
                    <div class="flex items-center gap-x-2">
                        <div class="xl:col-span-11 col-span-12 dark:text-white mb-2"><span class="font-bold">Created Date: </span>
                            <div>
                            {{ $sub_project->created_at->format('Y/m/d H:i A') }}
                            <x-chip color='sky'>
                                <?php
                                    if($sub_project->createdBy) {
                                        $firstName = explode(' ', $sub_project->createdBy->name);
                                        echo "$firstName[0]." . substr($firstName[1], 0, 1);
                                    }else {
                                        echo "N/A";
                                    }
                                ?>
                            </x-chip>
                            </div>
                        </div>
                        <div class="xl:col-span-11 col-span-12 dark:text-white mb-2">
                            <span class="font-bold">Updated Date: </span>
                            <div>
                            {{ $sub_project->created_at->format('Y/m/d H:i A') }}
                            <x-chip color='sky'>
                                <?php
                                    if ($sub_project->updatedBy) {
                                        $firstName = explode(' ', $sub_project->updatedBy->name);
                                        echo "$firstName[0]." . substr($firstName[1], 0, 1);
                                    } else {
                                        echo "N/A";
                                    }
                                ?>
                            </x-chip>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-x-2">
                        
                    </div>
                    {{-- <div class="pb-5 text-gray-500 dark:text-slate-300 justify-self-start flex truncate ">Staging PipeLine Name: {{ $project->staging_pipeline ?? '----'}}</div><br>
                    <div class="pb-5 text-gray-500 dark:text-slate-300 justify-self-start flex truncate ">Development PipeLine Name: {{ $project->development_pipeline ?? '----' }}</div><br>
                    <div class="pb-5 text-gray-500 dark:text-slate-300 justify-self-start flex truncate ">Production PipeLine Name: {{ $project->production_Pipeline ?? '----' }}</div> --}}
                    <div class="flex items-center gap-x-2">
                        <x-button to="{{ route('documents.index', $sub_project->id) }}" class="btn btn-success btn-sm whitespace-nowrap">View Docs </x-button>
                        <x-button to="{{ route('projects.edit', $sub_project->id) }}" class="btn btn-success btn-sm whitespace-nowrap">Edit </x-button>
                        <a href="{{  $sub_project->url }}" target="_blank" class="text-darkblue font-semibold dark:text-skyblue truncate">{{ $sub_project->url }}</a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class='text-center'>Project does not have any sub project recently</p>
    @endif

    <script type="text/javascript">

    </script>
@endsection
