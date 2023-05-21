class NewGame {
  constructor(width, bombAmount) {
    this.grid = document.querySelector(".grid");
    this.width = width;
    this.bombAmount = bombAmount;
    this.squares = [];
    this.flag = 0;
    this.isGameOver = false;
  }

  start() {
    this.createBoard();
  }

  createBoard() {
    //get shuffled game array with random bombs
    const bombsArray = Array(this.bombAmount).fill("bomb");
    const emptyArray = Array(this.width * this.width - this.bombAmount).fill(
      "valid"
    );
    const gameArray = emptyArray.concat(bombsArray);
    const shuffledArray = gameArray.sort(() => Math.random() - 0.5);

    for (let i = 0; i < this.width * this.width; i++) {
      const square = document.createElement("div");
      square.setAttribute("id", i);
      square.classList.add(shuffledArray[i]);
      this.grid.appendChild(square);
      this.squares.push(square);

      // normal click
      square.addEventListener("click", (e) => {
        this.click(square);
      });

      // right click
      square.oncontextmenu = (e) => {
        console.log("ПКМ");
        e.preventDefault();
        this.addFlag(square);
      };
    }

    // add numbers
    for (let i = 0; i < this.squares.length; i++) {
      let total = 0;
      const isLeftEdge = i % this.width === 0;
      const isRightEdge = i % this.width === this.width - 1;

      if (this.squares[i].classList.contains("valid")) {
        if (
          i > 0 &&
          !isLeftEdge &&
          this.squares[i - 1].classList.contains("bomb")
        ) {
          total++;
        }
        if (
          i > 9 &&
          !isRightEdge &&
          this.squares[i + 1 - this.width].classList.contains("bomb")
        ) {
          total++;
        }
        if (i > 10 && this.squares[i - this.width].classList.contains("bomb")) {
          total++;
        }
        if (
          i > 11 &&
          !isLeftEdge &&
          this.squares[i - 1 - this.width].classList.contains("bomb")
        ) {
          total++;
        }
        if (
          i < 98 &&
          !isRightEdge &&
          this.squares[i + 1].classList.contains("bomb")
        ) {
          total++;
        }
        if (
          i < 90 &&
          !isLeftEdge &&
          this.squares[i - 1 + this.width].classList.contains("bomb")
        ) {
          total++;
        }
        if (
          i < 88 &&
          !isRightEdge &&
          this.squares[i + 1 + this.width].classList.contains("bomb")
        ) {
          total++;
        }
        if (i < 89 && this.squares[i + this.width].classList.contains("bomb")) {
          total++;
        }
        this.squares[i].setAttribute("data", total);
      }
    }
  }

  addFlag(square) {
    if (this.isGameOver) return;
    if (!square.classList.contains("checked") && this.flags < this.bombAmount) {
    console.log(1);

      if (!square.classList.contains("flag")) {
        square.classList.add("flag");
        square.innerHTML = "Flag";
        this.flags++;
        this.checkForWin();
      } else {
        square.classList.remove("flag");
        square.innerHTML = "";
        this.flags--;
      }
    }
  }

  click(square) {
    let currentId = square.id;
    if (this.isGameOver) return;
    if (
      square.classList.contains("checked") ||
      square.classList.contains("flag")
    )
      return;
    if (square.classList.contains("bomb")) {
      this.gameOver(square);
    } else {
      let total = square.getAttribute("data");
      if (total != 0) {
        square.classList.add("checked");
        square.innerHTML = total;
        return;
      }
      this.checkSquare(currentId);
    }
    square.classList.add("checked");
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
      if (square.classList.contains("bomb")) {
        square.innerHTML = "BOMB!";
      }
    });
  }

  checkForWin() {
    let matches = 0;
    for (let i = 0; i < squares.length; i++) {
      if (
        squares[i].classList.contains("flag") &&
        squares[i].classList.contains("bomb")
      ) {
        matches++;
      }
      if (matches === bombAmount) {
        console.log("YOU WIN!");
        this.isGameOver = true;
      }
    }
  }
}

export { NewGame };
