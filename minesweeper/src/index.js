import "./index.html";
import "./style.scss";
import { App } from "./modules/app";

const body = document.querySelector("body");
console.log(body);
const app = new App(body);
app.render();
app.newGame();
