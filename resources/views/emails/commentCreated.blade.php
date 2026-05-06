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
                        /* padding-bottom: 20px; */
                    ">
            <tr style="display:flex; justify-content: center;">
                <td style="width: 100%;">
                    <img src="{{ $message->embed(base_path() . '/public/img/comment-bg.png') }}"  style="min-width: 360px; max-width: 500px"/>
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">
                    <div style="padding-left: 10px; padding-right: 10px;">
                        <span style="font-size:20px; font-weight: 500;">
                        Task Title: {{$taskTitle}}
                        </span>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="text-align: left; font-size: 16px;">
                    <div style="padding-left: 10px; padding-right: 10px;">
                        <p>
                            {!!$comment!!}
                        </p>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">
                    <div style="color: #25467d; padding-bottom: 16px">
                        Task Id: {{$taskId}}
                    </div>
                </td>
            </tr>
            <tr>
                <td style="text-align: center; padding-bottom: 34px;">
                    <a href="{{ route('tasks.edit', $taskId) }}" style="
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
                    <img width="50px" height="50px" src="{{ $message->embed(base_path() . '/public/img/st_logo.png') }}" />
                </th>
            </tr>
        </table>
    </div>
</div>
