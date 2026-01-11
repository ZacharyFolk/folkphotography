<?php get_header(); ?>

<main class="site-content">
    <div class="content-wrapper">
        <?php if (have_posts()) : ?>
            <header class="page-header">
                <?php
                if (is_home() && !is_front_page()) :
                    ?>
                    <h1 class="page-title"><?php single_post_title(); ?></h1>
                <?php
                elseif (is_archive()) :
                    the_archive_title('<h1 class="page-title">', '</h1>');
                    the_archive_description('<div class="archive-description">', '</div>');
                elseif (is_search()) :
                    ?>
                    <h1 class="page-title">
                        <?php printf(__('Search Results for: %s', 'folkphotography'), '<span>' . get_search_query() . '</span>'); ?>
                    </h1>
                <?php endif; ?>
            </header>

            <div class="posts-list">
                <?php
                while (have_posts()) :
                    the_post();
                    get_template_part('template-parts/content', get_post_type());
                endwhile;
                ?>
            </div>

            <?php
            the_posts_pagination(array(
                'mid_size' => 2,
                'prev_text' => __('&larr; Previous', 'folkphotography'),
                'next_text' => __('Next &rarr;', 'folkphotography'),
            ));
            ?>

        <?php else : ?>
            <div class="no-results">
                <h1><?php _e('Nothing Found', 'folkphotography'); ?></h1>
                <p><?php _e('It seems we can&rsquo;t find what you&rsquo;re looking for.', 'folkphotography'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
