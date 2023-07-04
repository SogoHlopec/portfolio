import { CreateElement } from "../../modules/CreateElement";
import { dataLevels } from "../levels/dataLevels";

class HtmlViewer {
  wrapper: CreateElement;

  constructor(wrapper: CreateElement) {
    this.wrapper = wrapper;
  }

  createCssEditor(levelIndex: number): void {
    const level = dataLevels[levelIndex - 1];
    console.log(level);

    const editor = new CreateElement("section", "viewer");
    this.wrapper.appendElement(editor.getElement());

    const header = new CreateElement("div", "viewer__header");
    editor.appendElement(header.getElement());
    header.getElement().innerHTML = `HTML Viewer <div class="viewer__file-name">table.html</div>`;

    const code = new CreateElement("div", "viewer__code");
    editor.appendElement(code.getElement());

    const lineNumbers = new CreateElement("ul", "code__line-numbers");
    code.appendElement(lineNumbers.getElement());

    for (let i = 0; i < 20; i++) {
      const line = new CreateElement("li", "line-number");
      line.setInnerText(`${i + 1}`);
      lineNumbers.appendElement(line.getElement());
    }

    const codeWindow = new CreateElement("pre", "code__window");
    code.appendElement(codeWindow.getElement());
    codeWindow.setInnerText(`${level.htmlForViewer}`);
  }
}

export { HtmlViewer };
