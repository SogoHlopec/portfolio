import { CreateElement } from "../../modules/CreateElement";
import { dataLevels } from "../levels/dataLevels";

class Board {
  wrapper: CreateElement;

  constructor(wrapper: CreateElement) {
    this.wrapper = wrapper;
  }

  createBoard(levelIndex: number): void {
    const level = dataLevels[levelIndex - 1];
    const board = new CreateElement("section", "board");
    this.wrapper.appendElement(board.getElement());

    const title = new CreateElement("h1", "board__title");
    board.appendElement(title.getElement());
    title.setInnerText(`${level.titleTask}`);

    const table = new CreateElement("div", "table");
    board.appendElement(table.getElement());

    table.getElement().innerHTML = level.html;
  }
}

export { Board };
