<div style="min-width: 360px; max-width: 500px; margin: auto;">
    <h1>Tasks To Review</h1>
    <table role="presentation" style="
                            width: 100%;
                            border: none;
                            background-color: #fff;
                        ">
        @foreach($tasksList as $task)
            <tr style="color: #25467d; padding-bottom: 16px">
                <td><a href="{{ $task['link'] }}">{{ $task['title'] }}</a></td>
            </tr>
        @endforeach
    </table>
</div>
