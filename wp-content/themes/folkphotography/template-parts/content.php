<article id="post-<?php the_ID(); ?>" <?php post_class('post-excerpt'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <div class="post-thumbnail">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('medium'); ?>
            </a>
        </div>
    <?php endif; ?>

    <div class="post-summary">
        <header class="entry-header">
            <?php the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '">', '</a></h2>'); ?>

            <div class="entry-meta">
                <span class="posted-on"><?php echo get_the_date(); ?></span>
            </div>
        </header>

        <div class="entry-summary">
            <?php the_excerpt(); ?>
        </div>

        <a href="<?php the_permalink(); ?>" class="read-more">
            <?php _e('Read More', 'folkphotography'); ?> &rarr;
        </a>
    </div>
</article>
