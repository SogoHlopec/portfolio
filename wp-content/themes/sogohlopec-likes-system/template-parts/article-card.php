<div class="articles__card" data-id="<?php the_ID(); ?>">
    <a class="articles__card-image" href="<?php the_permalink(); ?>">
        <?php the_post_thumbnail(); ?>
    </a>
    <div class="articles__card-content"> <a class="articles__card-title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        <div class="articles__card-description"><?php the_excerpt(); ?></div>
        <div class="articles__card-footer">
            <p class="articles__card-author">Автор: <span><?php the_author(); ?></span></p>
            <div class="articles__card-btns">
                <div class="btn btn-like"><svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_7201_144)">
                            <path
                                d="M11 22C17.0751 22 22 17.0751 22 11C22 4.92487 17.0751 0 11 0C4.92487 0 0 4.92487 0 11C0 17.0751 4.92487 22 11 22Z"
                                fill="#43B05C" />
                            <path d="M11 5.72V16.72" stroke="white" stroke-width="2" stroke-miterlimit="10"
                                stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M16.5 11H5.5" stroke="white" stroke-width="2" stroke-miterlimit="10"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </g>
                        <defs>
                            <clipPath id="clip0_7201_144">
                                <rect width="22" height="22" fill="white" />
                            </clipPath>
                        </defs>
                    </svg></div>
                <?php
                $post_id = get_the_ID();
                $votes = sogohlopec_likes_system_get_number_of_votes($post_id);

                if ($votes > 0) {
                ?>
                    <div class="likes-number likes"><?php echo $votes ?></div>
                <?php
                } else if ($votes < 0) {
                ?>
                    <div class="likes-number dislikes"><?php echo $votes ?></div>
                <?php
                } else {
                ?>
                    <div class="likes-number"><?php echo $votes ?></div>
                <?php
                }
                ?>
                <div class="btn btn-dislike"><svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_7201_149)">
                            <path
                                d="M11 22C17.0751 22 22 17.0751 22 11C22 4.92487 17.0751 0 11 0C4.92487 0 0 4.92487 0 11C0 17.0751 4.92487 22 11 22Z"
                                fill="#ED8A19" />
                            <path d="M16.72 11H5.28003" stroke="white" stroke-width="2" stroke-miterlimit="10"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </g>
                        <defs>
                            <clipPath id="clip0_7201_149">
                                <rect width="22" height="22" fill="white" />
                            </clipPath>
                        </defs>
                    </svg></div>
            </div>
        </div>
    </div>
</div>