@extends('layouts.app')

@section('content')
@if($isPermitted)

<div class="fixed top-0 left-0 bottom-0 right-0 z-0">
    <div class="w-1/2 bg-white dark:bg-neutral-900 h-screen"></div>
    <div class="w-1/2 bg-gray-50 dark:bg-neutral-900 h-screen"></div>
</div>
<div class="-mt-4 -mx-4 dark:bg-neutral-900 min-h-[calc(100vh-70px)] -mb-4">
    <!-- <h3 class="text-xl font-bold p-5">Edit Documents</h3> -->
    
    <form action=" {{ isset($document) ? route('document.update',$document->id) : ''}}" method="post" id="documentForm">
        @csrf

        @if (isset($document))
        @method('PUT')
        @endif
        {{-- category section --}}
        <div class="documents_quell relative flex justify-center h-[calc(100vh-70px)]">
            <div class=" w-[900px] bg-white dark:bg-stone-800">
                <!-- <input type="hidden" name="description" />
                <div id="editor-container" class="min-h-[90vh] dark:text-gray-200 rounded-b text-base dark:bg-stone-800">
                    {!!isset($document) ? $document->description : ''!!}
                </div> -->
                <!-- <div id="stQuote">
                    <span>quote</span>
                <select class="stQuote-color">
                    <option value="red">red</option>
                    <option value="green">green</option>
                    <option value="orange">orange</option>
                </select>
                </div> -->

                <!-- <span>{{$document->description}}</span> -->
           

                <textarea name="description" data-quilljs placeholder="Please enter text" class=" dark:text-gray-200 rounded-b text-xl dark:bg-stone-800"> {!!isset($document) ? $document->description : ''!!}</textarea>
                
            </div>

            <div class=" xl:w-[360px] lg:w-[300px] rounded dark:bg-neutral-900 z-10">
                <div class="sticky top-0 flex flex-col gap-y-3 p-5">
                    <h3 class="text-xl font-bold mb-4 dark:text-gray-300">Edit Documents</h3>
                    <div class="grid">
                        <x-label for="name">Title:</x-label>
                        <x-input type="text" id="name" class="form-control" name="name" value=" {{ isset($document) ? $document->name : '' }}" required />
                    </div>
                    <div class=" grid mb-3">
                        <x-label for="category">Categories:</x-label>
                        <select class=" ml-4 w-full border-gray-300 rounded-md shadow-sm form-select focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 multiselect " name="categories[]" multiple required>
                            @foreach($categories as $category)
                            <option value="{{$category->id}}" <?php if (in_array($category->id, $documentCategory)) echo 'selected';  ?>>{{$category->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid">
                        <x-button type="submit" class="py-2">
                            Update
                        </x-button>

                    </div>
                </div>
            </div>
        </div>


    </form>
</div>
@else
You don't have permission to access to this page!
@endif

<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>


<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">



</script>
<script type="text/javascript">
    $(document).ready(function() {
        var max_fields = 2; //maximum input boxes allowed
        var wrapper = $(".input_fields_wrap"); //Fields wrapper
        var add_button = $(".add_field_button"); //Add button ID

        $(wrapper).on("click", ".remove_field", function(e) { //user click on remove text
            e.preventDefault();
            $(this).parents('tr').remove();
        })
        $('.multiselect').select2();

        $('.dummyclick').click(function() {
            console.log("clcked", input)
        })

        // var quill = new Quill('#editor', {
        //     modules: {
        //         toolbar: [
        //             ['bold', 'italic'],
        //             ['link', 'blockquote', 'code-block', 'image'],
        //             [{
        //                 list: 'ordered'
        //             }, {
        //                 list: 'bullet'
        //             }]
        //         ]
        //     },
        //     placeholder: 'Compose an epic...',
        //     theme: 'snow'
        // });
        // const forms = document.querySelector("#documentForm");
        // forms.onsubmit = function(e) {
        //     const input = document.querySelector("input[name=description]");
        //     input.value = quill.root.innerHTML;
        // }

    });
</script>

@endsection