class Timer {
  constructor(elTime) {
    this.counter = elTime;
    this.sec = 0;
    this.timerId = null;
  }

  start() {
    if (this.timerId) {
      console.log("Таймер уже запущен.");
      return;
    }

    const tick = () => {
      this.sec++;
      this.addText();
      this.timerId = setTimeout(tick, 1000);
    };
    tick();
  }

  addText() {
    this.counter.textContent = `Time: ${
      this.sec > 99
        ? this.sec
        : "0" + (this.sec > 9 ? this.sec : "0" + this.sec)
    }`;
  }

  stop() {
    this.sec = 0;
    clearTimeout(this.timerId);
    this.addText();
  }
}

export { Timer };
