import { createElement } from "./createElement";

const createHtml = (body) => {
  const header = createElement("header", "header");
  body.append(header);

  const title = createElement("h1", "title");
  title.textContent = "Minesweeper";
  header.append(title);

  const counter = createElement("div", "counter");
  header.append(counter);

  const time = createElement("div", "counter__time");
  time.textContent = "Time: 000";
  counter.append(time);

  const moves = createElement("div", "counter__moves");
  moves.textContent = "Moves: 0";
  counter.append(moves);

  const main = createElement("main", "main");
  body.append(main);

  const grid = createElement("div", "grid");
  main.append(grid);

  const modal = createElement("div", "modal");
  main.append(modal);

  const modalContent = createElement("div", "modal__content");
  modal.append(modalContent);

  const modalText = createElement("p", "modal__text");
  modalText.textContent =
    "Hooray! You found all mines in ## seconds and N moves!";
  modalContent.append(modalText);

  const modalClose = createElement("span", "modal__close");
  modalClose.textContent = "close";
  modalContent.append(modalClose);
};

export { createHtml };
