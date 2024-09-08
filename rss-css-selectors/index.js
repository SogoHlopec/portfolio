/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/index.html":
/*!************************!*\
  !*** ./src/index.html ***!
  \************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
// Module
var code = "<!DOCTYPE html>\r\n<html lang=\"en\">\r\n<head>\r\n  <meta charset=\"UTF-8\">\r\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title>RSS-CSS-Selectors</title>\r\n</head>\r\n<body>\r\n\r\n</body>\r\n</html>";
// Exports
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (code);

/***/ }),

/***/ "./src/style.scss":
/*!************************!*\
  !*** ./src/style.scss ***!
  \************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./src/components/App.ts":
/*!*******************************!*\
  !*** ./src/components/App.ts ***!
  \*******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   App: () => (/* binding */ App)
/* harmony export */ });
/* harmony import */ var _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../modules/CreateElement */ "./src/modules/CreateElement.ts");
/* harmony import */ var _game_wrapper_GameWrapper__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./game-wrapper/GameWrapper */ "./src/components/game-wrapper/GameWrapper.ts");


class App {
    constructor() {
        this.body = document.querySelector("body");
        this.main = new _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__.CreateElement("main", "main");
    }
    start() {
        this.body.append(this.main.getElement());
        const gameWrapper = new _game_wrapper_GameWrapper__WEBPACK_IMPORTED_MODULE_1__.GameWrapper();
        gameWrapper.createHtml();
    }
}



/***/ }),

/***/ "./src/components/board/Board.ts":
/*!***************************************!*\
  !*** ./src/components/board/Board.ts ***!
  \***************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Board: () => (/* binding */ Board)
/* harmony export */ });
/* harmony import */ var _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../modules/CreateElement */ "./src/modules/CreateElement.ts");
/* harmony import */ var _levels_dataLevels__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../levels/dataLevels */ "./src/components/levels/dataLevels.ts");


class Board {
    constructor(wrapper) {
        this.wrapper = wrapper;
    }
    createBoard(levelIndex) {
        const level = _levels_dataLevels__WEBPACK_IMPORTED_MODULE_1__.dataLevels[levelIndex - 1];
        const board = new _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__.CreateElement("section", "board");
        this.wrapper.appendElement(board.getElement());
        const title = new _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__.CreateElement("h1", "board__title");
        board.appendElement(title.getElement());
        title.setInnerText(`${level.titleTask}`);
        const table = new _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__.CreateElement("div", "table");
        board.appendElement(table.getElement());
        table.getElement().innerHTML = level.html;
    }
}



/***/ }),

/***/ "./src/components/css-editor/CssEditor.ts":
/*!************************************************!*\
  !*** ./src/components/css-editor/CssEditor.ts ***!
  \************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CssEditor: () => (/* binding */ CssEditor)
/* harmony export */ });
/* harmony import */ var _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../modules/CreateElement */ "./src/modules/CreateElement.ts");
/* harmony import */ var _levels_dataLevels__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../levels/dataLevels */ "./src/components/levels/dataLevels.ts");


class CssEditor {
    constructor(wrapper) {
        this.wrapper = wrapper;
    }
    createCssEditor(levelIndex) {
        const level = _levels_dataLevels__WEBPACK_IMPORTED_MODULE_1__.dataLevels[levelIndex - 1];
        console.log(level);
        const editor = new _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__.CreateElement("section", "editor");
        this.wrapper.appendElement(editor.getElement());
        const header = new _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__.CreateElement("h2", "editor__header");
        editor.appendElement(header.getElement());
        header.getElement().innerHTML = `CSS Editor <div class="editor__file-name">style.css</div>`;
        const code = new _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__.CreateElement("div", "editor__code");
        editor.appendElement(code.getElement());
        const lineNumbers = new _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__.CreateElement("ul", "code__line-numbers");
        code.appendElement(lineNumbers.getElement());
        for (let i = 0; i < 20; i++) {
            const line = new _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__.CreateElement("li", "line-number");
            line.setInnerText(`${i + 1}`);
            lineNumbers.appendElement(line.getElement());
        }
        const codeWindow = new _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__.CreateElement("div", "code__window");
        code.appendElement(codeWindow.getElement());
        const inputWrapper = new _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__.CreateElement("div", "code__input-wrapper");
        codeWindow.appendElement(inputWrapper.getElement());
        const input = new _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__.CreateElement("input", "code__input");
        input.getElement().setAttribute("type", "text");
        input.getElement().setAttribute("placeholder", "Type in a CSS selector");
        inputWrapper.appendElement(input.getElement());
        const submit = new _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__.CreateElement("button", "code__submit");
        submit.setInnerText("Enter");
        inputWrapper.appendElement(submit.getElement());
        const comments = new _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__.CreateElement("pre", "code__comments");
        comments.setInnerText(`  {
      /* Styles would go here. */
  }

  /*
  Type a number to skip to a level.
  Ex → "5" for level 5
  */`);
        codeWindow.appendElement(comments.getElement());
    }
}



