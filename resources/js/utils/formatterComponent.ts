import { createVNode, render } from 'vue'
interface RenderComponentOptions {
  component: any
  props: Record<string, any>
}
export default function renderComponent({ component, props }: RenderComponentOptions): HTMLElement {
  const vNode: any = createVNode(component, props)
  const el: HTMLElement = document.createElement('div')
  render(vNode, el)
  return el
}
