@extends('layouts.app')

@section('content')



<x-header heading="Users">
    <div style="display: flex; justify-content: end; margin-bottom: 10px" class="mr-2">
        <x-button type="submit" to="{{ route('users.index', ['status' => $status == '1' ? '0' : '1' ]) }}">
            {{ $status == '1' ? 'Inactive' : 'Active' }}
        </x-button>
   </div>
</x-header>
<div class="py-5 border dark:border-gray-700 rounded-lg bg-white dark:bg-neutral-800">
   @if ($users->count()>0)
    <table class="w-full text-base cTable display nowrap ">
        <thead>
            <th>Name</th>
            <th>Status</th>
            <th>Slack Username</th>
            <th>Github Username</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Department</th>
            <th>Title</th>
            <th>Updated By</th>
            @if($isDepartmentHead || Auth::user()->id)
            <th data-orderable="false">Action</th>
            @endif
        </thead>
        <tbody>
            @foreach ( $users as $user )
                <tr class="">
                    <td>
                       <div class="flex items-center gap-2">
                            <div>
                                @if(empty($user->profile_picture))
                                <p class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white">
                                    <?php
                                                                                                        $name = explode(' ',$user->name);
                                                                                                        $userFirstName = substr($name[0],0,1);
                                                                                                        $userLastName = substr($name[1],0,1);
                                                                                                        echo $userFirstName.$userLastName;
                                                                                                    ?>
                                </p>
                                @else
                                <div class="w-10 h-10">
                                    <img src="{{asset('storage/profiles/'.$user->profile_picture)}}" alt="profle_picture"
                                        class="rounded-full w-full h-full object-center object-cover cursor-pointer">
                                </div>
                                @endif
                            </div>
                            <div>
                                {{ $user->name }}
                                <a href="{{ route('users.userTaskList',$user->id) }}" class="text-blue-500">
                                    ({{ $user->task_count}})
                                </a>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($user->status === '1')
                        <x-chip color="green">Active</x-chip>
                        @else
                        <x-chip color="red">Inactive</x-chip>
                        @endif
                    <td>{{  $user->slack_username }}</td>
                    <td>{{  $user->github_username }}</td>
                    <td>{{  $user->email }}</td>
                    <td>{{  $user->phone }}</td>
                    <td>{{ ($user->departments_name) ? $user->departments_name : NULL }}</td>
                    <td>{{ ($user->title_name) ? $user->title_name : NULL }}</td>
                    <td>{{ ($user->updated_by) ? $user->updatedBy->name : NULL }}</td>
                    @if($isDepartmentHead || Auth::user()->id)
                    <td class="flex gap-2 items-center">

                         <x-icon-button to="{{ route('users.edit', $user->id) }}" color="blue" />
                          {{-- @if($isDepartmentHead) --}}
                            <a href="{{ route('permissions.edit', $user->id) }}" class="h-8 w-8  flex items-center justify-center rounded-full hover:bg-blue-100  dark:bg-opacity-5 cursor-pointer" >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                            </a>
                         {{-- @endif --}}
                    </td>
                    @endif
                </tr>
           @endforeach
       </tbody>
    </table>
</div>
@else
<p class='text-center'>No users.</p>
@endif

@endsection
