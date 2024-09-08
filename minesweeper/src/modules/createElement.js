const createElement = (element, selector) => {
  const elem = document.createElement(element);
  elem.classList.add(selector);
  return elem;
};

export { createElement };
