export const createElement = (
    tag,
    classes = [],
    attributes = {},
    content = ''
) => {
    try {
        const element = document.createElement(tag);
        if (classes.length) element.classList.add(...classes);
        Object.entries(attributes).forEach(([key, value]) => {
            element.setAttribute(key, value);
        });
        if (typeof content === 'string') element.innerHTML = content;
        if (content instanceof HTMLElement) element.appendChild(content);
        return element;
    } catch (error) {
        console.log(error);
    }
};
