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
                        <?php printf( esc_html__( 'Search Results for: %s', 'folkphotography' ), '<span>' . esc_html( get_search_query() ) . '</span>' ); ?>
                    </h1>
                <?php endif; ?>
            </header>

            <?php if ( is_home() || is_archive() ) :
                $all_cats    = get_categories( array( 'hide_empty' => true ) );
                $current_cat = is_category() ? get_queried_object() : null;
                $blog_home   = get_option( 'page_for_posts' )
                    ? get_permalink( absint( get_option( 'page_for_posts' ) ) )
                    : home_url( '/' );
                if ( $all_cats ) : ?>
                <nav class="journal-filters">
                    <a href="<?php echo esc_url( $blog_home ); ?>"
                       class="filter-btn <?php echo is_home() ? 'active' : ''; ?>">
                        <?php esc_html_e( 'All', 'folkphotography' ); ?>
                    </a>
                    <?php foreach ( $all_cats as $cat ) : ?>
                        <a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>"
                           class="filter-btn <?php echo ( $current_cat && $current_cat->term_id === $cat->term_id ) ? 'active' : ''; ?>">
                            <?php echo esc_html( $cat->name ); ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
                <?php endif; ?>
            <?php endif; ?>

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
