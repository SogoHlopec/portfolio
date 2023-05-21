import { createElement } from "./createElement";

const createHtml = (body) => {
  const header = createElement("header", "header");
  body.append(header);

  const title = createElement("h1", "title");
  title.textContent = "Minesweeper";
  header.append(title);

  const main = createElement("main", "main");
  body.append(main);

  const grid = createElement("div", "grid");
  main.append(grid);
};

export { createHtml };
