class CreateElement {
  element: HTMLElement;
  selector: string | undefined;

  constructor(element: string, selector?: string) {
    this.element = document.createElement(element);
    this.selector = selector;
  }

  setClassSelector(classSelector: string): void {
    this.element.classList.add(classSelector);
  }

  setInnerText(text: string): void {
    this.element.innerText = text;
  }

  getElement(): HTMLElement {
    if (this.selector) this.element.classList.add(this.selector);
    return this.element;
  }

  prependElement(element: HTMLElement): void {
    this.element.prepend(element);
  }

  appendElement(element: HTMLElement): void {
    this.element.append(element);
  }
}
export { CreateElement };
