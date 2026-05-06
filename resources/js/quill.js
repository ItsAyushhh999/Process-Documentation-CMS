import Quill from 'quill';
import ImageResize from 'quill-image-resize-module';
import '../css/editor.css';
// import StQuote from './stquote.js';
// Quill.register('modules/stquote', StQuote);
window.Quill = Quill;

import StQuote from './stquote.js';
Quill.register({'formats/StQuote':StQuote}, true);


// let Parchment = Quill.import('parchment');

Quill.register('modules/imageResize', ImageResize);
// console.log("quill", Parchment)
var toolbarOptions = [
    ['bold', 'italic', 'underline', 'strike', ],        // toggled buttons
    ['code-block'],
    [{'StQuote': ['red', 'blue', 'yellow']}],
    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
    [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
    [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
    [{ 'align': [] }],
    ['link'],
    ['clean']                                         // remove formatting button
];
function quilljs_textarea(selector = null, options = null) {
    let value;
    let container;
    if(selector) {
        var elements = Array.prototype.slice.call(document.querySelectorAll(selector));
    } else {
        var elements = Array.prototype.slice.call(document.querySelectorAll('[data-quilljs]'));
    }
    elements.forEach(function(el) {
        if(selector && el.hasAttribute("data-quilljs")) {
            return;
        }
        var type = el.type;
        if(type == 'textarea') {
            value = el.value;
            container = document.createElement('div');
            container.innerHTML = value;
            el.parentNode.insertBefore(container, el.nextSibling);
            el.style.display = "none";
            var placeholder = el.placeholder;
        } else {
            var placeholder = null;
            container = el;
        }
        if(!options) {
            var defaults = {
                modules: {
                    imageResize: {
                        displaySize: true
                      },
                    toolbar: toolbarOptions,
                },
                theme: 'snow',
                placeholder: placeholder,
            };
        } else {
            if(!options.placeholder) {
                options.placeholder = placeholder;
            }
            var defaults = options;
        }

        var editor = new Quill(container, defaults);
        editor.on('text-change', function() {
            var text = editor.root.innerHTML;
            el.value = text;
        });
    });
  }
  (function() {
    quilljs_textarea();
  })();

export default quilljs_textarea;