<div class="px-3 py-2">
    <div class="center relative inline-block select-none whitespace-nowrap rounded-lg bg-green-500 py-2 px-3.5 p-2 align-baseline font-sans text-xs font-bold uppercase leading-none text-white">
        {{$feed->type}}
    </div>
    @if($feed->status == 'SUCCESS' )
        <div class="center relative inline-block select-none whitespace-nowrap rounded-lg bg-blue-500 py-2 px-3.5 align-baseline font-sans text-xs font-bold uppercase leading-none text-white">
            {{$feed->status}}
        </div>
    @elseif($feed->status == 'FAILURE')
        <div class="center relative inline-block select-none whitespace-nowrap rounded-lg bg-red-500 py-2 px-3.5 align-baseline font-sans text-xs font-bold uppercase leading-none text-white">
            {{$feed->status}}
        </div>
    @elseif($feed->status == 'REVERT')
        <div class="center relative inline-block select-none whitespace-nowrap rounded-lg bg-yellow-500 py-2 px-3.5 align-baseline font-sans text-xs font-bold uppercase leading-none text-white">
            {{$feed->status}}
        </div>
    @elseif($feed->status == 'CREATED')
        <div class="center relative inline-block select-none whitespace-nowrap rounded-lg bg-green-700 py-2 px-3.5 align-baseline font-sans text-xs font-bold uppercase leading-none text-white">
            {{$feed->status}}
        </div>
    @elseif($feed->status == 'CLOSE')
        <div class="center relative inline-block select-none whitespace-nowrap rounded-lg bg-neutral-500 py-2 px-3.5 align-baseline font-sans text-xs font-bold uppercase leading-none text-white">
            {{$feed->status}}
        </div>
    @else
        <div class="center relative inline-block select-none whitespace-nowrap rounded-lg bg-blue-600 py-2 px-3.5 align-baseline font-sans text-xs font-bold uppercase leading-none text-white">
            {{$feed->status}}
        </div>
    @endif

    @if($feed->task)
        <a href="{{route('tasks.edit', ['task' => $feed->task->id])}}">
            <div class="center relative inline-block select-none whitespace-nowrap rounded-lg bg-blue-700 py-2 px-3.5 align-baseline font-sans text-xs font-bold uppercase leading-none text-white">
              Task:  {{$feed->task?->title}}
            </div>
        </a>
    @endif

    @if($feed->project)
        <a href="{{ route('documents.index', ['project' => $feed->project->id]) }}">
            <div class="center relative inline-block select-none whitespace-nowrap rounded-lg bg-blue-800 py-2 px-3.5 align-baseline font-sans text-xs font-bold uppercase leading-none text-white">
               Project:  {{$feed->project?->name}}
            </div>
        </a>
    @endif

    @if($feed->createdBy)
        <div class="center relative inline-block select-none whitespace-nowrap rounded-lg bg-blue-900 py-2 px-3.5 align-baseline font-sans text-xs font-bold uppercase leading-none text-white">
           Created By: {{$feed->createdBy->name}}
        </div>
    @endif

</div>
