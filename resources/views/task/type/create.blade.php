
<div class="">
    <form action=" {{ route('taskTypes.store') }}" method="post">
        @csrf
        <div class="grid mb-5">
            <x-label for="task_type" required>Task type :</x-label>
            <x-input type="text" id="task_type" name="task_type" value="" required />
        </div>

        <div class="flex justify-end mb-5">
            <x-button type="submit">
                {{  'Add'  }}
            </x-button>
        </div>
    </form>
<div>