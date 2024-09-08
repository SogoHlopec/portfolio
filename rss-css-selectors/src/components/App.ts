import { CreateElement } from "../modules/CreateElement";
import { GameWrapper } from "./game-wrapper/GameWrapper";

class App {
  body: HTMLElement = document.querySelector("body") as HTMLElement;
  main: CreateElement;

  constructor() {
    this.main = new CreateElement("main", "main");
  }

  start() {
    this.body.append(this.main.getElement());
    const gameWrapper = new GameWrapper();
    gameWrapper.createHtml();
  }
}

export { App };
