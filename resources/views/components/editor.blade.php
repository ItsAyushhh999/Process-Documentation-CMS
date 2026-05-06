@props(['value' => '', 'name' => '', 'id' => ''])

<div class="editor" {{ $attributes->merge(['id' => $id]) }}>
  <div class="editor-toolbar rounded-t-md border-t border-l border-r p-2 max-w-full" >
    <div class="editor-toolbar__buttons">

      <button class="editor-toolbar__button   hover:bg-neutral-100 p-2 rounded-l focus:outline-none"
        data-tiptap-button="bold" type="button" {{ $attributes->merge(['data-tiptap-id' => $id]) }}><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
          fill="currentColor" class="bi bi-type-bold" viewBox="0 0 16 16" >
          <path d="M8.21 13c2.106 0 3.412-1.087 3.412-2.823 0-1.306-.984-2.283-2.324-2.386v-.055a2.176 2.176 0 0 0 1.852-2.14c0-1.51-1.162-2.46-3.014-2.46H3.843V13H8.21zM5.908 4.674h1.696c.963 0 1.517.451 1.517 1.244 0 .834-.629 1.32-1.73 1.32H5.908V4.673zm0 6.788V8.598h1.73c1.217 0 1.88.492 1.88 1.415 0 .943-.643 1.449-1.832 1.449H5.907z" /></svg></button>
      <button class="editor-toolbar__button   hover:bg-neutral-100 p-2 rounded-l focus:outline-none"
        data-tiptap-button="italic" type="button" {{ $attributes->merge(['data-tiptap-id' => $id]) }}><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-type-italic" viewBox="0 0 16 16">
      <path d="M7.991 11.674 9.53 4.455c.123-.595.246-.71 1.347-.807l.11-.52H7.211l-.11.52c1.06.096 1.128.212 1.005.807L6.57 11.674c-.123.595-.246.71-1.346.806l-.11.52h3.774l.11-.52c-1.06-.095-1.129-.211-1.006-.806z"/></svg></button>
      <button class="editor-toolbar__button   hover:bg-neutral-100 p-2 rounded-l focus:outline-none"
        data-tiptap-button="underline" type="button" {{ $attributes->merge(['data-tiptap-id' => $id]) }}><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-type-underline" viewBox="0 0 16 16">
      <path d="M5.313 3.136h-1.23V9.54c0 2.105 1.47 3.623 3.917 3.623s3.917-1.518 3.917-3.623V3.136h-1.23v6.323c0 1.49-.978 2.57-2.687 2.57-1.709 0-2.687-1.08-2.687-2.57V3.136zM12.5 15h-9v-1h9v1z"/></svg></button>
      <button class="editor-toolbar__button   hover:bg-neutral-100 p-2 rounded-l focus:outline-none"
        data-tiptap-button="strike" type="button" {{ $attributes->merge(['data-tiptap-id' => $id]) }}><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-type-strikethrough" viewBox="0 0 16 16">
      <path d="M6.333 5.686c0 .31.083.581.27.814H5.166a2.776 2.776 0 0 1-.099-.76c0-1.627 1.436-2.768 3.48-2.768 1.969 0 3.39 1.175 3.445 2.85h-1.23c-.11-1.08-.964-1.743-2.25-1.743-1.23 0-2.18.602-2.18 1.607zm2.194 7.478c-2.153 0-3.589-1.107-3.705-2.81h1.23c.144 1.06 1.129 1.703 2.544 1.703 1.34 0 2.31-.705 2.31-1.675 0-.827-.547-1.374-1.914-1.675L8.046 8.5H1v-1h14v1h-3.504c.468.437.675.994.675 1.697 0 1.826-1.436 2.967-3.644 2.967z"/></svg></button>
      
      <button class="editor-toolbar__button ml-3  hover:bg-neutral-100 p-2 rounded-l focus:outline-none"
        data-tiptap-button="ordered-list" type="button" {{ $attributes->merge(['data-tiptap-id' => $id]) }}><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list-ol" viewBox="0 0 16 16">
      <path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5z"/>
      <path d="M1.713 11.865v-.474H2c.217 0 .363-.137.363-.317 0-.185-.158-.31-.361-.31-.223 0-.367.152-.373.31h-.59c.016-.467.373-.787.986-.787.588-.002.954.291.957.703a.595.595 0 0 1-.492.594v.033a.615.615 0 0 1 .569.631c.003.533-.502.8-1.051.8-.656 0-1-.37-1.008-.794h.582c.008.178.186.306.422.309.254 0 .424-.145.422-.35-.002-.195-.155-.348-.414-.348h-.3zm-.004-4.699h-.604v-.035c0-.408.295-.844.958-.844.583 0 .96.326.96.756 0 .389-.257.617-.476.848l-.537.572v.03h1.054V9H1.143v-.395l.957-.99c.138-.142.293-.304.293-.508 0-.18-.147-.32-.342-.32a.33.33 0 0 0-.342.338v.041zM2.564 5h-.635V2.924h-.031l-.598.42v-.567l.629-.443h.635V5z"/></svg></button>
      <button class="editor-toolbar__button   hover:bg-neutral-100 p-2 rounded-l focus:outline-none"
        data-tiptap-button="bullet-list" type="button" {{ $attributes->merge(['data-tiptap-id' => $id]) }}><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list-ul" viewBox="0 0 16 16">
      <path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm-3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm0 4a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm0 4a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/></svg></button>
    
      <button class="editor-toolbar__button   hover:bg-neutral-100 p-2 rounded-l focus:outline-none"
        data-tiptap-button="alignment" type="button" {{ $attributes->merge(['data-tiptap-id' => $id]) }}><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-justify" viewBox="0 0 16 16">
      <path fill-rule="evenodd" d="M2 12.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/></svg></button>

      
      <div class="dropdown  editor-toolbar__button ml-3   focus:outline-none" >
        <span > <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-type-h1" viewBox="0 0 16 16">
      <path d="M7.648 13V3H6.3v4.234H1.348V3H0v10h1.348V8.421H6.3V13h1.348ZM14 13V3h-1.333l-2.381 1.766V6.12L12.6 4.443h.066V13H14Z"/>
    </svg></span>
    <div class="dropdown-content text-left px-4 py-4 w-42 hidden absolute bg-slate-50 rounded-md shadow-md z-10">
      <div class="flex flex-col">
        <button class="editor-toolbar__button   hover:bg-neutral-100 p-2 rounded-l focus:outline-none font-bold text-2xl"
        data-tiptap-button="heading[1]" type="button" {{ $attributes->merge(['data-tiptap-id' => $id]) }}> Heading 1 </button>
      <button class="editor-toolbar__button  hover:bg-neutral-100 p-2 rounded-l focus:outline-none font-semibold text-xl"
        data-tiptap-button="heading[2]" type="button" {{ $attributes->merge(['data-tiptap-id' => $id]) }}>Heading 2</button>
        <button class="editor-toolbar__button  hover:bg-neutral-100 p-2 rounded-l focus:outline-none"
        data-tiptap-button="heading[3]" type="button" {{ $attributes->merge(['data-tiptap-id' => $id]) }}>Heading 3 </button>      
      </div>
      </div>
    </div>
    
          <button class="editor-toolbar__button ml-3  hover:bg-neutral-100 p-2 rounded-l focus:outline-none"
            data-tiptap-button="code-block" type="button" {{ $attributes->merge(['data-tiptap-id' => $id]) }}><svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 4 1 8l4 4m10-8 4 4-4 4M11 1 9 15"/></svg></button>
    
    <!-- <button class="editor-toolbar__button ml-4   hover:bg-neutral-100 px-2  focus:outline-none  " data-tiptap-button="color" type="button"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-palette" viewBox="0 0 16 16">
      <path d="M8 5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zm4 3a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zM5.5 7a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm.5 6a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/>
      <path d="M16 8c0 3.15-1.866 2.585-3.567 2.07C11.42 9.763 10.465 9.473 10 10c-.603.683-.475 1.819-.351 2.92C9.826 14.495 9.996 16 8 16a8 8 0 1 1 8-8zm-8 7c.611 0 .654-.171.655-.176.078-.146.124-.464.07-1.119-.014-.168-.037-.37-.061-.591-.052-.464-.112-1.005-.118-1.462-.01-.707.083-1.61.704-2.314.369-.417.845-.578 1.272-.618.404-.038.812.026 1.16.104.343.077.702.186 1.025.284l.028.008c.346.105.658.199.953.266.653.148.904.083.991.024C14.717 9.38 15 9.161 15 8a7 7 0 1 0-7 7z"/></svg></button> -->
      
      <!-- <div class="relative inline-block text-left">
      <button id="alignButton" class="  py-3 px-4 rounded cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-justify font-weight-bold" viewBox="0 0 16 16">
      <path fill-rule="evenodd" d="M2 12.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/></svg></button>
      <ul id="alignOptions" class="hidden absolute z-10 mt-2 py-2 px-0 bg-white border border-gray-300 rounded shadow-lg">
      <li data-align="left" class="cursor-pointer px-4 py-2 hover:bg-gray-100">Left</li>
      <li data-align="center" class="cursor-pointer px-4 py-2 hover:bg-gray-100">Center</li>
      <li data-align="right" class="cursor-pointer px-4 py-2 hover:bg-gray-100">Right</li>
      <li data-align="justify" class="cursor-pointer px-4 py-2 hover:bg-gray-100">Justify</li>
    </ul> -->

    <!-- color picker -->
    <input type="color" data-tiptap-button="highlight-color-picker" class="invisible	 h-0.5 w-0.5" {{ $attributes->merge(['data-tiptap-id' => $id]) }}>
    <button class="editor-toolbar__button ml-2  hover:bg-neutral-100 p-2 rounded-l focus:outline-none"
        data-tiptap-button="highlight" type="button" {{ $attributes->merge(['data-tiptap-id' => $id]) }}><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-highlighter" viewBox="0 0 16 16">
      <path fill-rule="evenodd" d="M11.096.644a2 2 0 0 1 2.791.036l1.433 1.433a2 2 0 0 1 .035 2.791l-.413.435-8.07 8.995a.5.5 0 0 1-.372.166h-3a.5.5 0 0 1-.234-.058l-.412.412A.5.5 0 0 1 2.5 15h-2a.5.5 0 0 1-.354-.854l1.412-1.412A.5.5 0 0 1 1.5 12.5v-3a.5.5 0 0 1 .166-.372l8.995-8.07.435-.414Zm-.115 1.47L2.727 9.52l3.753 3.753 7.406-8.254-2.905-2.906Zm3.585 2.17.064-.068a1 1 0 0 0-.017-1.396L13.18 1.387a1 1 0 0 0-1.396-.018l-.068.065 2.85 2.85ZM5.293 13.5 2.5 10.707v1.586L3.707 13.5h1.586Z"/></svg></button>


      <div class="dropdown  editor-toolbar__button ml-3   focus:outline-none rounded-md" >
        <span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-quote" viewBox="0 0 16 16">
      <path d="M12 12a1 1 0 0 0 1-1V8.558a1 1 0 0 0-1-1h-1.388c0-.351.021-.703.062-1.054.062-.372.166-.703.31-.992.145-.29.331-.517.559-.683.227-.186.516-.279.868-.279V3c-.579 0-1.085.124-1.52.372a3.322 3.322 0 0 0-1.085.992 4.92 4.92 0 0 0-.62 1.458A7.712 7.712 0 0 0 9 7.558V11a1 1 0 0 0 1 1h2Zm-6 0a1 1 0 0 0 1-1V8.558a1 1 0 0 0-1-1H4.612c0-.351.021-.703.062-1.054.062-.372.166-.703.31-.992.145-.29.331-.517.559-.683.227-.186.516-.279.868-.279V3c-.579 0-1.085.124-1.52.372a3.322 3.322 0 0 0-1.085.992 4.92 4.92 0 0 0-.62 1.458A7.712 7.712 0 0 0 3 7.558V11a1 1 0 0 0 1 1h2Z"/></svg></span>
      <div class="dropdown-content text-left px-4 py-4 w-32 hidden absolute bg-slate-50 rounded-md shadow-md z-10">
          <div class="flex flex-col">
        </button><button class="editor-toolbar__button hover:bg-red-100 p-2 rounded-l focus:outline-none"
              data-tiptap-button="blockquote_red" type="button" {{ $attributes->merge(['data-tiptap-id' => $id]) }}>
             Red
          </button>
        <button class="editor-toolbar__button hover:bg-yellow-100 p-2 rounded-l focus:outline-none"
              data-tiptap-button="blockquote_yellow" type="button" {{ $attributes->merge(['data-tiptap-id' => $id]) }}>
              Yellow 
          <button class="editor-toolbar__button hover:bg-green-100 p-2 rounded-l focus:outline-none"
              data-tiptap-button="blockquote_green" type="button" {{ $attributes->merge(['data-tiptap-id' => $id]) }}>
              Green
          </button>
        </div>
        </div>
        </div>
          

      <button class="editor-toolbar__button  hover:bg-neutral-100 p-2 rounded-l focus:outline-none"
        data-tiptap-button="horizontal" type="button" {{ $attributes->merge(['data-tiptap-id' => $id]) }}><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-dash-lg" viewBox="0 0 16 16">
      <path fill-rule="evenodd" d="M2 8a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11A.5.5 0 0 1 2 8Z"/></svg></button>
    
      <button class="editor-toolbar__button ml-3  hover:bg-neutral-100 p-2 rounded-l focus:outline-none"
        data-tiptap-button="link" type="button" {{ $attributes->merge(['data-tiptap-id' => $id]) }}><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-link-45deg" viewBox="0 0 16 16">
      <path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1.002 1.002 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4.018 4.018 0 0 1-.128-1.287z"/>
      <path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243L6.586 4.672z"/></svg></button>

      <!-- <button class="editor-toolbar__button ml-4  bg-neutral-50 hover:bg-neutral-100 px-2  focus:outline-none rounded " data-tiptap-button="text-color" type="button"></button> -->

      
      <input type="file" data-tiptap-button="image-upload-input" style="display: none" accept="image/*"  {{ $attributes->merge(['data-tiptap-id' => $id]) }}>
      <button class="editor-toolbar__button hover:bg-neutral-100 p-2 rounded-l focus:outline-none"
      data-tiptap-button="image-upload" type="button"  {{ $attributes->merge(['data-tiptap-id' => $id]) }}>
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-card-image" viewBox="0 0 16 16">
      <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
      <path d="M1.5 2A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13zm13 1a.5.5 0 0 1 .5.5v6l-3.775-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12v.54A.505.505 0 0 1 1 12.5v-9a.5.5 0 0 1 .5-.5h13z"/></svg></button>

      <button class="editor-toolbar__button   hover:bg-neutral-100 p-2 rounded-l focus:outline-none"
      data-tiptap-button="hardbreak" type="button" {{ $attributes->merge(['data-tiptap-id' => $id]) }}><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-return-left" viewBox="0 0 16 16">
      <path fill-rule="evenodd" d="M14.5 1.5a.5.5 0 0 1 .5.5v4.8a2.5 2.5 0 0 1-2.5 2.5H2.707l3.347 3.346a.5.5 0 0 1-.708.708l-4.2-4.2a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 8.3H12.5A1.5 1.5 0 0 0 14 6.8V2a.5.5 0 0 1 .5-.5z"/></svg></button>
    
    </div>
      </div>
      <div class="element prose "  element-editor >
     <input type="hidden"  {{ $attributes->merge([ 'value' => $value, 'name' => $name ]) }}>
   </div>
</div>  


<style>
  
 .prose
 {
  
  max-width: 100%;
  --tw-prose-links: #3498DB;
 }
.blockquote {
  /* background-color: red;  */
  padding: 4rem;
  border-left: 3px solid rgba(#0D0D0D, 0.1);; 
  margin: 4;
}
.tiptap p.is-editor-empty:first-child::before {
  color: #adb5bd;
  content: attr(data-placeholder);
  float: left;
  height: 0;
  pointer-events: none;
  font-style: italic; 
}

.dropdown {
  display: inline-block;
}


.dropdown:hover .dropdown-content {
  display: block;
}

p {
  margin: 1em 0;
}
</style>

