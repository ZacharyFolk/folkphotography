<article id="post-<?php the_ID(); ?>" <?php post_class( has_post_thumbnail() ? 'post-excerpt has-thumbnail' : 'post-excerpt' ); ?>>
    <?php if ( has_post_thumbnail() ) : ?>
        <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
            <?php the_post_thumbnail( 'medium' ); ?>
        </a>
    <?php endif; ?>

    <div class="post-summary">
        <header class="entry-header">
            <?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' ); ?>
            <div class="entry-meta">
                <span class="posted-on"><?php echo get_the_date(); ?></span>
                <?php
                $post_cats = get_the_category();
                if ( $post_cats ) :
                    echo '<span class="entry-cats">';
                    foreach ( $post_cats as $i => $c ) {
                        if ( $i ) echo ' · ';
                        echo '<a href="' . esc_url( get_category_link( $c->term_id ) ) . '">' . esc_html( $c->name ) . '</a>';
                    }
                    echo '</span>';
                endif;
                ?>
            </div>
        </header>

        <div class="entry-summary">
            <?php the_excerpt(); ?>
        </div>

        <a href="<?php the_permalink(); ?>" class="read-more">
            <?php esc_html_e( 'Read More', 'folkphotography' ); ?> &rarr;
        </a>
    </div>
</article>
