import { CreateElement } from "../utils/CreateElement";
import { API } from "../api/Api";
import { Car } from "./Car";
import { ICar } from "../api/interfaces";

class Garage {
  body: HTMLTemplateElement | null;
  main: CreateElement;

  constructor() {
    this.body = document.querySelector<HTMLTemplateElement>("body");
    this.main = new CreateElement("main", "main-garage");
  }

  private createForm(parentElement: HTMLElement) {
    const createForm = new CreateElement("form", "form");
    createForm.setClassSelector("create-form");
    const input = new CreateElement("input", "input");
    input.getElement().setAttribute("name", "name");
    input.getElement().setAttribute("type", "text");
    input.getElement().setAttribute("required", "");
    const color = new CreateElement("input", "color");
    color.getElement().setAttribute("name", "color");
    color.getElement().setAttribute("type", "color");
    color.getElement().setAttribute("value", "#ffffff");
    const submit = new CreateElement("button", "button");
    submit.setInnerText("CREATE");
    createForm.appendElement(input.getElement());
    createForm.appendElement(color.getElement());
    createForm.appendElement(submit.getElement());
    parentElement.append(createForm.getElement());
  }

  private createFormsContainer(parentElement: HTMLElement) {
    const container = new CreateElement("div", "forms-container");
    this.createForm(container.getElement());
    parentElement.append(container.getElement());
  }

  private async create(): Promise<void> {
    this.createFormsContainer(this.main.getElement());
    const title: CreateElement = new CreateElement("h2", "main__title");
    const Cars = await API.getCars(1);
    const counterCars: string | null = Cars.count;
    title.setInnerText(`GARAGE(${counterCars})`);
    this.main.appendElement(title.getElement());

    const page: CreateElement = new CreateElement("p", "main__page-number");
    page.setInnerText("Page #1");
    this.main.appendElement(page.getElement());

    Cars.items.forEach((item: ICar) => {
      const car = new Car(item);
      car.render(this.main.getElement(), item);
    });
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
