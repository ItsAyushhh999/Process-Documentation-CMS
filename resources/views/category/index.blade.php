@extends('layouts.app')

@section('content')
<x-header heading="Categories">
{{-- check for admin --}}
    @if(auth()->user()->is_super_admin == 1)
    <x-button to="{{ route('categories.create') }}" class="float-right btn btn-success">Add Category</x-button>
    @endif
</x-header>

 @if ($categories->count()>0) 
 <div class="py-5 bg-white border dark:border-gray-700 rounded-lg dark:bg-neutral-800">
    <table class="w-full text-base cTable display nowrap " >
            <thead >
                <th>S.N</th>
                <th>Name</th>
                <th>Description</th>
                <th>Projects</th>
                @if(auth()->user()->is_super_admin == 1)
                <th data-orderable="false">Action</th>
                @endif
            </thead>
            <tbody>
                @foreach ( $categories as $category )
                    <tr>
                        <td>{{  $category->id }}</td>
                        <td>{{  $category->name }}</td>
                        <td>{{  $category->description }}</td>
                        <td>
                            @foreach ($category->project as $project )
                            <span class="whitespace-nowrap">
                                {{  $project->name }}
                            </span></br>
                            @endforeach 
                        </td>
                        @if(auth()->user()->is_super_admin == 1)
                        <td>
                            <x-icon-button to="{{ route('categories.edit', $category->id) }}" color="blue" />
                        </td>  
                        @endif             
                    </tr>
            @endforeach
        </tbody>
        </table>
    </div>
    @else
  <p class='text-center'>No categories.</p>
 @endif

@endsection