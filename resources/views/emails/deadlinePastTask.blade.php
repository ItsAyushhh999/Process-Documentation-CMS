<div style="min-width: 360px; max-width: 500px; margin: auto;">
<h1>Your Pending Tasks</h1>
<table role="presentation" style="
                        width: 100%;
                        border: none;
                        background-color: #fff;
                    ">
    @foreach($tasks as $task)
<tr style="color: #25467d; padding-bottom: 16px">
    <td>Task Id:</td>
    <td><a href="{{ route('tasks.edit', $task->id) }}" > {{$task->id}} </a></td>
</tr>
<tr>
    <td>Title:</td>
    <td>{{$task->title}}</td>
</tr>
<tr>
    <td>Description:</td>
    <td>{!!$task->description!!}</td>
</tr>
<tr style="color: #ff0000; padding-bottom: 16px; font-size: 16px;">
    <td>Deadline:</td>
    <td>{{$task->deadline}}</td>
</tr>
@endforeach
</table>
</div>