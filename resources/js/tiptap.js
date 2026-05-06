import { Editor } from '@tiptap/core'
import StarterKit from '@tiptap/starter-kit'
import Underline from '@tiptap/extension-underline';
import CodeBlock from '@tiptap/extension-code-block';
import Heading from '@tiptap/extension-heading';
import TextAlign from '@tiptap/extension-text-align';
import Link from '@tiptap/extension-link';
import Highlight from '@tiptap/extension-highlight';
import OrderedList from '@tiptap/extension-ordered-list';
import BulletList from '@tiptap/extension-bullet-list';
import Blockquote from '@tiptap/extension-blockquote'; 
import HorizontalRule from '@tiptap/extension-horizontal-rule';
import Typography from '@tiptap/extension-typography';
import Image from '@tiptap/extension-image';
import Placeholder from '@tiptap/extension-placeholder'
import HardBreak from '@tiptap/extension-hard-break';


var testEditor = document.querySelectorAll('.editor')

// console.log('testEditor', testEditor[0].getElementsByClassName('element'))

// testEditor.forEach()

// var elements = document.querySelectorAll('.element')


const editors = [];
// console.log(elements)
// for (let i = 0, len = elements.length; i < len; i++) {
  // console.log(i)
  
  function updateDescriptionInput(editor, inputElement) {
    const content = editor.getHTML();
    inputElement.value = content; 
  }
  
  // console.log('editor', elements)

  testEditor.forEach((ele, index) => {
    let uiid = ele.id;
    let element =  ele.children[1]
    const boldBtnUid = `[data-tiptap-button="bold"][data-tiptap-id="${uiid}"]`
    const italicBtnUid = `[data-tiptap-button="italic"][data-tiptap-id="${uiid}"]`
    const underlineBtnUid = `[data-tiptap-button="underline"][data-tiptap-id="${uiid}"]`
    const strikeBtnUid = `[data-tiptap-button="strike"][data-tiptap-id="${uiid}"]`
    const orderedListBtnUid = `[data-tiptap-button="ordered-list"][data-tiptap-id="${uiid}"]`
    const bulletListBtnUid = `[data-tiptap-button="bullet-list"][data-tiptap-id="${uiid}"]`
    const textAlignBtnUid = `[data-tiptap-button="alignment"][data-tiptap-id="${uiid}"]`
    // const codeBtnUid = `[data-tiptap-button="code"][data-tiptap-id="${uiid}"]`
    const codeBlockBtnUid = `[data-tiptap-button="code-block"][data-tiptap-id="${uiid}"]`
    const h1BtnUid = `[data-tiptap-button="heading[1]"][data-tiptap-id="${uiid}"]`
    const h2BtnUid = `[data-tiptap-button="heading[2]"][data-tiptap-id="${uiid}"]`
    const h3BtnUid = `[data-tiptap-button="heading[3]"][data-tiptap-id="${uiid}"]`
    const blockquoteBtnUid = `[data-tiptap-button="blockquote_red"][data-tiptap-id="${uiid}"]`
    const blockquote1BtnUid = `[data-tiptap-button="blockquote_yellow"][data-tiptap-id="${uiid}"]`
    const blockquote2BtnUid = `[data-tiptap-button="blockquote_green"][data-tiptap-id="${uiid}"]`
    const horizontalRuleUid = `[data-tiptap-button="horizontal"][data-tiptap-id="${uiid}"]`
    const linkBtnUid = `[data-tiptap-button="link"][data-tiptap-id="${uiid}"]`
    const highlightBtnUid = `[data-tiptap-button="highlight"][data-tiptap-id="${uiid}"]`
    const highlightColorPickerBtnUid =  `[data-tiptap-button="highlight-color-picker"][data-tiptap-id="${uiid}"]`
    const imageUploadBtnUid =`[data-tiptap-button="image-upload"][data-tiptap-id="${uiid}"]`
    const imageUploadInputBtnUid =`[data-tiptap-button="image-upload-input"][data-tiptap-id="${uiid}"]`
    const HardBreakBtnUid =`[data-tiptap-button="hardbreak"][data-tiptap-id="${uiid}"]`
    // console.log('block', blockquoteBtnUid)
    const button ={
      bold: document.querySelector(boldBtnUid),
      italic: document.querySelector(italicBtnUid),
      underline: document.querySelector(underlineBtnUid),
      strike: document.querySelector(strikeBtnUid),
      orderedList: document.querySelector(orderedListBtnUid),
      bulletList: document.querySelector(bulletListBtnUid),
      textAlign : document.querySelector(textAlignBtnUid),
      codeBlock: document.querySelector(codeBlockBtnUid),
      // code: document.querySelector(codeBtnUid),
      h1: document.querySelector(h1BtnUid),
      h2: document.querySelector(h2BtnUid),
      h3: document.querySelector(h3BtnUid),
      blockquote: document.querySelector(blockquoteBtnUid),
      blockquote1: document.querySelector(blockquote1BtnUid),
      blockquote2: document.querySelector(blockquote2BtnUid),
      horizontalRule: document.querySelector(horizontalRuleUid),
      link: document.querySelector(linkBtnUid),
      highlight: document.querySelector(highlightBtnUid),
      highlightColorPicker: document.querySelector(highlightColorPickerBtnUid),
      imageUpload: document.querySelector(imageUploadBtnUid),
      imageUploadInput: document.querySelector(imageUploadInputBtnUid),
      HardBreak: document.querySelector(HardBreakBtnUid),
        // const imageUploadInput = document.getElementById('image-upload-input');

    }
    
    const CustomBlockquote = Blockquote.extend({
      addAttributes(){
        return{
          class:{
            default:null,
            renderHTML: attributes => {
              return{
                class:`${attributes.class}`,
              }
            }
          }
        }
      }
    })
    
    // console.log('elem',ele.id)
    const editor = new Editor({
      element: element,
      extensions: [
        StarterKit,
        CodeBlock.configure({}),
        Heading.configure({ levels: [1, 2, 3] }), 
        TextAlign.configure({
        types: ['heading', 'paragraph'], 
        alignments: ['left','right','center'],
        }),
        Link.configure({}),
        Highlight.configure({multicolor: true}),
        Underline.configure({}),
        OrderedList.configure({}),
        BulletList.configure({}),
        Blockquote.configure({
          HTMLAttributes: {
            class: 'blockquote',

          },
        }),
        
        CustomBlockquote,
        HorizontalRule.configure({}),
        Typography.configure({}),
        Image.configure({
          inline: true,
          allowBase64: true,
        }), 
        Placeholder.configure({
          placeholder: 'Please enter text',
        }),
        HardBreak.configure({}),
        
      ],
    content: element.querySelector('input').value
    });

    // console.log('element', element.querySelector('input').value)


    const starterKitDiv = ele.querySelector('.ProseMirror');
    starterKitDiv.classList.add(
      'px-5',
      'py-3',
      'border',
      'overflow-y-auto',
      'w-full',
      'block',
      'focus:border-blue-300',
      'focus:ring',
      'focus:ring-blue-200',
      'focus:ring-opacity-50',
      'dark:focus:ring-gray-700',
      'rounded-b',
      'border-gray-300',
      'dark:border-gray-700',
      'dark:bg-stone-800',
      'dark:text-slate-200',
      'focus:outline-none'
    );

    const height = ele.getAttribute('height') || '250px'; 

    if (starterKitDiv) {
        starterKitDiv.style.height = height; 
    }
    // editors.push(editor); 
   
    const setButtonActive = (button, mark, editor) => {
      if (editor.isActive(mark)) {
        button.classList.add('active');
      } else{
        button.classList.remove('active')
      }
    };
    // if(button.blod){
      button.bold.addEventListener('click', () => {
        editor.chain().focus().toggleBold().run();
        setButtonActive(button.bold, 'bold', editor);
      });
      // }
      
      // if(button.italic){
        button.italic.addEventListener('click', () => {
          editor.chain().focus().toggleItalic().run();
          setButtonActive(button.italic,'italic', editor);
          
        })
        // }
        
        // if(button.underline){
          button.underline.addEventListener('click', () => {
              if (editor.isActive('underline')) {
                editor.chain().focus().unsetUnderline().run(); 
              } else {
                editor.chain().focus().setUnderline().run(); 
              }
              setButtonActive(button.underline, 'underline', editor);
            });
        // }

        // if(button.strike){
          button.strike.addEventListener('click', () => {
            editor.chain().focus().toggleStrike().run()
            setButtonActive(button.strike, 'strike', editor)
          })
        // }


        // if (button.textAlign) {
          let currentAlignment = 'left';
        
          button.textAlign.addEventListener('click', () => {
              // Toggle between 'center' and 'left' alignments
              currentAlignment = currentAlignment === 'center' ? 'left' : 'center';
        
              editor.chain().focus().setTextAlign(currentAlignment).run();
        
              if (currentAlignment === 'center') {
                setButtonActive(button.textAlign, 'alignment', editor);
              } else {
                setButtonInactive(button.textAlign, 'alignment', editor);
              }
          });
        // }

        // if(button.code){
          // button.code.addEventListener('click', ()=> {
          //   editor.chain().focus().toggleCode().run()
          //   setButtonActive(button.code, ' code', editor)
          // })
        // }

        // if(button.codeBlock){
          button.codeBlock.addEventListener('click', () => {
              editor.chain().focus().toggleCodeBlock().run();
          });
        // }

        // if(button.h1){
          button.h1.addEventListener('click', () => {
              editor.chain().focus().toggleHeading({ level: 1 }).run();
              setButtonActive(button.h1, 'heading[1]', editor);
          });
        // }
        
        // if(button.h2){
          button.h2.addEventListener('click', () => {
              editor.chain().focus().toggleHeading({ level: 2 }).run();
              setButtonActive(button.h2, 'heading[2]', editor);
          });
        // }
        
        // if(button.h3){
          button.h3.addEventListener('click', () => {
              editor.chain().focus().toggleHeading({ level: 3 }).run();
              setButtonActive(button.h3, 'heading[3]', editor);
          });
        // }

        // if(button.blockquote){
          
        // const colorOptions = ['#FF5733', '#33FF57', '#3366FF', '#FF33CC', '#FFFF33'];        
        // let currentColorIndex = 0;
        
        // button.blockquote.addEventListener('click', () => {
        //   document.body.style.backgroundColor = colorOptions[currentColorIndex];
        
        //   currentColorIndex = (currentColorIndex + 1) % colorOptions.length;
        //   editor.chain().focus().toggleBlockquote().run();
        // });

        
       
        //   button.blockquote.addEventListener('click', () => {
        //     const blockquote = editor.getClosestBlock(node => node.type.name === 'blockquote');
        
        //     const hasBackgroundColor = blockquote.hasAttribute('data-background-color');
        
        //     const backgroundColors = ['yellow', 'lightblue', 'lightgreen'];
        //     const currentColorIndex = hasBackgroundColor ? backgroundColors.indexOf(blockquote.getAttribute('data-background-color')) : -1;
        //     const nextColorIndex = (currentColorIndex + 1) % backgroundColors.length;
        
        //     if (hasBackgroundColor) {
        //         blockquote.removeAttribute('data-background-color');
        //     } else {
        //         blockquote.setAttribute('data-background-color', backgroundColors[nextColorIndex]);
        //     }
        
        //     // Run the toggleBlockquote command to update the editor state
        //     editor.chain().focus().toggleBlockquote().run();
        // });
        
      //   button.blockquote.addEventListener('click', () => {
      //     editor.chain().focus().toggleBlockquote().run();
      
      //     // Get the current blockquote element
      //     const blockquoteElement = editor.view.dom.querySelector('.blockquote');
      
      //     // Log the blockquote element and class application
      //     console.log('blockquoteElement:', blockquoteElement);
      //     if (blockquoteElement) {
      //         blockquoteElement.classList.add('red-background');
      //     }
      // });
      
      
          button.blockquote.addEventListener('click', () => {
          editor.chain().focus().toggleBlockquote().run();
          editor.commands.updateAttributes('blockquote',{class:'red-background'});

         });

          button.blockquote1.addEventListener('click', () => {
            // toggleBackgroundColor();
            editor.chain().focus().toggleBlockquote().run();
          editor.commands.updateAttributes('blockquote',{class:'yellow-background'}) 
          });

          button.blockquote2.addEventListener('click', () => {
            editor.chain().focus().toggleBlockquote().run();
            editor.commands.updateAttributes('blockquote',{class:'green-background'}) 
          });
        // }

        // if(button.horizontalRule){
          button.horizontalRule.addEventListener('click',()=>{
              editor.chain().focus().setHorizontalRule().run();
            })
        // }

        button.HardBreak.addEventListener('click', ()=> {
          editor.chain().focus().setHardBreak().run();
        });

        // if(button.link){

          button.link.addEventListener('click', () => {
              const url = prompt('Enter the URL for the link:');
              if (url) {
                editor.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
              }
          });
        // }

        // if(button.highlight){

          let isHighlightActive = false;

          button.highlight.addEventListener('click', () => {
            if (isHighlightActive) {  //  highlight is already active, unset it
             
              editor.chain().focus().unsetHighlight().run(); 
              isHighlightActive = false;
            } else {
              button.highlightColorPicker.click();
            }
          });
          
          button.highlightColorPicker.addEventListener('input', () => {
            const selectedColor = button.highlightColorPicker.value;
          
            if (selectedColor) {
              if (editor.isActive('highlight', { color: selectedColor })) {
                inline: true,
                editor.chain().focus().unsetHighlight().run();
                isHighlightActive = false;
              } else {
                editor.chain().focus().setHighlight({ color: selectedColor }).run();
                isHighlightActive = true;
              }
              setButtonActive(button.highlight, 'highlight', editor);
            }
          });

        // const imageUploadInput = document.getElementById('image-upload-input');
// if (imageUploadButton && imageUploadInput) {
  // console.log('button', button.imageUpload)
    button.imageUpload.addEventListener('click', () => {
        button.imageUploadInput.click();
    });

    button.imageUploadInput.addEventListener('change', (event) => {
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();

            reader.onload = function () {
                const imageUrl = reader.result; 
                editor.chain().focus().setImage({ src: imageUrl }).run();
            };

            reader.readAsDataURL(file);
        }
    });
// }

  editor.on('update', () => {
    updateDescriptionInput(editor,  element.querySelector('input'));

  });
  // updateDescriptionInput(editor,  element.querySelector('input'));
 // if(button.orderedList){
//   button.orderedList.addEventListener('click', () => {
//     editor.chain().focus().toggleOrderedList().run();    
// });
// }

// if(button.bulletList){
// button.bulletList.addEventListener('click', () => {
//     editor.chain().focus().toggleBulletList().run(); 
// });
// }
//   element.addEventListener('keydown', event => {
//     if (event.key === 'Enter') {
//         event.preventDefault();
//         editor.chain().focus().setHardBreak().run();
//     }
// });
let isListButtonActive = false; 

button.orderedList.addEventListener('click', () => {
    editor.chain().focus().toggleOrderedList().run();
    isListButtonActive = true; 
});

button.bulletList.addEventListener('click', () => {
    editor.chain().focus().toggleBulletList().run();
    isListButtonActive = true; 
});

element.addEventListener('keydown', event => {
  const { selection } = editor.state;

  const isInList = editor.isActive('orderedList') || editor.isActive('bulletList');

  if (!isInList && event.key === 'Enter') { 
      event.preventDefault();
      editor.chain().focus().setHardBreak().run();
  }
});



// Reset isListButtonActive when the buttons lose focus
button.orderedList.addEventListener('blur', () => {
    isListButtonActive = false;
});

button.bulletList.addEventListener('blur', () => {
    isListButtonActive = false;
});


});
  

// button.leftAlign.addEventListener('click', () => {
//   editors.forEach(editor => {
//     editor.chain().focus().setTextAlign({ align: 'left' }).run();
//   });
// });

// button.centerAlign.addEventListener('click', () => {
//   editors.forEach(editor => {
//     editor.chain().focus().setTextAlign({ align: 'center' }).run();
//   });
// });

// button.rightAlign.addEventListener('click', () => {
//   editors.forEach(editor => {
//     editor.chain().focus().setTextAlign({ align: 'right' }).run();
//   });
// });

// button.justifyAlign.addEventListener('click', () => {
//   editors.forEach(editor => {
//     editor.chain().focus().setTextAlign({ align: 'justify' }).run();
//   });
// });
