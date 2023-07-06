import { CreateElement } from "../../modules/CreateElement";
import { Board } from "../board/Board";
import { CssEditor } from "../css-editor/CssEditor";
import { HtmlViewer } from "../html-viewer/HtmlViewer";
import { Levels } from "../levels/Levels";

class GameWrapper {
  main: HTMLTemplateElement | null;

  constructor() {
    this.main = document.querySelector<HTMLTemplateElement>(".main");
  }

  createHtml() {
    const gameWrapper: CreateElement = new CreateElement("div", "game-wrapper");
    this.main?.append(gameWrapper.getElement());

    const board: Board = new Board(gameWrapper);
    board.createBoard(1);

    const codeWrapper: CreateElement = new CreateElement("div", "code-wrapper");
    gameWrapper.appendElement(codeWrapper.getElement());
    const cssEditor = new CssEditor(codeWrapper);
    cssEditor.createCssEditor(1);

    const htmlViewer = new HtmlViewer(codeWrapper);
    htmlViewer.createCssEditor(1);
    // TODO Create block 4 Levels
    const levels = new Levels();
    levels.createHtml();
  }
}

export { GameWrapper };
