@extends('layouts.app')

@section('content')
    <x-header heading="Pull Request Logs">
        <div class="justify-end flex">
            @if(auth()->user()->is_super_admin == 1)
                <x-button to="{{ route('githubPrs.index') . '?show=all' }}" class="float-right btn btn-success mr-3">Show All</x-button>
            @endif

        </div>
    </x-header>

    @if ($githubWebhooks->count()>0)
        <div class="py-5 bg-white dark:bg-neutral-800 border dark:border-gray-700 rounded-lg">
            <table class="w-full text-base cTable display nowrap ">
                <thead>
                <th class="min-w-[90px]">PR Id</th>
                <th class="min-w-[300px]">PR Title</th>
                <th class="min-w-[150px]">Sender Username</th>
                <th class="min-w-[150px]">Repository</th>
                <th class="min-w-[150px]">Comment</th>
                <th class="min-w-[200px]">Status</th>
                <th class="min-w-[120px]">Task</th>
                <th class="min-w-[120px]">User</th>
                <th class="min-w-[120px]"></th>
                </thead>
                <tbody>
                @foreach($githubWebhooks as $webhook)
                    <tr>
                        <td><a class="text-darkblue font-semibold" href="{{$webhook->pull_request_url}}">{{ $webhook->pull_request_id  }}</a></td>
                        <td>{{$webhook->pull_request_title}}</td>
                        <td><a class="text-darkblue font-semibold" href="{{ $webhook->pull_request_sender_url }}">{{$webhook->pull_request_sender_username}}</a></td>
                        <td><a class="text-darkblue font-semibold" href="{{$webhook->repository_url}}"> {{ $webhook->repository_name }}</a></td>
                        <td>{!! $webhook->pull_request_comment !!}</td>
                        <td>
                            @if($webhook->status == 'ATTACHED')
                                <x-chip color="violet">ATTACHED</x-chip>
                            @elseif($webhook->status == 'UN-ATTACHED')
                                <x-chip color="red">UN-ATTACHED</x-chip>
                            @else
                                <x-chip color="blue">RESOLVED</x-chip>
                            @endif
                           </td>
                        <td>
                            @if($webhook->task?->id)
                                <a href="{{ route('tasks.edit', $webhook->task?->id) }}" class="text-darkblue font-semibold">{{$webhook->task->title}}</a>
                            @else
                                NULL
                            @endif
                        </td>
                        <td>
                            @if($webhook->user?->id)
                                {{$webhook->user->name}}
                            @else
                                NULL
                            @endif
                        </td>

                        <td>{{$webhook->created_at->format('D m/Y')}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class='text-center'>No webhooks.</p>
    @endif

@endsection
