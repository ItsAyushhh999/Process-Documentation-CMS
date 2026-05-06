@extends('layouts.app')

@section('content')
<div class="p-5 bg-white border rounded border-primary lg:w-1/3 dark:border-gray-700 dark:bg-neutral-800">
    <form action=" {{ isset($category) ? route('categories.update',$category->id) :   route('categories.store') }}" method="post" enctype="multipart/form-data">
        @csrf

        @if (isset($category))
        @method('PUT')
        @endif

        <div class="grid mb-5">
            <x-label for="name" required>Name:</x-label>
            <x-input type="text" id="name" name="name" value=" {{ isset($category) ? $category->name : old('name') }}" required />
        </div>
        <div class="grid mb-5">
            <x-label for="description" required>Description:</x-label>
            <x-textarea name="description" required>{{ isset($category) ? $category->description : old('description')  }}</x-textarea>
        </div>
        {{-- Project section --}}
        <div class="grid mb-5" style='margin-top: 10px;'>
            <x-label for="projects" required>Projects:</x-label>
            <select name="projects[]" class="multiselect" multiple="multiple" required>

                @if (isset($category))
                @foreach($projects as $project)
                {{-- @if (in_array($project->id,$projectCategory)) --}}
                @if($projectCategory->contains($project->id))
                <option value="{{$project->id}}" selected>{{$project->name}}</option>
                @else
                <option value="{{$project->id}}">{{$project->name}}</option>
                @endif
                @endforeach
                @else
                @foreach($projects as $project)
                <option value="{{$project->id}}">{{$project->name}}</option>
                @endforeach
                @endif

            </select>
        </div>
        <!-- <div  class="grid mb-5">

            <x-input type="file" id="logo" name="logo" accept="image/jpeg,image/png,image/jpg" />
        </div> -->
        <x-label for="logo">Logo:</x-label>
        <div class="flex justify-between p-8 space-x-5">
            <div class="flex-grow">
                <x-input type="file" id="logo" name="logo" accept="image/jpeg,image/png,image/jpg" />
            </div>
            @if(isset($category))
            <div class="w-32">
                <div class="overflow-hidden aspect-w-1 aspect-h-1">
                    <img src="{{asset('storage/category/logo/'.$category->logo)}}" />
                </div>
            </div>
            @endif
        </div>
        <div class="grid mb-5">
            @if(isset($category))
            <x-button type="submit">
                Update
            </x-button>
            @else
            <x-button type="submit">
                Add
            </x-button>
            @endif
        </div>
    </form>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<script type="text/javascript">
    $(document).ready(function() {
        var max_fields = 10; //maximum input boxes allowed
        var wrapper = $(".input_fields_wrap"); //Fields wrapper
        var add_button = $(".add_field_button"); //Add button ID

        $(wrapper).on("click", ".remove_field", function(e) { //user click on remove text
            e.preventDefault();
            $(this).parents('tr').remove();
        })
        $('.multiselect').select2();

    });
</script>

@endsection