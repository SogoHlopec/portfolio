import { app } from "..";
class Modal {
  constructor() {
    this.modal = document.querySelector(".modal");
    this.text = this.modal.querySelector(".modal__text");
    this.seconds = Number(
      document.querySelector(".counter__time").textContent.split(" ")[1]
    );
    this.moves = Number(
      document.querySelector(".counter__moves").textContent.split(" ")[1]
    );
  }

  open(win) {
    this.modal.style.display = "block";
    this.modal.querySelector(".modal__close").addEventListener("click", () => {
      this.close();
      app.newGame();
    });
    window.addEventListener("click", (event) => {
      if (event.target == this.modal) {
        this.close();
        app.newGame();
      }
    });

    if (win === true) {
      this.text.textContent = `Hooray! You found all mines in ${this.seconds} seconds and ${this.moves} moves!`;
    }
  }

  close() {
    this.modal.style.display = "none";
  }
}

export { Modal };
