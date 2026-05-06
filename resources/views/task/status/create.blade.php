
<div class="">
    <form action=" {{ isset($status) ? route('taskStatuses.update',$status->id) : route('taskStatuses.store') }}" method="post">
        @csrf
        @if (isset($status))
            @method('PUT')
        @endif
        <div class="grid mb-5">
            <x-label for="status_name" required>Status Name:</x-label>
            <x-input type="text" id="status_name" name="name" :value="isset($status) ? $status->name : ''" required />
        </div>

        <div class="grid mb-5">
            <x-label for="status_value" required>Status Value:</x-label>
            <x-input type="text" id="status_value" name="value" :value="isset($status) ? $status->value : ''" required />
            {{-- <span class="text-xs">Insert Value's From 0 to 20</span> --}}
        </div>

          {{-- Status Section --}}
          <div class="grid mb-5" style='margin-top: 10px;'>
            <x-label for="Status">Status:</x-label>
            <x-select name="status">
                <option value="" disabled selected>Select--</option>
                @if(isset($status))
                <option value="1"  {{$status?->status == '1' ? 'selected' : ''}} >Active</option>
                <option value="0"  {{$status?->status == '0' ? 'selected' : ''}}>Inactive</option>
                @else
                <option value="1" selected>Active</option>
                <option value="0">Inactive</option>
                @endif
            </x-select>
        </div>

        <div class="flex justify-end mb-5">
            <x-button type="submit">
                {{ isset($status) ? 'Update' : 'Add'  }}
            </x-button>
        </div>
    </form>
</div>
