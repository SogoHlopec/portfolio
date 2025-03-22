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
                    
                        <nav class="pagination articles__pagination">
                            <?php
                            // Btn Back
                            $current_page = max(1, get_query_var('paged'));
                            $total_pages = $wp_query->max_num_pages;
                            if ($current_page > 1) {
                                $prev_link = get_pagenum_link($current_page - 1);
                                echo '<button class="pagination__btn pagination__btn-prev">
                                    <a href="' . $prev_link . '">
                                        <span>Назад</span>
                                        <svg width="10" height="16" viewBox="0 0 10 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M9.68091 1.66598L3.59843 8.01937L9.64225 14.4118L8.05374 16L0.305926 8.05818L8.01563 -5.34786e-05L9.68091 1.66598Z" fill="black" /></svg>
                                    </a>
                                </button>';
                            } else {
                                echo '<button class="pagination__btn pagination__btn-prev" disabled>
                                    <span>Назад</span>
                                    <svg width="10" height="16" viewBox="0 0 10 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M9.68091 1.66598L3.59843 8.01937L9.64225 14.4118L8.05374 16L0.305926 8.05818L8.01563 -5.34786e-05L9.68091 1.66598Z" fill="black" /></svg>
                                </button>';
                            }
                            ?>
                            <ul class="pagination__list">
                                <?php
                                    // Page links list 
                                    $pagination_links = paginate_links(array(
                                    'total' => $total_pages,
                                    'current' => $current_page,
                                    'prev_next' => false,
                                    'type' => 'array',
                                    'mid_size' => 2,
                                    'end_size' => 1,
                                ));

                                if ($pagination_links) {
                                    foreach ($pagination_links as $link) {
                                        if (strpos($link, 'current') !== false) {
                                            echo '<li class="pagination__item pagination__item_active">' . $link . '</li>';
                                        } elseif (strpos($link, 'dots') !== false) {
                                            echo '<li class="pagination__item pagination__itme-dots">' . $link . '</li>';
                                        } else {
                                            echo '<li class="pagination__item">' . $link .'</li>';
                                        }
                                    }
                                }
                                ?>
                            </ul>

                            <?php
                            // Btn Next
                            if ($current_page < $total_pages) {
                                $next_link = get_pagenum_link($current_page + 1);
                                echo '<button class="pagination__btn pagination__btn-next">
                                    <a href="' . $next_link . '">
                                        <span>Вперёд</span>
                                        <svg width="10" height="16" viewBox="0 0 10 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M0.319091 1.66598L6.40157 8.01937L0.357753 14.4118L1.94626 16L9.69407 8.05818L1.98437 -5.34786e-05L0.319091 1.66598Z" fill="black" /></svg>
                                    </a>
                                </button>';
                            } else {
                                echo '<button class="pagination__btn pagination__btn-next" disabled>
                                    <span>Вперёд</span>
                                    <svg width="10" height="16" viewBox="0 0 10 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M0.319091 1.66598L6.40157 8.01937L0.357753 14.4118L1.94626 16L9.69407 8.05818L1.98437 -5.34786e-05L0.319091 1.66598Z" fill="black" /></svg>
                                </button>';
                            }
                            ?>
                        </nav>

                    <?php
                    } else {
                        echo "<h2>Записей нет.</h2>";
                    }
                    ?>

                </div>
            </div>
        </section>
        <?php get_sidebar(); ?>
    </div>
</main>
<?php get_footer(); ?>