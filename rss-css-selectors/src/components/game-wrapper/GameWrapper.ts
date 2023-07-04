import { CreateElement } from "../../modules/CreateElement";
import { Board } from "../board/Board";
import { CssEditor } from "../css-editor/CssEditor";

class GameWrapper {
  main: HTMLTemplateElement | null;

  constructor() {
    this.main = document.querySelector<HTMLTemplateElement>(".main");
  }

  createHtml() {
    if (this.main) {
      const gameWrapper: CreateElement = new CreateElement(
        "div",
        "game-wrapper"
      );
      this.main.append(gameWrapper.getElement());

      const board: Board = new Board(gameWrapper);
      board.createBoard(1);

      const codeWrapper: CreateElement = new CreateElement(
        "div",
        "code-wrapper"
      );
      gameWrapper.appendElement(codeWrapper.getElement());
      // TODO create block 2 CSS editor
      const cssEditor = new CssEditor(codeWrapper);
      cssEditor.createCssEditor(1);

      // TODO create block 3 HTML Viewer
      // TODO Create block 4 Levels
    }
  }
}

export { GameWrapper };
