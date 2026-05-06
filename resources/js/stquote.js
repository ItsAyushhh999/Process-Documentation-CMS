import Quill from 'quill';
let Inline = Quill.import('blots/inline');

export default class StQuote extends Inline {
  static create(elem){
    let node = super.create(elem);
      if(String(elem).includes('ql-span-')){
        node.setAttribute('class', `${elem}`)
          return node;
      }else{
        node.setAttribute('class', `ql-span-${elem}-note`);
        return node;
      } 
  }
  static formats(domNode) {
    return domNode.getAttribute('class') || true;
  }

  format(name, value) {
    if (name === 'StQuote' && value) {
      this.domNode.setAttribute('class', `ql-span-${value}-note`);
    } else {
      super.format(name, value);
    }
  }
  // formats() {
  //   let formats = super.formats();
  //   formats['StQuote'] = StQuote.formats(this.domNode);
  //   return formats;
  // }
}

StQuote.blotName = 'StQuote';
StQuote.tagName = 'span';
