const splitImageVertically = () => {
    const img = document.querySelector('.start-image img');

    if (img) {
        img.onload = () => {
            const imgHeight = img.clientHeight;
            const halfHeight = imgHeight / 2;

            ['.start-image', '.finish-image'].forEach((selector) => {
                const container = document.querySelector(selector);
                container.style.height = `${halfHeight}px`;
            });
        };

        if (img.complete) {
            img.onload();
        }
    }
};
