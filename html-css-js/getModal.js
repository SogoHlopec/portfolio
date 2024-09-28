if (document.querySelector('.open-modal')) {
    const getModal = (e) => {
        const modal = $('.modal');

        const resourceId = $(e.target).closest('.open-modal').data('id');

        if (modal.length) {
            $.ajax({
                type: 'POST',
                url: '/ajax',
                data: {
                    id: resourceId,
                    action: 'getModal',
                },
                success: function (response) {
                    modal.html(response);
                    openModal(e);
                },
                error: function () {
                    console.log('Error loading modal content');
                },
            });
        }
    };

    const openModal = (e) => {
        document.querySelector('.modal').classList.add('modal_active');
        document.querySelector('body').classList.add('hidden');
    };

    const closeModal = (e) => {
        document.querySelector('.modal').classList.remove('modal_active');
        document.querySelector('body').classList.remove('hidden');
    };

    try {
        document.addEventListener('click', (e) => {
            if (e.target.closest('.open-modal')) getModal(e);
            if (e.target.closest('.modal__btn-close')) closeModal(e);
            if (e.target.closest('.modal')) closeModal(e);
        });
    } catch (error) {
        console.log(error);
    }
}
