@extends('layouts.app')

@section('content')
<x-header heading="Profile"></x-header>


<div class=" bg-white rounded-lg shadow-md  py-6  p-4 sm:px-6 " style="width:500px;">
    <div class="grid grid-cols-3 gap-5">
        <div class="flex flex-col items-center">
            <div class="flex items-center">
                <div class="rounded-full w-[160px] h-[160px] p-4 relative">
                    <img id="preview-selected-image" src="/storage/profiles/{{auth()->user()->profile_picture }}"
                        class="rounded-full w-full h-full object-center object-cover" />

                        <div class="absolute right-3 bottom-6  flex flex-col items-center">
                            <label for="file-upload"
                                class="flex cursor-pointer items-center w-full p-2 rounded-full bg-gray-200/50  focus:outline-none focus:ring-2 focus:ring-offset-2 hover:bg-gray-400 focus:ring-indigo-500">
                                <span class="text-sm font-medium text-center text-gray-700 whitespace-nowrap">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                        class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </span>
                            </label>
                        </div>
                </div>
            </div>
        </div>
        <div class="flex flex-col justify-center col-span-2">
            <div class="flex flex-col  justify-start">
                <div>
                    <span class="text-4xl">
                        {{ $user->name }}
                    </span>
                </div>
                <div class="text-gray-400">{{ $user->email }}</div>
                <div class="mt-2">
                    <x-chip color="sky">
                        {{ $user->title?->title_name ?: 'NA' }}
                    </x-chip>
                </div>
            </div>
        </div>
    </div>
    <div style="">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid px-4 py-8 gap-2">
                <input type="file" name="profile_picture" id="file-upload" accept="image/*"
                    onchange="previewImage(event);" class="hidden" />
                <!-- slack_username -->
                <div class="">
                    <label for="slack" class="block mb-2 text-lg font-medium text-gray-900 dark:text-white">Slack
                        Username</label>

                    <input autocomplete="off" id="slack" type="text" value="{{ $user->slack_username }}"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        name="slack_username" />

                </div>

                <!-- Phone -->
                <div class="">
                    <label for="phone"
                        class="block mb-2 text-lg font-medium text-gray-900 dark:text-white">Phone</label>
                    <input autocomplete="off" id="phone" type="text" name="phone"
                        value="{{ old('phone', $user->phone) }}"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                </div>

                <!-- Secondary Phone -->
                <div class="">
                    <label for="secondary_phone"
                        class="block mb-2 text-lg font-medium text-gray-900 dark:text-white">Secondary
                        Phone</label>
                    <input id="secondary_phone" type="text" name="secondary_phone" value="{{ $user->secondary_phone }}"
                        autocomplete="off"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                </div>

            </div>
            <div class="px-4 ">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                    Update Profile
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
<script>
    const previewImage = (event) => {
        const imageFiles = event.target.files;
        const imageFilesLength = imageFiles.length;
        if (imageFilesLength > 0) {
            const imageSrc = URL.createObjectURL(imageFiles[0]);
            const imagePreviewElement = document.querySelector("#preview-selected-image");
            imagePreviewElement.src = imageSrc;
            imagePreviewElement.style.display = "block";
        }
};
</script>