import "./index.html";
import "./style.scss";
import { App } from "./modules/app";

const body = document.querySelector("body");
const app = new App(body);
app.render();
app.newGame();

// alert("Пожалуйста, проверьте работу в последний день. Спасибо.")
export { app };