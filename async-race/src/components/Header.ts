import { CreateElement } from "../utils/CreateElement";
import { app } from "../index";

class Header {
  header: CreateElement;
  body: HTMLTemplateElement | null;

  constructor() {
    this.body = document.querySelector<HTMLTemplateElement>("body");
    this.header = new CreateElement("header", "header");
  }

  private create(): void {
    const title: CreateElement = new CreateElement("h1", "title");
    title.setInnerText("ASYNC RACE");
    this.header.appendElement(title.getElement());

    const btnGarage: CreateElement = new CreateElement("button", "btn");
    btnGarage.setClassSelector("btn__garage");
    btnGarage.setInnerText("Garage");
    this.header.appendElement(btnGarage.getElement());

    const btnWinners: CreateElement = new CreateElement("button", "btn");
    btnWinners.setClassSelector("btn__winners");
    btnWinners.setInnerText("Winners");
    this.header.appendElement(btnWinners.getElement());
  }

  private event(): void {
    document
      .querySelector<HTMLTemplateElement>(".btn__winners")
      ?.addEventListener("click", () => {
        const currentMain: HTMLTemplateElement | null =
          document.querySelector<HTMLTemplateElement>(".main");
        const newMain: HTMLElement = app.winners.main.getElement();
        if (currentMain) this.body?.replaceChild(newMain, currentMain);
      });

    document
      .querySelector<HTMLTemplateElement>(".btn__garage")
      ?.addEventListener("click", () => {
        const currentMain: HTMLTemplateElement | null =
          document.querySelector<HTMLTemplateElement>(".main");
        const newMain: HTMLElement = app.garage.main.getElement();
        if (currentMain) this.body?.replaceChild(newMain, currentMain);
      });
  }

  render(): void {
    this.create();
    if (app.body) {
      app.body.append(this.header.getElement());
    }
    this.event();
  }
}

export { Header };
