<style>
    /* Style for the dropdown drawer */
    .profile-drawer {
        display: none;
    }

    .profile-drawer.active {
        display: block;
        background-color: #ffffff !important
    }
</style>
<button id="profileButton" class="z-50 top-0 right-0  bg-white rounded-full relative scale-110">
    @if(auth()->user()->profile_picture)
    <img class="w-8 h-8 rounded-full object-cover mx-auto" src="/storage/profiles/{{auth()->user()->profile_picture }}"
        alt="hello">
    @else
    <div class="flex flex-col items-center">
        <span class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white">
            <?php
                                            $userName = explode(' ', auth()->user()->name);
                                            echo substr($userName[0], 0, 1) . substr($userName[1], 0, 1);
                                            ?>
        </span>
        <span class="w-[1px] block bg-gray-300 dark:bg-gray-600 grow mt-2"></span>
    </div>
    @endif
</button>
<div>

    <!-- Dropdown Content -->
    <div id="profileDrawer"
        class="profile-drawer absolute top-1 right-1 mt-16 mr-4 w-auto max-h-[50vh] overflow-scroll bg-black rounded-md shadow-lg">
        <div class="container flex-rows bg-white rounded-t-md p-5 shadow-sm w-full bg-gray-100 rounded px-3 py-1">
            <div class="">
                <a href="{{ route('profile.index') }}" class="flex gap-1 items-center mb-4">
                    @if(auth()->user()->profile_picture)
                    <img src="/storage/profiles/{{auth()->user()->profile_picture }}"
                        class="w-10 h-10 rounded-full object-cover mr-2" src="path/to/profile/image.jpg"
                        alt="Profile Picture">
                    @else
                    <div class="flex flex-col items-center">
                        <span class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white">
                            <?php
                                                                    $userName = explode(' ', auth()->user()->name);
                                                                    echo substr($userName[0], 0, 1) . substr($userName[1], 0, 1);
                                                                    ?>
                        </span>
                        <span class="w-[1px] block bg-gray-300 dark:bg-gray-600 grow mt-2"></span>
                    </div>
                    @endif
    
                    <div>
                        <div class="font-medium">{{auth()->user()->name}}</div>
                        <div class="text-sm text-gray-400">{{auth()->user()->title?->title_name}}</div>
                    </div>
                </a>
            </div>
           
            <div class="border-b border-t pt-3">
            <a href="{{ route('task.watchlists.index') }}" class="text-gray-700 hover:text-blue-300 flex items-center mb-2 gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                </svg>
               Task WatchList
            </a>
            </div>
            <div class="border-t pt-3">
            <a href="{{ route('logout') }}" class="text-gray-700 hover:text-blue-300 flex items-center mb-2 gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                </svg>
                Logout
            </a>
            </div>
        </div>
    </div>
</div>

<script>
    // JavaScript to toggle the dropdown drawer
        document.getElementById('profileButton').addEventListener('click', function () {
            event.stopPropagation(); // Prevent click event from propagating to document body
            document.getElementById('profileDrawer').classList.toggle('active');
        });
        document.body.addEventListener('click', function (event) {
            const profileDrawer = document.getElementById('profileDrawer');
            if (!profileDrawer.contains(event.target) && !document.getElementById('profileButton').contains(event.target))
            {
            profileDrawer.classList.remove('active');
            }
            });
</script>