import { CreateElement } from "../../modules/CreateElement";
import { dataLevels } from "../levels/dataLevels";

class CssEditor {
  wrapper: CreateElement;

  constructor(wrapper: CreateElement) {
    this.wrapper = wrapper;
  }

  createCssEditor(levelIndex: number): void {
    const level = dataLevels[levelIndex - 1];
    console.log(level);

    const editor = new CreateElement("section", "editor");
    this.wrapper.appendElement(editor.getElement());

    const header = new CreateElement("div", "editor__header");
    editor.appendElement(header.getElement());
    header.getElement().innerHTML = `CSS Editor <div class="editor__file-name">style.css</div>`;

    const code = new CreateElement("div", "editor__code");
    editor.appendElement(code.getElement());

    const lineNumbers = new CreateElement("ul", "code__line-numbers");
    code.appendElement(lineNumbers.getElement());

    for (let i = 0; i < 20; i++) {
      const line = new CreateElement("li", "line-number");
      line.setInnerText(`${i + 1}`);
      lineNumbers.appendElement(line.getElement());
    }

    const codeWindow = new CreateElement("div", "code__window");
    code.appendElement(codeWindow.getElement());

    const inputWrapper = new CreateElement("div", "code__input-wrapper");
    codeWindow.appendElement(inputWrapper.getElement());

    const input = new CreateElement("input", "code__input");
    input.getElement().setAttribute("type", "text");
    input.getElement().setAttribute("placeholder", "Type in a CSS selector");
    inputWrapper.appendElement(input.getElement());

    const submit = new CreateElement("button", "code__submit");
    submit.setInnerText("Enter");
    inputWrapper.appendElement(submit.getElement());

    const comments = new CreateElement("pre", "code__comments");
    comments.setInnerText(`  {
      /* Styles would go here. */
  }

  /*
  Type a number to skip to a level.
  Ex → "5" for level 5
  */`);
    codeWindow.appendElement(comments.getElement());
  }
}

export { CssEditor };
