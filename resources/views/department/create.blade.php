
<div class="">
    <form action=" {{ isset($department) ? route('departments.update',$department->id) : route('departments.store') }}" method="post">
        @csrf
        @if (isset($department))
            @method('PUT')              
        @endif
        <div class="grid mb-5">
            <x-label for="department_name" required>Department Name:</x-label>
            <x-input type="text" id="department_name" name="department_name" :value="isset($department) ? $department->department_name : ''" required />
        </div>

        <div class="flex justify-end mb-5">
            <x-button type="submit">
                {{ isset($department) ? 'Update' : 'Add'  }}
            </x-button>
        </div>
    </form>
</div>