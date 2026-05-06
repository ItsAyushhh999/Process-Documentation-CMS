
<div class="">
    <form action=" {{ isset($title) ? route('titles.update',$title->id) : route('titles.store') }}" method="post">
        @csrf
        @if (isset($title))
            @method('PUT')              
        @endif
        <div class="grid mb-5">
            <x-label for="title_name" required>Name:</x-label>
            <x-input type="text" id="title_name" name="title_name" :value="isset($title) ? $title->title_name : ''" required />
        </div>
        
        <div class="flex justify-end mb-5">
            <x-button type="submit">
                {{ isset($title) ? 'Update' : 'Add'  }}
            </x-button>
        </div>
    </form>
</div>