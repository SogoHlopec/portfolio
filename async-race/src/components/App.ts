import { Header } from "./Header";
import { Garage } from "./Garage";

class App {
  body: HTMLTemplateElement | null;
  header: Header;
  garage: Garage;

  constructor() {
    this.body = document.querySelector<HTMLTemplateElement>("body");
    this.header = new Header();
    this.garage = new Garage();
  }

  start() {
    this.header.render();
    this.garage.render();
  }
}

export { App };