/***/ }),

/***/ "./src/components/game-wrapper/GameWrapper.ts":
/*!****************************************************!*\
  !*** ./src/components/game-wrapper/GameWrapper.ts ***!
  \****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   GameWrapper: () => (/* binding */ GameWrapper)
/* harmony export */ });
/* harmony import */ var _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../modules/CreateElement */ "./src/modules/CreateElement.ts");
/* harmony import */ var _board_Board__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../board/Board */ "./src/components/board/Board.ts");
/* harmony import */ var _css_editor_CssEditor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../css-editor/CssEditor */ "./src/components/css-editor/CssEditor.ts");
/* harmony import */ var _html_viewer_HtmlViewer__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../html-viewer/HtmlViewer */ "./src/components/html-viewer/HtmlViewer.ts");
/* harmony import */ var _levels_Levels__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../levels/Levels */ "./src/components/levels/Levels.ts");





class GameWrapper {
    constructor() {
        this.main = document.querySelector(".main");
    }
    createHtml() {
        var _a;
        const gameWrapper = new _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__.CreateElement("div", "game-wrapper");
        (_a = this.main) === null || _a === void 0 ? void 0 : _a.append(gameWrapper.getElement());
        const board = new _board_Board__WEBPACK_IMPORTED_MODULE_1__.Board(gameWrapper);
        board.createBoard(1);
        const codeWrapper = new _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__.CreateElement("div", "code-wrapper");
        gameWrapper.appendElement(codeWrapper.getElement());
        const cssEditor = new _css_editor_CssEditor__WEBPACK_IMPORTED_MODULE_2__.CssEditor(codeWrapper);
        cssEditor.createCssEditor(1);
        const htmlViewer = new _html_viewer_HtmlViewer__WEBPACK_IMPORTED_MODULE_3__.HtmlViewer(codeWrapper);
        htmlViewer.createCssEditor(1);
        // TODO Create block 4 Levels
        const levels = new _levels_Levels__WEBPACK_IMPORTED_MODULE_4__.Levels();
        levels.createHtml();
    }
}



/***/ }),

/***/ "./src/components/html-viewer/HtmlViewer.ts":
/*!**************************************************!*\
  !*** ./src/components/html-viewer/HtmlViewer.ts ***!
  \**************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   HtmlViewer: () => (/* binding */ HtmlViewer)
/* harmony export */ });
/* harmony import */ var _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../modules/CreateElement */ "./src/modules/CreateElement.ts");
/* harmony import */ var _levels_dataLevels__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../levels/dataLevels */ "./src/components/levels/dataLevels.ts");


class HtmlViewer {
    constructor(wrapper) {
        this.wrapper = wrapper;
    }
    createCssEditor(levelIndex) {
        const level = _levels_dataLevels__WEBPACK_IMPORTED_MODULE_1__.dataLevels[levelIndex - 1];
        console.log(level);
        const editor = new _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__.CreateElement("section", "viewer");
        this.wrapper.appendElement(editor.getElement());
        const header = new _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__.CreateElement("h2", "viewer__header");
        editor.appendElement(header.getElement());
        header.getElement().innerHTML = `HTML Viewer <div class="viewer__file-name">table.html</div>`;
        const code = new _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__.CreateElement("div", "viewer__code");
        editor.appendElement(code.getElement());
        const lineNumbers = new _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__.CreateElement("ul", "code__line-numbers");
        code.appendElement(lineNumbers.getElement());
        for (let i = 0; i < 20; i++) {
            const line = new _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__.CreateElement("li", "line-number");
            line.setInnerText(`${i + 1}`);
            lineNumbers.appendElement(line.getElement());
        }
        const codeWindow = new _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__.CreateElement("pre", "code__window");
        code.appendElement(codeWindow.getElement());
        codeWindow.setInnerText(`${level.htmlForViewer}`);
    }
}



/***/ }),

