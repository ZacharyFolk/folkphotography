<?php get_header(); ?>

<main class="site-content">
    <div class="content-wrapper">
        <?php
        while (have_posts()) :
            the_post();
            $thumb_id    = get_post_thumbnail_id();
            $att_desc    = $thumb_id ? trim( get_post_field( 'post_content', $thumb_id ) ) : '';
            $use_att_desc = ! trim( get_the_content() ) && $att_desc;
        ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('post-content'); ?>>
                <header class="entry-header">
                    <?php the_title('<h1 class="entry-title">', '</h1>'); ?>

                    <div class="entry-meta">
                        <span class="posted-on">
                            <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
                        </span>
                        <span class="byline">
                            <?php printf( esc_html__( 'by %s', 'folkphotography' ), esc_html( get_the_author() ) ); ?>
                        </span>
                        <?php if (has_category()) : ?>
                            <span class="categories">
                                <?php the_category(', '); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </header>

                <?php if (has_post_thumbnail()) : ?>
                    <div class="featured-image">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                <?php endif; ?>

                <div class="entry-content">
                    <?php if ( $use_att_desc ) : ?>
                        <?php echo wp_kses_post( wpautop( $att_desc ) ); ?>
                    <?php else : ?>
                        <?php the_content(); ?>
                    <?php endif; ?>
                </div>

                <?php if (has_tag()) : ?>
                    <footer class="entry-footer">
                        <div class="tags">
                            <?php the_tags('<span class="tags-label">Tags: </span>', ', '); ?>
                        </div>
                    </footer>
                <?php endif; ?>
            </article>

            <?php
            // Post navigation
            the_post_navigation(array(
                'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous:', 'folkphotography' ) . '</span> <span class="nav-title">%title</span>',
                'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next:', 'folkphotography' ) . '</span> <span class="nav-title">%title</span>',
            ));

            // Comments
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;
            ?>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
