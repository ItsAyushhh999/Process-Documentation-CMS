@extends('layouts.app')

@section('content')

<div class="p-5 border border-primary rounded w-1/3 dark:border-gray-700 bg-white dark:bg-neutral-800">
    <form action=" {{ route('users.update',$user->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="grid mb-5">
            <x-label for="name">Name: {{ $user->name }}</x-label>
        </div>

        {{-- Department section --}}
        <div class="grid mb-5" style='margin-top: 10px;'>
            <x-label for="department">Department:</x-label>
            <x-select name="department">
                <option value="" disabled selected>Select--</option>
                @foreach($departments as $department)
                @if ( $user->department && ($department->id == $user->department->id))
                <option value="{{$department->department_name}},{{$department->id}}" selected>{{$department->department_name}}</option>
                @else
                <option value="{{$department->department_name}},{{$department->id}}">{{$department->department_name}}</option>
                @endif
                @endforeach
            </x-select>
        </div>
        {{-- Title section --}}
        <div class="grid mb-5" style='margin-top: 10px;'>
            <x-label for="title">Title:</x-label>
            <x-select name="title">
                <option value="" disabled selected>Select--</option>
                @foreach($titles as $title)
                @if ( $user->title && ($title->id == $user->title->id))
                <option value="{{$title->id}}" selected>{{$title->title_name}}</option>
                @else
                <option value="{{$title->id}}">{{$title->title_name}}</option>
                @endif
                @endforeach
            </x-select>
        </div>
        {{-- Phone section --}}
        <div class="grid mb-5" style='margin-top: 10px;'>
            <x-label for="phone">Phone:</x-label>
            <x-input type="tel" id="phone" name="phone" value="{{$user->phone}}" required />
        </div>
        {{-- Slack Username section --}}
        <div class="grid mb-5" style='margin-top: 10px;'>
            <x-label for="slackname">Slack Username:</x-label>
            <x-input type="text" id="slackname" name="slack_username" value="{{$user->slack_username}}" required />
        </div>

        <div class="grid mb-5" style='margin-top: 10px;'>
            <x-label for="github_username">Github Username:</x-label>
            <x-input type="text" id="github_username" name="github_username" value="{{$user->github_username}}" required />
        {{-- Status Section --}}
        <div class="grid mb-5" style='margin-top: 10px;'>
            <x-label for="Status">Status:</x-label>
            <x-select name="status">
                <option value="" disabled selected>Select--</option>
                <option value="1" {{$user->status == '1' ? 'selected' : ''}}>Active</option>
                <option value="0" {{$user->status == '0' ? 'selected' : ''}}>Inactive</option>
            </x-select>
        </div>
        <div class="grid mb-5">
            <x-button type="submit">
                Update
            </x-button>
        </div>
    </form>
</div>

@endsection
