import { CreateElement } from "../../modules/CreateElement";
import { dataLevels } from "./dataLevels";

class Levels {
  main: HTMLTemplateElement | null;

  constructor() {
    this.main = document.querySelector<HTMLTemplateElement>(".main");
  }

  createHtml(): void {
    const levels = new CreateElement("section", "levels");
    this.main?.append(levels.getElement());

    const title = new CreateElement("h2", "levels__title");
    title.setInnerText("Level");
    levels.appendElement(title.getElement());

    const listLevels = new CreateElement("ul", "levels__list");
    levels.appendElement(listLevels.getElement());

    for (let i = 0; i < dataLevels.length; i++) {
      const listItem = new CreateElement("li", "list__item");
      listItem.setInnerText(`${dataLevels[i].level}`);
      listLevels.appendElement(listItem.getElement());
    }

    const reset = new CreateElement("button", "levels__reset");
    reset.setInnerText("Reset");
    levels.appendElement(reset.getElement());
  }
}

export { Levels };
