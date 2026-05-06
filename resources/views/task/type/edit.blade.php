<div class="">
    <form action=" {{ route('taskTypes.update',$task_type->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="grid mb-5">
            <x-label for="task_type" required>Task type :</x-label>
            <x-input type="text" id="task_type" name="task_type" value="{{ $task_type->type }}" required />
        </div>

        <div class="flex justify-end mb-5">
            <x-button type="submit">
                {{  'Update'  }}
            </x-button>
        </div>
    </form>
</div>