/***/ "./src/components/levels/Levels.ts":
/*!*****************************************!*\
  !*** ./src/components/levels/Levels.ts ***!
  \*****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Levels: () => (/* binding */ Levels)
/* harmony export */ });
/* harmony import */ var _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../modules/CreateElement */ "./src/modules/CreateElement.ts");
/* harmony import */ var _dataLevels__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./dataLevels */ "./src/components/levels/dataLevels.ts");


class Levels {
    constructor() {
        this.main = document.querySelector(".main");
    }
    createHtml() {
        var _a;
        const levels = new _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__.CreateElement("section", "levels");
        (_a = this.main) === null || _a === void 0 ? void 0 : _a.append(levels.getElement());
        const title = new _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__.CreateElement("h2", "levels__title");
        title.setInnerText("Level");
        levels.appendElement(title.getElement());
        const listLevels = new _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__.CreateElement("ul", "levels__list");
        levels.appendElement(listLevels.getElement());
        for (let i = 0; i < _dataLevels__WEBPACK_IMPORTED_MODULE_1__.dataLevels.length; i++) {
            const listItem = new _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__.CreateElement("li", "list__item");
            listItem.setInnerText(`${_dataLevels__WEBPACK_IMPORTED_MODULE_1__.dataLevels[i].level}`);
            listLevels.appendElement(listItem.getElement());
        }
        const reset = new _modules_CreateElement__WEBPACK_IMPORTED_MODULE_0__.CreateElement("button", "levels__reset");
        reset.setInnerText("Reset");
        levels.appendElement(reset.getElement());
    }
}



/***/ }),

/***/ "./src/components/levels/dataLevels.ts":
/*!*********************************************!*\
  !*** ./src/components/levels/dataLevels.ts ***!
  \*********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   dataLevels: () => (/* binding */ dataLevels)
/* harmony export */ });
const dataLevels = [
    {
        level: 1,
        titleTask: "Select the flower pot",
        html: `<pot class="animated"></pot>
    <pot class="animated"></pot>
    `,
        htmlForViewer: `  <div class="table">
    <pot></pot>
    <pot></pot>
  </div>
    `,
    },
    {
        level: 2,
        titleTask: "Select the pink flower pot",
        html: `<pot></pot>
    <pink class="animated"></pink>
    <pot></pot>
    `,
        htmlForViewer: `  <div class="table">
    <pot></pot>
    <pink></pink>
    <pot></pot>
  </div>
    `,
    },
    {
        level: 3,
        titleTask: "",
        html: "",
        htmlForViewer: "",
    },
    {
        level: 4,
        titleTask: "",
        html: "",
        htmlForViewer: "",
    },
    {
        level: 5,
        titleTask: "",
        html: "",
        htmlForViewer: "",
    },
    {
        level: 6,
        titleTask: "",
        html: "",
        htmlForViewer: "",
    },
    {
        level: 7,
        titleTask: "",
        html: "",
        htmlForViewer: "",
    },
    {
        level: 8,
        titleTask: "",
        html: "",
        htmlForViewer: "",
    },
    {
        level: 9,
        titleTask: "",
        html: "",
        htmlForViewer: "",
    },
    {
        level: 10,
        titleTask: "",
        html: "",
        htmlForViewer: "",
    },
];



/***/ }),

/***/ "./src/modules/CreateElement.ts":
/*!**************************************!*\
  !*** ./src/modules/CreateElement.ts ***!
  \**************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CreateElement: () => (/* binding */ CreateElement)
/* harmony export */ });
class CreateElement {
    constructor(element, selector) {
        this.element = document.createElement(element);
        this.selector = selector;
    }
    setClassSelector(classSelector) {
        this.element.classList.add(classSelector);
    }
    setInnerText(text) {
        this.element.innerText = text;
    }
    getElement() {
        if (this.selector)
            this.element.classList.add(this.selector);
        return this.element;
    }
    prependElement(element) {
        this.element.prepend(element);
    }
    appendElement(element) {
        this.element.append(element);
    }
}



/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!**********************!*\
  !*** ./src/index.ts ***!
  \**********************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _index_html__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./index.html */ "./src/index.html");
/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./style.scss */ "./src/style.scss");
/* harmony import */ var _components_App__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./components/App */ "./src/components/App.ts");



const app = new _components_App__WEBPACK_IMPORTED_MODULE_2__.App();
app.start();

})();

/******/ })()
;
//# sourceMappingURL=index.js.map