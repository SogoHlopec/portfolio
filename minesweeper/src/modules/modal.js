class Modal {
  constructor() {
    this.modal = document.querySelector(".modal");
  }

  open() {
    this.modal.style.display = "block";
  }

  close() {
    this.modal.style.display = "none";
  }
}

export { Modal };
