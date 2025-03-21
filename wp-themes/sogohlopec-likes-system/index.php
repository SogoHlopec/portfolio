<?php get_header(); ?>
<main class="main">
    <div class="page-main">
        <section class="articles">
            <div class="container">
                <h1 class="title articles__title">Статьи</h1>
                <div class="articles__list">

                    <?php
                    if (have_posts()) {
                        while (have_posts()) {
                            the_post();
                            get_template_part('template-parts/article-card');
                        }
                    ?>
                        <?php echo get_the_posts_pagination(); ?>
                    <?php
                    } else {
                        echo "<h2>Записей нет.</h2>";
                    }
                    ?>

                    <nav class="pagination articles__pagination"><button class="pagination__btn pagination__btn-prev">
                            <span>Назад</span><svg width="10" height="16" viewBox="0 0 10 16" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M9.68091 1.66598L3.59843 8.01937L9.64225 14.4118L8.05374 16L0.305926 8.05818L8.01563 -5.34786e-05L9.68091 1.66598Z"
                                    fill="black" />
                            </svg></button>
                        <ul class="pagination__list">
                            <li class="pagination__item"><a class="item" href="#">1</a></li>
                            <li class="pagination__item"><a class="item" href="#">2</a></li>
                            <li class="pagination__item pagination__item_active"><a class="item" href="#">3</a></li>
                            <li class="pagination__item pagination__itme-dots"><span>...</span></li>
                            <li class="pagination__item"><a class="item" href="#">8</a></li>
                            <li class="pagination__item"><a class="item" href="#">9</a></li>
                            <li class="pagination__item"><a class="item" href="#">10</a></li>
                        </ul><button class="pagination__btn pagination__btn-next"><span>Вперед</span><svg width="10"
                                height="16" viewBox="0 0 10 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M0.319091 1.66598L6.40157 8.01937L0.357753 14.4118L1.94626 16L9.69407 8.05818L1.98437 -5.34786e-05L0.319091 1.66598Z"
                                    fill="black" />
                            </svg></button>
                    </nav>
                </div>
            </div>
        </section>
        <?php get_sidebar(); ?>
    </div>
</main>
<?php get_footer(); ?>