<template>
  <div class="relative" :id="id">
    <header
      v-if="editor"
      class="grid gap-2 p-3 bg-white border rounded rounded-b-none dark:bg-gray-800 border-gray-300 dark:border-slate-700"
      :class="[
        noBorder ? 'border-t-0 border-x-0' : 'border-b-0',
        fullHeight && 'sticky top-0 z-10'
      ]"
    >
      <div class="flex flex-wrap gap-x-5 gap-y-3">
        <template v-for="(items, index) in toolbar" :key="index">
          <div class="flex rounded bg-slate-50 dark:bg-slate-900">
            <button
              type="button"
              v-for="({ slug, option, active, icon }, idx) in items.children"
              class="px-2 py-1 transition-all rounded hover:bg-gray-100 dark:hover:bg-slate-500 text-gray-500 hover:text-gray-600"
              :class="{ 'bg-slate-200 dark:bg-slate-700': editor.isActive(active) }"
              @click="onActionClick(slug, option)"
              :key="idx"
            >
              <AppIcon :name="icon" class="w-4 h-4" />
            </button>
          </div>
        </template>
        <!-- <button
          type="button"
          class="px-2 py-1 transition-all rounded hover:bg-gray-100 dark:hover:bg-gray-400 text-gray-500 hover:text-gray-600"
          @click="convertToHTML"
        >
          <app-icon name="fa-code"/>
        </button> -->
        <!-- <button
          type="button"
          class="bg-gray-50 px-2 py-1 transition-all rounded hover:bg-gray-100 dark:hover:bg-gray-400 text-gray-500 hover:text-gray-600"
          @click="openTipTapImageInput"
        >
        <app-icon name="fa-images"/>
        </button> -->
        <input type="file" :id="`image-upload-${id}`" accept="image/*" style="display: none" />

        <div
          class="bg-gray-50 dark:bg-gray-700 transition-all rounded hover:bg-gray-100 dark:hover:bg-gray-400 text-gray-500 hover:text-gray-600 border dark:border-slate-700 flex relative"
        >
          <!-- <app-icon name="fa-quote-left"/> -->
          <button type="button" class="h-full flex items-center px-2">
            <div
              class="w-4 h-4 rounded-sm"
              :class="[
                selectedQuote == 'red'
                  ? 'bg-red-500'
                  : selectedQuote == 'yellow'
                  ? 'bg-yellow-500'
                  : 'bg-green-500'
              ]"
              @click="toggleQuote()"
            />
          </button>
          <button
            class="h-full pr-2 flex items-center cursor-pointer"
            type="button"
            @click="isQuoteColorModalOpen = !isQuoteColorModalOpen"
          >
            <app-icon
              name="fa-chevron-down"
              class="w-3 h-3 transition-transform"
              :class="isQuoteColorModalOpen ? 'rotate-180' : 'rotate-0'"
            ></app-icon>
          </button>

          <div
            v-if="isQuoteColorModalOpen"
            class="absolute top-8 right-0 bg-white border flex gap-2 p-2 rounded"
            ref="quoteColorRef"
          >
            <button
              class="bg-red-500 w-6 h-6 rounded-sm"
              @click="handleSelectQuoteColor('red')"
            ></button>
            <button
              class="bg-yellow-500 w-6 h-6 rounded-sm"
              @click="handleSelectQuoteColor('yellow')"
            ></button>
            <button
              class="bg-green-500 w-6 h-6 rounded-sm"
              @click="handleSelectQuoteColor('green')"
            ></button>
          </div>
        </div>
      </div>
      <transition>
        <div v-if="tableOptions" class="flex flex-wrap gap-1 text-xs leading-none">
          <button
            type="button"
            class="p-1 text-gray-500 border rounded border-slate-300"
            @click="
              editor.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run()
            "
          >
            Insert Table
          </button>
          <button
            type="button"
            class="p-1 text-gray-500 border rounded border-slate-300"
            @click="editor.chain().focus().addColumnBefore().run()"
            :disabled="!editor.can().addColumnBefore()"
          >
            Add Column Before
          </button>
          <button
            type="button"
            class="p-1 text-gray-500 border rounded border-slate-300"
            @click="editor.chain().focus().addColumnAfter().run()"
            :disabled="!editor.can().addColumnAfter()"
          >
            Add Column After
          </button>
          <button
            type="button"
            class="p-1 text-gray-500 border rounded border-slate-300"
            @click="editor.chain().focus().deleteColumn().run()"
            :disabled="!editor.can().deleteColumn()"
          >
            Delete Column
          </button>
          <button
            type="button"
            class="p-1 text-gray-500 border rounded border-slate-300"
            @click="editor.chain().focus().addRowBefore().run()"
            :disabled="!editor.can().addRowBefore()"
          >
            Add Row Before
          </button>
          <button
            type="button"
            class="p-1 text-gray-500 border rounded border-slate-300"
            @click="editor.chain().focus().addRowAfter().run()"
            :disabled="!editor.can().addRowAfter()"
          >
            Add Row After
          </button>
          <button
            type="button"
            class="p-1 text-gray-500 border rounded border-slate-300"
            @click="editor.chain().focus().deleteRow().run()"
            :disabled="!editor.can().deleteRow()"
          >
            Delete Row
          </button>
          <button
            type="button"
            class="p-1 text-gray-500 border rounded border-slate-300"
            @click="editor.chain().focus().deleteTable().run()"
            :disabled="!editor.can().deleteTable()"
          >
            Delete Table
          </button>
        </div>
      </transition>
    </header>
    <div
      v-if="htmlJSON"
      class="p-5 bg-white border rounded rounded-t-none dark:bg-gray-800 border-slate-300 dark:border-gray-500 hover:dark:bg-gray-700"
    >
      <pre class="text-left whitespace-break-spaces">
        <code>{{ htmlJSON }}</code>
      </pre>
    </div>

    <editor-content
      v-else
      :editor="editor"
      class="p-5 bg-white dark:bg-slate-800/50 border rounded rounded-t-none hover:dark:bg-gray-700 dark:text-gray-200 overflow-auto min-h-[180px]"
      :class="[
        fullHeight ? 'h-auto' : 'max-h-[480px] ',
        noBorder ? 'border-transparent' : 'border-slate-300 dark:border-slate-700'
      ]"
    />
    <!-- <editor-content :editor="editor" /> -->
    <div v-if="editor" class="absolute flex text-xs divide-x text-slate-400 right-3 bottom-3">
      <span class="px-2" :class="maxLimit ? limitWarning : ''">
        {{ charactersCount }} {{ maxLimit ? `/ ${maxLimit} characters` : 'characters' }}
      </span>
      <span class="px-2"> {{ wordsCount }} words </span>
    </div>
    <transition
      enter-active-class="duration-300 ease-out"
      enter-from-class="-translate-y-2 opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="duration-100 ease-in"
      leave-from-class="translate-y-0 opacity-100"
      leave-to-class="translate-y-1 opacity-0 "
    >
      <div v-show="errorMessage">
        <p class="text-sm text-red-600 text-left absolute">
          {{ errorMessage }}
        </p>
      </div>
    </transition>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, type Ref, watch, onMounted } from 'vue';
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import TextAlign from '@tiptap/extension-text-align';
import Underline from '@tiptap/extension-underline';
import Subscript from '@tiptap/extension-subscript';
import Superscript from '@tiptap/extension-superscript';
import CharacterCount from '@tiptap/extension-character-count';
import Table from '@tiptap/extension-table';
import TableCell from '@tiptap/extension-table-cell';
import TableHeader from '@tiptap/extension-table-header';
import TableRow from '@tiptap/extension-table-row';
import Link from '@tiptap/extension-link';
import Image from '@tiptap/extension-image';
// import Highlight from '@tiptap/extension-highlight';
import Blockquote from '@tiptap/extension-blockquote';
// import CodeBlock from '@tiptap/extension-code-block';
import { v4 as uuidv4 } from 'uuid';
import { onClickOutside } from '@vueuse/core'
import AppIcon from './AppIcon.vue';

