<!-- @if(session()->has('status'))
<div class="alert alert-success ">
    {{ session()->get('status') }}
</div>
@endif

@if($errors->any())
<div class="alert alert-danger ">
<ul class="list-group ">
    @foreach ($errors->all() as $error )
    <li class="list-group-item.text-danger " style="margin-left: 4px;">
        {{ $error }}
    </li>
    @endforeach
</ul>
</div>
@endif -->

<div class="flash">
    @if ($error = Session::get('error'))
    <div class="fade -translate-x-1/2 fixed z-50 w-full max-w-sm p-3 transition-all transform bg-red-700 rounded-lg shadow-xl bounce left-1/2 top-3" x-data="{alertOpen: true}" x-show="alertOpen" role="alert">
        <strong class="text-white">{{ $error }}</strong>
        <a class="absolute inline-flex items-center justify-center w-5 h-5 text-white transition-all duration-100 bg-black/[.05] rounded-full cursor-pointer top-2 right-2" @click="alertOpen = false">
            <svg style="width:16px;height:16px" viewBox="0 0 24 24">
                <path fill="currentColor" d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" />
            </svg>
        </a>
    </div>
    @endif

    @if($errors->all())
    <div class="fade -translate-x-1/2 fixed z-50 w-full max-w-sm p-3 transition-all transform bg-red-700 rounded-lg shadow-xl bounce left-1/2 top-3" x-data="{alertOpen: true}" x-show="alertOpen">
        <ul>
            @foreach($errors->all() as $error)
            <li class="text-white">{{ $error }}</li>
            @endforeach
        </ul>
        <a class="absolute inline-flex items-center justify-center w-5 h-5 text-white transition-all duration-100 bg-black/[.05] rounded-full cursor-pointer top-2 right-2" @click="alertOpen = false">
            <svg style="width:16px;height:16px" viewBox="0 0 24 24">
                <path fill="currentColor" d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" />
            </svg>
        </a>
    </div>
    @endif
    @if ($message = Session::get('success'))
    <div class="fade -translate-x-1/2 fixed z-50 w-full max-w-sm p-3 transition-all transform bg-green-700 rounded-lg shadow-xl bounce left-1/2 top-3" role="alert" x-data="{alertOpen: true}" x-show="alertOpen">
        <strong class="text-white">{{ $message }}</strong>
        <a class="absolute inline-flex items-center justify-center w-5 h-5 text-white transition-all duration-100 bg-black/[.05] rounded-full cursor-pointer top-2 right-2" @click="alertOpen = false">
            <svg style="width:16px;height:16px" viewBox="0 0 24 24">
                <path fill="currentColor" d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" />
            </svg>
        </a>
    </div>
    @endif
</div>
