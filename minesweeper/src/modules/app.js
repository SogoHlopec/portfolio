import { NewGame } from "./newGame";
import { createHtml } from "./createHtml";

class App {
  constructor(body) {
    this.body = body;
    this.width = 10;
    this.bombAmount = 10;
  }

  render() {
    createHtml(this.body);
  }

  newGame() {
    const newGame = new NewGame(this.width, this.bombAmount);
    newGame.start();
  }
}

export { App };
