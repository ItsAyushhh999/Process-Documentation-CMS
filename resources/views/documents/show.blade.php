@extends('layouts.app')

@section('content')
<div class="fixed top-0 w-1/2 right-0 bottom-0 bg-white z-0 dark:bg-neutral-900"></div>
<div class="flex justify-center -my-4 -mx-4 ">

    @if($document)
    <div id="root" class="container flex">

        <div class="lg:block hidden  border-r dark:border-gray-700">
            <div class="sticky top-0">
                <div class="overflow-auto max-h-screen min-h-[calc(100vh-70px)]">
                    <app-side-bar :catagories="{{$categories}}" name="{{$projects->where('id',$project_id)->first()->name}}" url=" {{$projects->where('id',$project_id)->first()->url}}" :projects="{{$projects}}" :projectid={{$project_id}} :isadmin="{{$isPermitted ? 'true' : 'false'}}"> </app-side-bar>
                </div>
            </div>
        </div>

        <div class="relative bg-white dark:bg-neutral-800 flex-1 min-h-[calc(100vh-70px)]">
            <!-- <div class="absolute -translate-x-full transition-transform stickey top-0" id="docSideBar">
                <div class="flex items-start lg:hidden">
                    <div class="bg-slate-50 border shadow-lg">
                        <app-side-bar :catagories="{{$categories}}" name="{{$projects->where('id',$project_id)->first()->name}}" url=" {{$projects->where('id',$project_id)->first()->url}}" :projects="{{$projects}}" :projectid={{$project_id}}> </app-side-bar>
                    </div>
                    <span class="toggleSideBar  p-3 mt-2 bg-white rounded-r-md border-r border-t border-b shadow-sm text-red-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>

                    </span>
                </div>
            </div> -->
            <div class=" dark:bg-neutral-900 dark:text-gray-400 " id="contnet">
                <div class="flex items-center sticky top-0 bg-white dark:bg-stone-900 bg-opacity-80 backdrop-filter backdrop-blur-lg">
                    <!-- <span class="toggleSideBar lg:hidden flex text-blue-500 underline items-start justify-center">
                        More Documents
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 4.5l7.5 7.5-7.5 7.5m-6-15l7.5 7.5-7.5 7.5" />
                        </svg>

                    </span> -->

                    <!-- <x-input placeholder="seerch anything"></x-input> -->
                    <div class="flex-1 px-4 py-4 border-b dark:border-gray-700">
                        <div class="flex justify-end gap-x-3">
                            @if($isPermitted)
                            <a class="text-blue-500 flex gap-x-2 cursor-pointer items-center px-4" 
                            href="/document/{{$document->id}}/edit"
                            >
                            <!-- href="{{ route('document.edit', $document->id) }}" -->
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                                Edit This Page
                            </a>
                            @endif
                            <x-button outline id="htmlToPdf" class="htmlToPdf">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                                Save as PDF
                            </x-button>
                            @if($isPermitted)
                            <x-button class=" btn btn-success float-right" to="/document/create/{{$project_id}}">Add New Docs</x-button>
                            @endif

                        </div>
                    </div>

                </div>

                <div class="flex justify-center pt-8 px-5 overflow-auto" id="printArea">
                    <div class="prose prose-lg ql-editor w-[750px] dark:text-gray-300 prose-strong:dark:text-gray-300 description">
                        {!! $document->description !!}
                    </div>
                </div>
            </div>
        </div>

    </div>
    @else
    <div class="relative flex justify-center items-center w-full h-[calc(100vh-70px)] z-10 bg-gray-50">
        <div class="my-20 border rounded bg-white dark:bg-neutral-800 p-10 flex flex-col items-center justify-center">
            <span class="text-[24px] font-semibold mb-10">Project Id: {{$project_id}} doesn't contain documents</span>
            @if($isPermitted)
            <x-button class=" btn btn-success float-right" to=" {{ route('document.create', $project_id) }}">Add New Docsss</x-button>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection