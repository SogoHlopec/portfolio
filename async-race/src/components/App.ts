import { Header } from "./Header";
import { Garage } from "./Garage";
import { Winners } from "./Winners";
class App {
  body: HTMLTemplateElement | null;
  header: Header;
  garage: Garage;
  winners: Winners;

  constructor() {
    this.body = document.querySelector<HTMLTemplateElement>("body");
    this.header = new Header();
    this.garage = new Garage();
    this.winners = new Winners();
  }

  start() {
    this.header.render();
    this.garage.render();
    this.winners.render();
  }
}

export { App };
