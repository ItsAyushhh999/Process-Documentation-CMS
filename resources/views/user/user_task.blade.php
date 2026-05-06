@extends('layouts.app')
@section('content')
<x-header heading="{{ $user }}">

</x-header>   

<div class="grid  gap-4 rounded-lg">
   <div class="py-5 border rounded-lg col-span-1 overflow-auto bg-white dark:bg-gray-800 dark:border-gray-600">
      <table class="border-collapse text-base cTable display nowrap ">
         <thead>
            <th>Task Id</th>
            <th>Name</th>
            <th>Task Title</th>
            <th>Priority</th>
            <th>Reviewer</th>
            <th>Role</th>
            <th>Created At</th>
            <th>Status</th>
         </thead>
         <tbody>
            @foreach ( $data as $task )
            <tr>
               <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-darkblue font-semibold dark:text-slate-400">
                  <a href="{{ route('tasks.edit', $task->id) }}"  class="mb-2 btn btn-success btn-sm">{{ $task->id }} </a>
               </td>
               <td>{{ $task->projectName }}</td>
               <td>{{ $task->title }}</td>
               <td>
                    @if($task->priority == '0')
                    <span class="px-3 py-1 text-green-900 bg-green-200 font-semibold rounded-full text-base">
                        Normal
                    </span>
                    @elseif($task->priority == '1')
                    <span class="px-3 py-1 text-yellow-900 bg-yellow-200 font-semibold rounded-full text-base">
                        High
                    </span>
                    @else
                    <span class="px-3 py-1 text-red-900 bg-red-200 font-semibold rounded-full text-base">
                        Urgent
                    </span>
                    @endif
                </td>
               <td>
                  @foreach ($task->collaborators as $reviewer )
                     @if($reviewer->flag == 1)
                        <div class="p-1">
                    <span class="px-3 py-1 font-semibold bg-sky-200 text-sky-900 rounded-full text-base whitespace-nowrap" title="{{$reviewer->name}}">
                        <?php
                        $userName = explode(' ', $reviewer->name);
                        echo ($userName[0] . '.' . substr($userName[1], 0, 1));
                        ?>
                    </span>
                </div>
                     @endif
                  @endforeach
               </td>
               <td>
                  <span class="flex gap-1">
                     @if($task->isAssignee == '0')
                     <span class="text-sky-900 bg-sky-200 font-semibold px-3 py-1 rounded-full text-base whitespace-nowrap">
                        Assignee
                     </span>
                     @endif
                     @if($task->isReviewer)
                     <span class="text-green-900 bg-green-200 font-semibold px-3 py-1 rounded-full text-base whitespace-nowrap">
                        Reviewer
                     </span>
                     @endif
                  </span>
               </td>
               <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400 whitespace-nowrap">{{ $task->created_at->format('m/d/Y H:i A') }}</td>
               <td>
                    @if($task->status == '0')
                    <span class="px-3 py-1 text-red-800 bg-red-200 rounded-full text-base font-semibold">
                        Assigned
                    </span>
                    @elseif($task->status == '1')
                    <span class="px-3 py-1 text-violet-900 bg-violet-200 rounded-full text-base font-semibold">
                        In Progress
                    </span>
                    @elseif($task->status == '2')
                    <span class="px-3 py-1 text-yellow-900 bg-yellow-200 rounded-full text-base font-semibold">
                        Assigned for Review
                    </span>
                    @elseif($task->status == '3')
                    <span class="px-3 py-1 text-blue-900 bg-blue-200 rounded-full text-base font-semibold">
                        Reviewing
                    </span>
                    @elseif($task->status == '4')
                    <span class="px-3 py-1 text-cyan-900 bg-cyan-200 rounded-full text-base font-semibold">
                        Reviewed
                    </span>
                    @elseif($task->status == '5')
                    <span class="px-3 py-1 text-green-900 bg-green-200 rounded-full text-base font-semibold">
                        Completed
                    </span class="px-3 py-1 text-green-900 bg-green-200 rounded-full text-base font-semibold">
                    @elseif($task->status == '6')
                    <span>
                        Closed
                    </span>
                    @elseif($task->status == '7')
                    <span class="px-3 py-1 text-orange-900 bg-orange-200 rounded-full text-base font-semibold">
                        Created
                    </span>
                    @elseif($task->status == '8')
                    <span class="px-3 py-1 text-purple-900 bg-purple-100 rounded-full text-base font-semibold whitespace-nowrap">
                        Staging - Ready to Upload
                    </span>
                    @elseif($task->status == '9')
                    <span class="px-3 py-1 text-purple-900 bg-purple-300 rounded-full text-base font-semibold whitespace-nowrap">
                        Staging - Uploaded
                    </span>
                    @elseif($task->status == '10')
                    <span class="px-3 py-1 text-teal-900 bg-teal-200 rounded-full text-base font-semibold whitespace-nowrap">
                        Live - Ready to Upload
                    </span>
                    @elseif($task->status == '11')
                    <span class="px-3 py-1 text-teal-900 bg-teal-400 rounded-full text-base font-semibold whitespace-nowrap">
                        Live - Uploaded
                    </span>
                    @endif
                </td>
            </tr>
            @endforeach
         </tbody>
      </table>
   </div>
</div>

@endsection