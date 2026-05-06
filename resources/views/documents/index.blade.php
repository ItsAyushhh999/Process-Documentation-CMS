@extends('layouts.app')

@section('content')

<div class="flex -my-4 -mx-4">
    <div class="xl:ml-40 ml-0 md:block hidden">
        <app-side-bar :catagories="{{$categories}}" :documents="{{$documents}}" > </app-side-bar>
    </div>
    <div class="relative bg-white flex-grow overflow-auto min-h-screen" >
        <div class="absolute -translate-x-full transition-transform" id="docSideBar">
            <div class="flex items-start">
                <app-side-bar :catagories="{{$categories}}" :documents="{{$documents}}" > </app-side-bar>
                <span class="toggleSideBar border ">&times;</span>
            </div>
        </div>
       
        <!-- <div class=" dark:bg-neutral-800 dark:text-gray-400 pl-10 pr-8 py-4" id="contnet"> -->
        <form  class="flex-1" method="POST">
            @csrf
            <div class="flex justify-end gap-x-3" >
                <span class="text-blue-500 flex gap-x-2 cursor-pointer items-center px-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    Edit This Page
                </span>
                <x-button outline id="htmlToPdf" class="print"> 
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Save as PDF
                </x-button>
                <x-button  class=" btn btn-success float-right">Add New Docs</x-button>
            </div>
        </form>
        <button class="toggleSideBar">Show side Bar</button>
        </div>
    </div>
</div>

<script>
    $(function(){
        console.log("location", window.location.pathname)
        var location = window.location.pathname
        var temp = location.split('/document/')
        console.log("temp", temp)
        $('.print').on('click', function(){
            console.log("print")
            // $('#printable').print();
            var divToPrint = $('#printArea');
            newWindow = window.open("");
            newWindow.document.write(divToPrint.outerHTML);
            newWindow.print();
        })
    })

    $(function() {
        var sideBar = $('#docSideBar')
        console.log("sidebar", sideBar)
        // sideBar.addClass("")

        $('.toggleSideBar').on('click', function(){
            console.log("toggled")
            if(sideBar.hasClass("-translate-x-full")){
                sideBar.removeClass("-translate-x-full")
            }else{
                sideBar.addClass("-translate-x-full")
            }
        })

    })

    // $(document).on('click', function (e) {
    // console.log("e.targer", e.target)
    // if (e.target != undefined && e.target.classList.contains('trigger_model')) {
    //     var modal = $('.modal')
    //     modal.addClass('opacity-0 scale-90')
    //     $(e.target).addClass('hidden')
    // }
    // });

</script>
@endsection