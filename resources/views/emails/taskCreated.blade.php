<div style="
                width: 100%;
                background-color: #f9f9f9;
                display: flex;
                justify-content: center;
                padding: 20px 0px 40px 0px;
            ">
    <div style="min-width: 360px; max-width: 500px; margin: auto;">
        <table role="presentation" style="
                        width: 100%;
                        border: none;
                        background-color: #fff;
                    ">
            <tr>

                <td>
                    @if(data_get($collaboratorFlag, '0.flag', 1) == '0')
                    <img src="{{ $message->embed(base_path() . '/public/img/assigned-bg.png') }}"  style="width: 100%; height: auto;"/>
                    @elseif(data_get($collaboratorFlag, '0.flag', false)  == '1')
                    <img src="{{ $message->embed(base_path() . '/public/img/reviewer-bg.png') }}"  style="width: 100%; height: auto;"/>
                    @else
                    <img src="{{ $message->embed(base_path() . '/public/img/created-bg.png') }}"  style="width: 100%; height: auto;"/>
                    @endif
                </td>
            </tr>
            <tr>
                <td style="text-align: center; padding: 20px; color: #111A4D!important;">
                    <span style="font-size: 20px;">{{$task->title}}</span>
                </td>
            </tr>
            <tr>
                <td style="text-align: left; font-size: 16px;">
                    <div style="padding-left: 10px; padding-right: 10px;">
                        <p>
                            {!!$task->description!!}
                        </p>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">
                    @if(isset($task->deadline))
                    <div style="color: #f59e0b; padding-bottom: 16px; font-size: 16px;">Deadline: {{isset($task->deadline) ? \Carbon\Carbon::parse($task->deadline)->format('m/d/Y h:i A') : null}}</div>
                    @endif
                    <div style="color: #25467d; padding-bottom: 16px" class="task_id">
                        Task Id: {{$task->id}}
                    </div>
                </td>
            </tr>
            <tr>
                <td style="text-align: center; padding-bottom: 34px;">
                    <a href="{{ route('tasks.edit', $task->id) }}" class="button" style="
                                    border: none;
                                    border-radius: 8px;
                                    padding: 12px 16px;
                                    background-color: #29388a;
                                    font-size: 14px;
                                    color: #fefefe;
                                    cursor: pointer;
                                    text-decoration: none;
                                ">
                        Preview In Detail
                    </a>
                </td>
            </tr>
            <tr style="
                    border-top: 1px solid #d4d4d3;
                ">
                <th style="
                    margin-top: 8px;
                    border-top: 1px solid #d4d4d3;
                    padding: 12px;
                ">

                    <div style="font-size: 12px; font-weight: 400; letter-spacing: 1px; margin-bottom: 4px; color: rgb(104, 99, 99);">Shikhar Technologies Pvt. Ltd. &#x2022; Lalitpur, Nepal</div>
                    <div style="font-size: 10px; font-weight: 400; letter-spacing: 1px; margin-bottom: 8px; color: rgba(104, 99, 99, 0.92);">info@shikhartech.com | +977-9817854888</div>
                    <!-- <img width="50px" height="50px" alt="Shikhar tech logo" src="{url('/img/st_logo.png')}}" /> -->
                    <img width="50px" height="50px" src="{{ $message->embed(base_path() . '/public/img/st_logo.png') }}" />
                </th>
            </tr>
        </table>
    </div>
</div>
