<style>
    /* Style for the dropdown drawer */
    .notification-drawer {
        display: none;
    }

    .notification-drawer.active {
        display: block;
        background-color: #ffffff !important
    }
</style>
<button id="notificationButton"
    class=" z-50 top-0 right-0  bg-white p-2 rounded-full shadow-md relative scale-90 hover:bg-blue-100 hover:scale-100 transition-all">
    <svg class="w-6 h-6 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
        stroke-linecap="round" stroke-linejoin="round">
        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
    </svg>
    <span
    class="absolute w-2 h-2 transform -translate-x-1 translate-y-1 bg-red-500 rounded-full bottom-full left-full"/>
</button>
<div>
    <!-- Notification Bell Icon -->

    <!-- Dropdown Content -->
    <div id="notificationDrawer"
        class=" notification-drawer absolute top-1 right-1 mt-16 mr-4 w-1/2 max-h-[50vh] min-w-[300px] max-w-[360px] overflow-x-hidden overflow-y-auto  !bg-black rounded-md shadow-lg border">
        <div class="pt-3 text-xl px-4 text-black">
            <strong>
                Notifications
            </strong>
        </div>
        <div class="px-2 pb-4">
            @foreach($notifications as $notification)
            <!-- Notification Item -->
            <a href="{{route('tasks.edit', $notification->task_id)}}" target="_blank">
                <div class="p-2 hover:rounded-xl mb-1 hover:bg-gray-200 @if(!$loop->last) border-b @endif">
                        {{-- <div class="flex justify-between items-center"> --}}
                            <span class="font-semibold text-gray-600">
                                {{$notification->task_name}}
                                <span class="font-bold text-blue-300">
                                    #{{$notification->task_id}}
                                </span>
                            </span>
                            <div class="text-xs text-gray-400">
                                {{$notification->created_by}}
                            </div>
                        {{-- </div> --}}
                        <span class="text-sm font-light text-gray-600 line-clamp-2 text-justify">
                            {!!$notification->notification!!}
                        </span>
                    <div class="text-xs text-gray-400 pt-2">
                        {{$notification->created_at?->format('d/m/Y h:i A')}}
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>

<script>
    // JavaScript to toggle the dropdown drawer
        document.getElementById('notificationButton').addEventListener('click', function () {
            event.stopPropagation(); // Prevent click event from propagating to document body
            document.getElementById('notificationDrawer').classList.toggle('active');
        });
        document.body.addEventListener('click', function (event) {
            const notificationDrawer = document.getElementById('notificationDrawer');
            if (!notificationDrawer.contains(event.target) && !document.getElementById('notificationButton').contains(event.target))
            {
            notificationDrawer.classList.remove('active');
            }
            });
</script>