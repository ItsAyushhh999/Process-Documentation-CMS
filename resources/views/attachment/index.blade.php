@extends('layouts.app')

@section('content')
<x-header>
    <h3 >Attachments</h3>
</x-header>
 @if ($attachments->count()>0)   
    <table class="w-full text-base border-collapse table-auto" >
        <thead >
            <th class="p-4 pt-0 pb-3 pl-8 font-medium text-left border-b dark:border-slate-600 text-slate-400 dark:text-slate-200">S.N</th>
            <th class="p-4 pt-0 pb-3 pl-8 font-medium text-left border-b dark:border-slate-600 text-slate-400 dark:text-slate-200">Name</th>
            <th class="p-4 pt-0 pb-3 pl-8 font-medium text-left border-b dark:border-slate-600 text-slate-400 dark:text-slate-200">Task</th>
            <th class="p-4 pt-0 pb-3 pl-8 font-medium text-left border-b dark:border-slate-600 text-slate-400 dark:text-slate-200">Dir</th>
            {{-- <th class="p-4 pt-0 pb-3 pl-8 font-medium text-left border-b dark:border-slate-600 text-slate-400 dark:text-slate-200">Url</th>
            @if(auth()->user()->is_super_admin == 1)
            <th class="p-4 pt-0 pb-3 pl-8 font-medium text-left border-b dark:border-slate-600 text-slate-400 dark:text-slate-200">Action</th>
            @endif --}}
        </thead>
        <tbody>
            @foreach ( $attachments as $attachment )
                <tr>
                    <td class="p-4 pl-8 border-b border-slate-100 dark:border-slate-700 text-slate-500 dark:text-slate-400">{{  $attachment->id }}</td>
                    <td class="p-4 pl-8 border-b border-slate-100 dark:border-slate-700 text-slate-500 dark:text-slate-400">{{  $attachment->name }}</td>
                    <td class="p-4 pl-8 border-b border-slate-100 dark:border-slate-700 text-slate-500 dark:text-slate-400">{{  $attachment->task_id }}</td> 
                    <td class="p-4 pl-8 border-b border-slate-100 dark:border-slate-700 text-slate-500 dark:text-slate-400">{{  $attachment->dir }}</td> 
                    {{-- @if(auth()->user()->is_super_admin == 1)
                    <td class="p-4 pl-8 border-b border-slate-100 dark:border-slate-700 text-slate-500 dark:text-slate-400">
                        <a href="{{ route('attachment.edit', $attachment->id) }}"  class="mb-2 btn btn-success btn-sm">Edit </a>
                    </td>
                    @endif --}}
                </tr>
           @endforeach
       </tbody>
    </table>
 @else
  <p class='text-center'>No attachments.</p>
 @endif

@endsection