const props = withDefaults(
  defineProps<{
    modelValue: String;
    maxLimit?: number | null | undefined;
    fullHeight?: boolean;
    noBorder?: boolean;
    errorMessage?: string | null
  }>(),
  {
    fullHeight: false,
    noBorder: false,
    errorMessage: null
  }
);
const emit = defineEmits(['update:modelValue']);
interface Item {
  slug: string;
  option?: string | null | undefined; // We assume `option` exists in the `Item` type.
  active: string | object | null | undefined;
  icon: string;
}
interface ItemGroup {
  children: Item[];
}

// const tipTapImageInput = ref(null);
const id = uuidv4();

const isQuoteColorModalOpen = ref(false);
const selectedQuote = ref('red');
const tableOptions = ref(false);
const htmlJSON = ref(null);
const quoteColorRef = ref(null);
const toolbar: Ref<ItemGroup[]> = ref([
  {
    children: [
      { slug: 'bold', icon: 'fa-bold', active: 'bold' },
      { slug: 'italic', icon: 'fa-italic', active: 'italic' },
      { slug: 'underline', icon: 'fa-underline', active: 'underline' },
      { slug: 'strike', icon: 'fa-strikethrough', active: 'strike' }
    ]
  },
  // {
  //   children: [
  //     { slug: 'blockquote', icon: 'fa-quote-left', active: 'blockquote'}
  //   ]
  // },
  {
    children: [
      { slug: 'align', option: 'left', icon: 'fa-align-left', active: { textAlign: 'left' } },
      {
        slug: 'align',
        option: 'center',
        icon: 'fa-align-center',
        active: { textAlign: 'center' }
      },
      {
        slug: 'align',
        option: 'right',
        icon: 'fa-align-right',
        active: { textAlign: 'right' }
      },
      {
        slug: 'align',
        option: 'justify',
        icon: 'fa-align-justify',
        active: { textAlign: 'justify' }
      }
    ]
  },
  {
    children: [
      { slug: 'bulletList', icon: 'fa-list-ul', active: 'bulletList' },
      { slug: 'orderedList', icon: 'fa-list-ol', active: 'orderedList' }
    ]
  },
  {
    children: [
      { slug: 'subscript', icon: 'fa-subscript', active: 'subscript' },
      { slug: 'superscript', icon: 'fa-superscript', active: 'superscript' }
    ]
  },
  {
    children: [{ slug: 'table', icon: 'fa-table', active: 'table' }]
  },
  {
    children: [
      { slug: 'html', icon: 'fa-code', active: 'html' },
      { slug: 'link', icon: 'fa-link', active: 'link' },
      { slug: 'image', icon: 'fa-images' }
    ]
  },
  {
    children: [
      { slug: 'undo', icon: 'fa-rotate-left', active: 'undo' },
      { slug: 'redo', icon: 'fa-rotate-right', active: 'redo' }
    ]
  }
]);
const CustomTableCell = TableCell.extend({
  addAttributes() {
    return {
      // extend the existing attributes …
      ...this.parent?.(),

      // and add a new one …
      backgroundColor: {
        default: null,
        parseHTML: (element) => element.getAttribute('data-background-color'),
        renderHTML: (attributes) => {
          return {
            'data-background-color': attributes.backgroundColor,
            style: `background-color: ${attributes.backgroundColor}`
          };
        }
      }
    };
  }
});
const CustomBlockquote = Blockquote.extend({
  addAttributes() {
    return {
      class: {
        default: null,
        renderHTML: (attributes) => {
          return {
            class: `${attributes.class}`
          };
        }
      }
    };
  }
});

