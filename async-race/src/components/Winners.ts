import { CreateElement } from "../utils/CreateElement";
import { API } from "../api/Api";

class Winners {
  main: CreateElement;

  constructor() {
    this.main = new CreateElement("main", "main");
  }

  private async create() {
    this.main.setClassSelector("main-winners");

    const title = new CreateElement("h2", "main__title");
    const counterWinners = await (await API.getWinners(1)).count;
    title.setInnerText(`WINNERS(${counterWinners})`);
    this.main.appendElement(title.getElement());

    const page = new CreateElement("p", "main__page");
    page.setClassSelector("page");
    page.setInnerText("Page #1");
    this.main.appendElement(page.getElement());
  }

  async updateCounterWinners(page = 1) {
    const title = this.main
      .getElement()
      .querySelector<HTMLTemplateElement>(".main__title");
    const counterWinners = (await API.getWinners(page)).count;
    if (title) title.innerText = `GARAGE(${counterWinners})`;
  }

  render() {
    this.create();
  }
}

export { Winners };
