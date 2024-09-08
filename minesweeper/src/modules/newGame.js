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
    this.win = false;
    this.timer = new Timer(document.querySelector(".counter__time"));
    this.moves = document.querySelector(".counter__moves");
    this.countMove = 0;
  }

  start() {
    this.grid.innerHTML = "";
    this.countMove = 0;
    this.moves.textContent = `Moves: ${this.countMove}`;
    this.createBoard();
  }

  createBoard() {
    //get shuffled game array with random bombs
    // const bombsArray = Array(this.bombAmount).fill("cell_bomb");
    // const emptyArray = Array(this.width * this.width - this.bombAmount).fill(
    //   "cell_empty"
    // );
    // const gameArray = emptyArray.concat(bombsArray);
    // const shuffledArray = gameArray.sort(() => Math.random() - 0.5);

    const emptyArray = Array(this.width * this.width).fill("cell_empty");

    for (let i = 0; i < this.width * this.width; i++) {
      const square = document.createElement("div");
      square.classList.add("grid__cell");
      square.setAttribute("id", i);
      square.classList.add(emptyArray[i]);
      this.grid.appendChild(square);
      this.squares.push(square);

      // normal click
      square.addEventListener("click", (e) => {
        if (this.countMove === 0) {
          this.addBombs(e.target.id);
          this.addMove();
          this.addNumbers();
          e.target.click();
          return;
        }
        this.click(square);
        this.addMove();
        if (!this.isGameOver) {
          this.timer.start();
        }
      });

      // right click
      square.oncontextmenu = (e) => {
        e.preventDefault();
        this.addFlag(square);
        if (!this.isGameOver) {
          this.timer.start();
        }
      };
    }
    this.addNumbers();
  }

  addNumbers() {
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
          i < 99 &&
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
          i <= 88 &&
          !isRightEdge &&
          this.squares[i + 1 + this.width].classList.contains("cell_bomb")
        ) {
          total++;
        }
        if (
          i <= 89 &&
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
        this.checkForWin();
        return;
      }
      // if (this.countMove === 0) {
      //   this.addBombs();
      //   return
      // }
      this.checkSquare(currentId);
    }
    square.classList.add("cell_checked");
  }

  addBombs(id) {
    // get shuffled game array with random bombs

    const createShuffledArray = (id) => {
      const bombsArray = Array(this.bombAmount).fill("cell_bomb");
      const emptyArray = Array(this.width * this.width - this.bombAmount).fill(
        "cell_empty"
      );
      const gameArray = emptyArray.concat(bombsArray);
      const shuffledArray = gameArray.sort(() => Math.random() - 0.5);
      if (shuffledArray[id] === "cell_bomb") {
        return createShuffledArray(id);
      } else {
        return shuffledArray;
      }
    };
    const shuffledArray = createShuffledArray(id);
    console.log(shuffledArray);

    for (let i = 0; i < this.squares.length; i++) {
      this.squares[i].classList.add(shuffledArray[i]);
    }
  }

  checkSquare(currentId) {
    const isLeftEdge = currentId % this.width === 0;
    const isRightEdge = currentId % this.width === this.width - 1;

    const clickNewSquare = (newId) => {
      const newSquare = document.getElementById(newId);
      this.click(newSquare);
    };

    setTimeout(() => {
      if (currentId > 0 && !isLeftEdge) {
        const newId = Number(currentId) - 1;
        clickNewSquare(newId);
      }
      if (currentId > 9 && !isRightEdge) {
        const newId = Number(currentId) + 1 - this.width;
        clickNewSquare(newId);
      }
      if (currentId > 10) {
        const newId = Number(currentId) - this.width;
        clickNewSquare(newId);
      }
      if (currentId > 11 && !isLeftEdge) {
        const newId = Number(currentId) - 1 - this.width;
        clickNewSquare(newId);
      }
      if (currentId < 99 && !isRightEdge) {
        const newId = Number(currentId) + 1;
        clickNewSquare(newId);
      }
      if (currentId < 90 && !isLeftEdge) {
        const newId = Number(currentId) - 1 + this.width;
        clickNewSquare(newId);
      }
      if (currentId < 88 && !isRightEdge) {
        const newId = Number(currentId) + 1 + this.width;
        clickNewSquare(newId);
      }
      if (currentId < 89) {
        const newId = Number(currentId) + this.width;
        clickNewSquare(newId);
      }
    }, 10);
  }

  gameOver(square) {
    this.isGameOver = true;
    const modal = new Modal();
    modal.open(this.win);
    this.timer.stop();

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
    let checked = 0;
    for (let i = 0; i < this.squares.length; i++) {
      if (
        this.squares[i].classList.contains("cell_flag") &&
        this.squares[i].classList.contains("cell_bomb")
      ) {
        matches++;
      }
      if (this.squares[i].classList.contains("cell_checked")) {
        checked++;
      }
      if (
        matches === this.bombAmount ||
        checked === this.squares.length - this.bombAmount
      ) {
        this.isGameOver = true;
        this.win = true;
        const modal = new Modal();
        modal.open(this.win);
        this.timer.stop();
      }
    }
  }

  addMove() {
    this.countMove++;
    this.moves.textContent = `Moves: ${this.countMove}`;
  }
}

export { NewGame };