const editor: any = useEditor({
  content: props.modelValue,
  extensions: [
    StarterKit,
    Underline,
    Subscript,
    Superscript,
    Table.configure({
      resizable: true
      // HTMLAttributes: {
      //   class: 'not-prose',
      // },
    }),
    TableRow,
    TableHeader,
    // Default TableCell
    // TableCell,
    // Custom TableCell with backgroundColor attribute
    CustomTableCell,
    TextAlign.configure({
      types: ['heading', 'paragraph'],
      alignments: ['left', 'center', 'right', 'justify']
    }),
    CharacterCount.configure({
      limit: props.maxLimit || undefined
    }),
    Link.configure({
      // openOnClick: false
    }),

    Image.configure({
      inline: true,
      allowBase64: true
    }),
    CustomBlockquote
    // CodeBlock
  ],
  editorProps: {
    attributes: {
      class: 'prose prose-ul:mt-0 prose-p:m-0.5 prose-li:m-0 prose-lg max-w-full min-h-[140px] w-full h-full focus:outline-none  prose-a:text-sky-500'
    }
  },
  onUpdate: () => {
    const isEmpty = () => !editor.value.state.doc.textContent.trim().length;
    const inputValue = isEmpty() ? '' : editor.value.getHTML();
    emit('update:modelValue', inputValue);
  }
});
const modelValue = ref(props.modelValue);
// const previousUrl = editor.value.$el.getAttributes('link').href
// const url = window.prompt('URL', previousUrl)

watch(modelValue, (newValue) => {
  if (editor.value && editor.value.getHTML() === newValue) return; // Added null check
  editor.value?.commands.setContent(newValue, false); // Added null check
});

