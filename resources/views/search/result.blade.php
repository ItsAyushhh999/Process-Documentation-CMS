@extends('layouts.app')

@section('content')
<x-header heading="Search Result By: {{ ucfirst($type) }}"></x-header>
@if ($results->count() > 0)
<div class="py-5 bg-white border rounded-lg dark:border-gray-700 dark:bg-neutral-800">
    <p class="p-3" >Listed below are the @if($type == 'projects') tasks @else documents @endif that match your query.</p>
    <table class="w-full text-base cTable display nowrap ">
        <thead>
            <th class='max-w-[50px]'>S.N</th>
            <th>Title</th>
            <!-- <th>Description</th> -->
            @if( $type == 'projects')
            <th>Project</th>
            <th>Status</th>
            {{-- <th>Type</th> --}}
            <th>Priority</th>
            {{-- <th>Assigned By</th> --}}
            @endif
        </thead>

        <tbody>
            @foreach ( $results as $result )
            <tr>
                <td>
                    <!-- <a href="{{  $type=='projects' ? route('tasks.edit', $result->id) : route('document.index', $result->id)}}" class="font-semibold text-blue-500">
                        {{ $result->id }}
                    </a> -->
                    <a  class="font-semibold text-blue-500" href="{{  $type=='projects' ? route('tasks.edit', $result->id) : route('documents.show', [$result->project_id,$result->id]) }}">
                    {{ $result->id }}
                    </a>
                </td>
                <td>{{ $type=='projects' ? $result->title : $result->name }} </td>
                <!-- <td>{!! substr($result->description, 0, 100) !!}...</td> -->
                <!-- <td>{!! $result->description !!}...</td> -->
                @if( $type == 'projects')
                <td>{{ $result->project?->name }}</td>
                <td>
                    @if($result->status == '0')
                    <span class="px-3 py-1 text-sm font-semibold text-red-800 bg-red-200 rounded-full">
                        Assigned
                    </span>
                    @elseif($result->status == '1')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full text-violet-900 bg-violet-200">
                        In Progress
                    </span>
                    @elseif($result->status == '2')
                    <span class="px-3 py-1 text-sm font-semibold text-yellow-900 bg-yellow-200 rounded-full">
                        Assigned for Review
                    </span>
                    @elseif($result->status == '3')
                    <span class="px-3 py-1 text-sm font-semibold text-blue-900 bg-blue-200 rounded-full">
                        Reviewing
                    </span>
                    @elseif($result->status == '4')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full text-cyan-900 bg-cyan-200">
                        Reviewed
                    </span>
                    @elseif($result->status == '5')
                    <span class="px-3 py-1 text-sm font-semibold text-green-900 bg-green-200 rounded-full">
                        Completed
                    </span class="px-3 py-1 text-sm font-semibold text-green-900 bg-green-200 rounded-full">
                    @elseif($result->status == '6')
                    <span>
                        Closed
                    </span>
                    @elseif($result->status == '7')
                    <span class="px-3 py-1 text-sm font-semibold text-orange-900 bg-orange-200 rounded-full">
                        Created
                    </span>
                    @elseif($result->status == '8')
                    <span class="px-3 py-1 text-sm font-semibold text-purple-900 bg-purple-100 rounded-full whitespace-nowrap">
                        Staging - Ready to Upload
                    </span>
                    @elseif($result->status == '9')
                    <span class="px-3 py-1 text-sm font-semibold text-purple-900 bg-purple-300 rounded-full whitespace-nowrap">
                        Staging - Uploaded
                    </span>
                    @elseif($result->status == '10')
                    <span class="px-3 py-1 text-sm font-semibold text-teal-900 bg-teal-200 rounded-full whitespace-nowrap">
                        Live - Ready to Upload
                    </span>
                    @elseif($result->status == '11')
                    <span class="px-3 py-1 text-sm font-semibold text-teal-900 bg-teal-400 rounded-full whitespace-nowrap">
                        Live - Uploaded
                    </span>
                    @endif
                </td>
                <td>
                    @if($result->priority == '0')
                    <span class="px-3 py-1 text-sm font-semibold text-green-900 bg-green-200 rounded-full">
                        Normal
                    </span>
                    @elseif($result->priority == '1')
                    <span class="px-3 py-1 text-sm font-semibold text-yellow-900 bg-yellow-200 rounded-full">
                        High
                    </span>
                    @else
                    <span class="px-3 py-1 text-sm font-semibold text-red-900 bg-red-200 rounded-full">
                        Urgent
                    </span>
                    @endif
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<p class='text-center'>No Results.</p>
@endif

@endsection