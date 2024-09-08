class Timer {
  constructor(elTime) {
    this.counter = elTime;
    this.sec = 0;
    this.timerId = null;
  }

  start() {
    if (this.timerId) {
      return;
    }

    this.timerId = setInterval(() => {
      this.sec++;
      this.addText();
    }, 1000);
    console.log("Timer Start!");
  }

  addText() {
    this.counter.textContent = `Time: ${
      this.sec > 99
        ? this.sec
        : "0" + (this.sec > 9 ? this.sec : "0" + this.sec)
    }`;
  }

  stop() {
    console.log("Timer Stop!");
    this.sec = 0;
    clearInterval(this.timerId);
    this.timerId = null;
    this.addText();
  }
}

export { Timer };
