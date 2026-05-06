@extends('layouts.app')

@section('content')
<div class="fixed top-0 w-1/2 right-0 bottom-0 bg-white z-0 dark:bg-neutral-900"></div>
<div class="flex justify-center -my-4 -mx-4 ">

    <div id="root" class="container flex">

        <div class="lg:block hidden  border-r dark:border-gray-700">
            <div class="sticky top-0">
                <div class="overflow-auto max-h-screen min-h-[calc(100vh-70px)]">
                <app-side-bar :catagories="{{$categories}}" name="{{$projects->where('id',$project_id)->first()->name}}" url=" {{$projects->where('id',$project_id)->first()->url}}" :projects="{{$projects}}" :projectid={{$project_id}} :isadmin="{{$isPermitted ? 'true' : 'false'}}"> </app-side-bar>
                </div>
            </div>
        </div>


        <div class="relative bg-white dark:bg-neutral-800 flex-1 min-h-[calc(100vh-70px)]">
            <div class="flex justify-center pt-8 px-5 overflow-auto" id="printArea">
                <div class="prose prose-lg ql-editor w-[750px] dark:text-gray-300 prose-strong:dark:text-gray-300">
                    {!! $description !!}
                </div>
            </div>
        </div>

    </div>

</div>
@endsection