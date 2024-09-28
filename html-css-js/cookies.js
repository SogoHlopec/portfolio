try {
    document.addEventListener('DOMContentLoaded', () => {
        if (document.querySelector('.cookies-wrap')) {
            if (
                !localStorage.getItem('cookieAccept') ||
                localStorage.getItem('cookieAccept') === 0
            ) {
                document
                    .querySelector('.cookies-wrap')
                    .setAttribute('style', 'display: flex');
                setTimeout(() => {
                    document
                        .querySelector('.cookies-wrap')
                        .classList.add('show');

                    document
                        .querySelector('.cookies-wrap')
                        .addEventListener('click', (e) => {
                            if (
                                e.target.closest('.cookies__close') ||
                                e.target.closest('.cookies__button')
                            ) {
                                localStorage.setItem('cookieAccept', 1);
                                document
                                    .querySelector('.cookies-wrap')
                                    .classList.remove('show');
                            }
                        });
                }, 300);
            }
        }
    });
} catch (error) {
    console.log('Ошибка', error);
}
