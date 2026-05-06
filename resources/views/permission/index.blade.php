@extends('layouts.app')

@section('content')

    <x-header heading="Deployment Permissions To {{ $user->name }}">
        {{-- check for admin --}}

    </x-header>
    @if ($projects->count()>0)

    <div class="mt-5">

        <div class="flex justify-end">
            <button class="mb-6 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded " id="markAll" onclick="markAllCheckboxes(event)">
                Mark All
            </button>
        </div>

        <form method="post" action="{{ route('permissions.update', $user->id) }}">
            @csrf
            @method('patch')

            <div class="block w-full overflow-x-auto">
                <table class="items-center bg-transparent w-full border-collapse border ">
                    <thead>
                    <tr>
                        <th class="px-6 bg-blueGray-50 text-blueGray-500 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                            Project Name
                        </th>

                        @foreach($permissions as $permission)
                            <th class="px-6 bg-blueGray-50 text-blueGray-500 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                {{$permission->name}}
                            </th>
                        @endforeach
                    </tr>
                    </thead>

                    <tbody>

                    @foreach($projects as $project)
                        <tr>
                            <td class="px-6 bg-blueGray-50 text-slate-500 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                {{$project->name}}
                            </td>
                            @foreach($permissions as $permission)
                                <td class="px-6 bg-blueGray-50 text-slate-500 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                    <input
                                        class="bg-gray-50 border-gray-300 focus:ring-3 focus:ring-blue-300 h-4 w-4 rounded"
                                        type="checkbox"
                                        onclick="markUnmarkProject(event, 'project-permission-{{ $project->id }}-{{$permission->name}}')"
                                        id="checkboxDefault"
                                    /> Mark All
                                </td>
                            @endforeach
                        </tr>

                        @foreach($project->subprojects as $subProject)
                            <tr>
                                <td class="border-t-0 px-6 align-middle border-l-1 border-r-1 text-xs whitespace-nowrap p-4 text-left text-blueGray-700 ">
                                    {{$subProject->name}}
                                </td>

                                @foreach($permissions as $permission)
                                    <td class="border-t-0 px-6 align-middle border-l-1 border-r-1 text-xs whitespace-nowrap p-4 text-left text-blueGray-700 ">
                                            <input
                                                class="bg-gray-50 border-gray-300 focus:ring-3 focus:ring-blue-300 h-4 w-4 rounded project-permission-{{ $project->id }}-{{$permission->name}}"
                                                type="checkbox"
                                                name="permissions[]"
                                                value="{{$permission->id}}:{{$subProject->id}}"
                                                {{ $user->userPermissions->contains(function ($uPermission) use ($permission, $subProject) {
                                                    return $uPermission->project_id == $subProject->id && $uPermission->permission_id == $permission->id;
                                                }) ? 'checked' : '' }}
                                                id="checkboxDefault"
                                            />
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endforeach
                    </tbody>

                </table>
            </div>
            <div class="flex justify-end">
                <button class="mt-6 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ">
                    Update
                </button>
            </div>

        </form>
        </div>
    @else
        <p class='text-center'>Project does not have any sub project recently</p>
    @endif

    <script type="text/javascript">
        let marked = true;
        function markAllCheckboxes(event) {
            const element = event.target;
            let checkboxes = document.querySelectorAll('input[type="checkbox"]');

            checkboxes.forEach(function(checkbox) {
                checkbox.checked = marked;
            });

            marked = !marked
            marked ?  element.innerText = "Un-Marked All" : element.innerText = "Marked All";
        }

        function markUnmarkProject(event, data) {
            const element = event.target;
            const isChecked = element.checked;

            let checkboxes = document.getElementsByClassName(data);
            checkboxes = Array.from(checkboxes);
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = isChecked;
            });
        }

    </script>
@endsection
