class Spinner {
    constructor(elementSelector, targetSelector) {
        this.element = document.querySelector(`.${elementSelector}`);
        this.targetSelector = targetSelector;
        this.spinnerActive = false;
        this.startSpinnerTimeout = 0;
        this.startTimestamp = Date.now();
        this.currentTimestamp = 0;
    }

    start() {
        if (!this.spinnerActive) {
            this.startSpinnerTimeout = setTimeout(() => {
                this.element.classList.add('loader_active');
                this.spinnerActive = true;
            }, 1000);
        } else {
        }
    }

    stop() {
        if (this.spinnerActive) {
            return new Promise((resolve) => {
                this.currentTimestamp = Date.now();
                if (this.currentTimestamp - this.startTimestamp >= 2000) {
                    this.element.classList.remove('loader_active');
                    // Показываем секцию/блок
                    if (this.targetSelector) {
                        document
                            .querySelector(`.${this.targetSelector}`)
                            .classList.remove(this.targetSelector);
                    }
                    this.spinnerActive = false;
                } else {
                    setTimeout(() => {
                        this.element.classList.remove('loader_active');
                        // Показываем секцию/блок
                        if (this.targetSelector) {
                            document
                                .querySelector(`.${this.targetSelector}`)
                                .classList.remove(this.targetSelector);
                        }
                        this.spinnerActive = false;
                    }, this.currentTimestamp - this.startTimestamp);
                }
            });
        } else {
            clearTimeout(this.startSpinnerTimeout);
            setTimeout(() => {
                // Показываем секцию/блок
                if (this.targetSelector) {
                    document
                        .querySelector(`.${this.targetSelector}`)
                        .classList.remove(this.targetSelector);
                }
            }, 50);
        }
    }
}
const spinner = new Spinner('loader', 'cards-hidden');
try {
    if (spinner.element) {
        spinner.start();
    }
} catch (error) {
    console.log('Ошибка', error);
}

export { spinner, Spinner };
