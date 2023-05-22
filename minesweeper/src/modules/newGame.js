import { app } from "..";
import { Modal } from "./modal";
import { Timer } from "./timer";

class NewGame {
  constructor(width, bombAmount) {
    this.grid = document.querySelector(".grid");
    this.width = width;
    this.bombAmount = bombAmount;
    this.squares = [];
    this.flags = 0;
    this.isGameOver = false;
    this.timer = new Timer(document.querySelector(".counter__time"));
    this.moves = document.querySelector(".counter__moves");
    this.countMove = 0;
  }

  start() {
    this.grid.innerHTML = "";
    this.countMove = 0;
    this.moves.textContent = `Moves: ${this.countMove}`;
    this.timer.stop();
    this.createBoard();
  }

  createBoard() {
    //get shuffled game array with random bombs
    const bombsArray = Array(this.bombAmount).fill("cell_bomb");
    const emptyArray = Array(this.width * this.width - this.bombAmount).fill(
      "cell_empty"
    );
    const gameArray = emptyArray.concat(bombsArray);
    const shuffledArray = gameArray.sort(() => Math.random() - 0.5);

    for (let i = 0; i < this.width * this.width; i++) {
      const square = document.createElement("div");
      square.classList.add("grid__cell");
      square.setAttribute("id", i);
      square.classList.add(shuffledArray[i]);
      this.grid.appendChild(square);
      this.squares.push(square);

      // normal click
      square.addEventListener("click", (e) => {
        this.click(square);
        this.addMove();
      });

      // right click
      square.oncontextmenu = (e) => {
        e.preventDefault();
        this.addFlag(square);
        this.timer.start();
      };
    }

    // add numbers
    for (let i = 0; i < this.squares.length; i++) {
      let total = 0;
      const isLeftEdge = i % this.width === 0;
      const isRightEdge = i % this.width === this.width - 1;

      if (this.squares[i].classList.contains("cell_empty")) {
        if (
          i > 0 &&
          !isLeftEdge &&
          this.squares[i - 1].classList.contains("cell_bomb")
        ) {
          total++;
        }
        if (
          i > 9 &&
          !isRightEdge &&
          this.squares[i + 1 - this.width].classList.contains("cell_bomb")
        ) {
          total++;
        }
        if (
          i > 10 &&
          this.squares[i - this.width].classList.contains("cell_bomb")
        ) {
          total++;
        }
        if (
          i > 11 &&
          !isLeftEdge &&
          this.squares[i - 1 - this.width].classList.contains("cell_bomb")
        ) {
          total++;
        }
        if (
          i < 98 &&
          !isRightEdge &&
          this.squares[i + 1].classList.contains("cell_bomb")
        ) {
          total++;
        }
        if (
          i < 90 &&
          !isLeftEdge &&
          this.squares[i - 1 + this.width].classList.contains("cell_bomb")
        ) {
          total++;
        }
        if (
          i < 88 &&
          !isRightEdge &&
          this.squares[i + 1 + this.width].classList.contains("cell_bomb")
        ) {
          total++;
        }
        if (
          i < 89 &&
          this.squares[i + this.width].classList.contains("cell_bomb")
        ) {
          total++;
        }
        this.squares[i].setAttribute("data", total);
      }
    }
  }

  addFlag(square) {
    if (this.isGameOver) return;
    if (
      !square.classList.contains("cell_checked") &&
      this.flags < this.bombAmount
    ) {
      if (!square.classList.contains("cell_flag")) {
        square.classList.add("cell_flag");
        square.innerHTML = "F";
        this.flags++;
        this.checkForWin();
      } else {
        square.classList.remove("cell_flag");
        square.innerHTML = "";
        this.flags--;
      }
    }
  }

  click(square) {
    let currentId = square.id;
    if (this.isGameOver) return;
    if (square) {
      this.timer.start();
    }
    if (
      square.classList.contains("cell_checked") ||
      square.classList.contains("cell_flag")
    )
      return;
    if (square.classList.contains("cell_bomb")) {
      this.gameOver(square);
    } else {
      let total = square.getAttribute("data");
      if (total != 0) {
        square.classList.add("cell_checked");
        square.innerHTML = total;
        return;
      }
      this.checkSquare(currentId);
    }
    square.classList.add("cell_checked");
  }

  checkSquare(currentId) {
    const isLeftEdge = currentId % this.width === 0;
    const isRightEdge = currentId % this.width === this.width - 1;

    setTimeout(() => {
      if (currentId > 0 && !isLeftEdge) {
        const newId = [Number(currentId) - 1];
        const newSquare = document.getElementById(newId);
        this.click(newSquare);
      }
      if (currentId > 9 && !isRightEdge) {
        const newId = [Number(currentId) + 1 - this.width];
        const newSquare = document.getElementById(newId);
        this.click(newSquare);
      }
      if (currentId > 10) {
        const newId = [Number(currentId) - this.width];
        const newSquare = document.getElementById(newId);
        this.click(newSquare);
      }
      if (currentId > 11 && !isLeftEdge) {
        const newId = [Number(currentId) - 1 - this.width];
        const newSquare = document.getElementById(newId);
        this.click(newSquare);
      }
      if (currentId < 98 && !isRightEdge) {
        const newId = [Number(currentId) + 1];
        const newSquare = document.getElementById(newId);
        this.click(newSquare);
      }
      if (currentId < 90 && !isLeftEdge) {
        const newId = [Number(currentId) - 1 + this.width];
        const newSquare = document.getElementById(newId);
        this.click(newSquare);
      }
      if (currentId < 88 && !isRightEdge) {
        const newId = [Number(currentId) + 1 + this.width];
        const newSquare = document.getElementById(newId);
        this.click(newSquare);
      }
      if (currentId < 89) {
        const newId = [Number(currentId) + this.width];
        const newSquare = document.getElementById(newId);
        this.click(newSquare);
      }
    }, 10);
  }

  gameOver() {
    console.log("BOOM! Game Over!");
    this.isGameOver = true;

    // show ALL the bombs
    this.squares.forEach((square) => {
      if (square.classList.contains("cell_bomb")) {
        square.innerHTML = "B";
        square.style.backgroundColor = "red";
      }
    });
  }

  checkForWin() {
    let matches = 0;
    for (let i = 0; i < this.squares.length; i++) {
      if (
        this.squares[i].classList.contains("cell_flag") &&
        this.squares[i].classList.contains("cell_bomb")
      ) {
        matches++;
      }
      if (matches === this.bombAmount) {
        this.isGameOver = true;
        const modal = new Modal();
        modal.open();
        modal.modal
          .querySelector(".modal__close")
          .addEventListener("click", () => {
            modal.close();
            app.newGame();
          });
      }
    }
  }

  addMove() {
    this.countMove++;
    this.moves.textContent = `Moves: ${this.countMove}`;
  }
}

export { NewGame };
