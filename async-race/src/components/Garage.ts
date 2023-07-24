import { CreateElement } from "../utils/CreateElement";
import { API } from "../api/Api";

class Garage {
  body: HTMLTemplateElement | null;
  main: CreateElement;

  constructor() {
    this.body = document.querySelector<HTMLTemplateElement>("body");
    this.main = new CreateElement("main", "main");
  }

  private async create(): Promise<void> {
    this.main.setClassSelector("main-garage");

    const title: CreateElement = new CreateElement("h2", "main__title");
    const counterCars: string | null = (await API.getCars(1)).count;
    title.setInnerText(`GARAGE(${counterCars})`);
    this.main.appendElement(title.getElement());

    const page: CreateElement = new CreateElement("p", "main__page");
    page.setClassSelector("page");
    page.setInnerText("Page #1");
    this.main.appendElement(page.getElement());
  }

  async updateCounterCars(page = 1): Promise<void> {
    const title: HTMLTemplateElement | null = this.main
      .getElement()
      .querySelector<HTMLTemplateElement>(".main__title");
    const counterCars: string | null = (await API.getCars(page)).count;
    if (title) title.innerText = `GARAGE(${counterCars})`;
  }

  render(): void {
    this.create();
    if (this.body) this.body.append(this.main.getElement());
  }
}

export { Garage };
