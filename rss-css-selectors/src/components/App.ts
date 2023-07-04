import { CreateElem } from "../modules/CreateElement";

class App {
  body: HTMLElement = document.querySelector("body") as HTMLElement;
  main: CreateElem;

  constructor() {
    this.main = new CreateElem("main", "main");
  }

  start() {
    this.body.append(this.main.getElement());
  }
}

export { App };
