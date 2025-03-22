document.addEventListener('DOMContentLoaded', () => {
    const articleList = document.querySelector('.articles__list');

    articleList.addEventListener('click', async (e) => {
        const target = e.target;

        if (target.closest('.articles__card-btns')) {
            const btn = target.closest('.btn');
            const voteType = btn.classList.contains('btn-like')
                ? 'like'
                : 'dislike';

            if (!voteType) return;

            const card = target.closest('.articles__card');
            const postId = card.dataset.id;
            console.log(postId);
            try {
                const response = await fetch(voteAjax.ajax_url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=sogohlopec_likes_system_handle_vote&post_id=${postId}&vote_type=${voteType}`,
                });

                const data = await response.json();

                if (data.success) {
                    const votes = data.data;
                    const counter = card.querySelector('.likes-number');
                    counter.textContent = votes;
                    if (votes > 0) {
                        counter.className = '';
                        counter.classList.add('likes-number', 'likes');
                    } else if (votes < 0) {
                        counter.className = '';
                        counter.classList.add('likes-number', 'dislikes');
                    } else {
                        counter.className = '';
                        counter.classList.add('likes-number');
                    }
                } else {
                    console.error('Error:', data);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
    });
});