watch(
  () => props.modelValue,
  () => {
    console.log('changed');
    modelValue.value = props.modelValue;
  }
);
const onActionClick = (slug: any, option: string | null = null) => {
  const vm = editor.value.chain().focus();
  const actionTriggers: any = {
    bold: () => vm.toggleBold().run(),
    italic: () => vm.toggleItalic().run(),
    underline: () => vm.toggleUnderline().run(),
    strike: () => vm.toggleStrike().run(),
    bulletList: () => vm.toggleBulletList().run(),
    orderedList: () => vm.toggleOrderedList().run(),
    align: () => vm.setTextAlign(option).run(),
    subscript: () => vm.toggleSubscript().run(),
    superscript: () => vm.toggleSuperscript().run(),
    undo: () => vm.undo().run(),
    redo: () => vm.redo().run(),
    table: () => {
      // console.log('Table')
      tableOptions.value = !tableOptions.value;
    },
    clear: () => {
      vm.clearNodes().run();
      vm.unsetAllMarks().run();
    },
    link: () => {
      const previousUrl = editor.value.getAttributes('link').href;
      const url = window.prompt('URL', previousUrl);

      if (url === null) {
        return;
      }
      if (url === '') {
        editor.value.chain().focus().extendMarkRange('link').unsetLink().run();

        return;
      }

      // update link
      editor.value.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
    },
    image: () => {
      openTipTapImageInput();
    },
    html: () => vm.toggleCodeBlock().run()
    // blockquote: () => {
    //   console.log('block');
    //   editor.value.chain().focus().setBlockquote().run();
    // }
  };

  actionTriggers[slug]();
};
const convertToHTML = () => {
  const html = editor.value.getHTML();
  if (htmlJSON.value !== null) {
    htmlJSON.value = null;
    editor.value.commands.setContent(html);
  } else {
    htmlJSON.value = html;
  }
};
const charactersCount = computed(() => {
  return editor.value?.storage.characterCount.characters() || 0; // Added null check and default value
});

const wordsCount = computed(() => {
  return editor.value?.storage.characterCount.words() || 0; // Added null check and default value
});

const limitWarning = computed(() => {
  const isCloseToMax = charactersCount.value >= (props.maxLimit || 0) - 20; // Added a default value of 0 for maxLimit to avoid 'undefined' type
  const isMax = charactersCount.value === (props.maxLimit || 0);

  if (isCloseToMax && !isMax) return 'warning';
  if (isMax) return 'danger';

  return '';
});
onMounted(() => {
  editor.value?.commands.setContent(modelValue.value, false);
});
const openTipTapImageInput = () => {
  let imageUploader = document.getElementById(`image-upload-${id}`);
  imageUploader?.click();
  imageUploader?.addEventListener('change', (e) => {
    let file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function () {
        const imageUrl = reader.result;
        editor.value.chain().focus().setImage({ src: imageUrl }).run();
      };
      reader.readAsDataURL(file);
    }
    imageUploader.value = [];
  });
};

const handleSelectQuoteColor = (color: string) => {
  isQuoteColorModalOpen.value = false;
  selectedQuote.value = color;
  toggleQuote();
};

const toggleQuote = () => {
  // selectedQuote.value = color
  console.log('toggled');
  // editor.value.commands.updateAttributes('blockquote', {class:'bg-red-500'});
  editor.value.chain().focus().toggleBlockquote().run();
  const color =
    selectedQuote.value == 'red'
      ? 'blockquote-red'
      : selectedQuote.value == 'yellow'
      ? 'blockquote-red'
      : 'blockquote-green';
  editor.value.commands.updateAttributes('blockquote', { class: color });
};

onClickOutside(quoteColorRef, () => isQuoteColorModalOpen.value= false)
</script>
<style>
.v-enter-active,
.v-leave-active {
  transition: opacity 0.5s ease;
}

.v-enter-from,
.v-leave-to {
  opacity: 0;
}

#app .tableWrapper > table tbody tr th {
  @apply bg-slate-50;
}

#app .tableWrapper > table tbody tr th,
#app .tableWrapper > table tbody tr td {
  @apply border border-slate-300 px-3 py-2;
}

#app .tableWrapper > table tbody tr th p,
#app .tableWrapper > table tbody tr td p {
  @apply m-0;
}

.prose {
  @apply dark:text-slate-200;
}
.prose p {
  @apply dark:text-slate-200 break-words ;
}
.prose strong {
  @apply dark:text-slate-200;
}
.prose blockquote {
  @apply !border-l-4 !py-4 !m-0 !rounded-r-lg;
}

.prose .blockquote-green {
  @apply !bg-green-50 !border-green-500 !text-green-700;
}
.prose .blockquote-red {
  @apply !bg-red-50 !border-red-500 !text-red-700;
}
.prose .blockquote-yellow {
  @apply !bg-yellow-50 !border-yellow-500 !text-yellow-700;
}
</style>
