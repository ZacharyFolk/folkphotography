<?php
/**
 * Single portfolio item template.
 *
 * Layout: full-bleed featured image above the content wrapper, then title,
 * portfolio categories, excerpt, post content (the image gallery), an EXIF
 * panel pulled from the featured image's _iwh_* meta, and prev/next
 * navigation within the same portfolio_category.
 */

get_header();

while ( have_posts() ) :
    the_post();

    $post_id  = get_the_ID();
    $thumb_id = get_post_thumbnail_id();
    $cats     = get_the_terms( $post_id, 'portfolio_category' );
    $tags     = get_the_terms( $post_id, 'portfolio_tag' );

    // EXIF from featured image
    $make     = $thumb_id ? get_post_meta( $thumb_id, '_iwh_camera_make',   true ) : '';
    $model    = $thumb_id ? get_post_meta( $thumb_id, '_iwh_camera_model',  true ) : '';
    $lens     = $thumb_id ? get_post_meta( $thumb_id, '_iwh_lens',          true ) : '';
    $focal    = $thumb_id ? get_post_meta( $thumb_id, '_iwh_focal_length',  true ) : '';
    $aperture = $thumb_id ? get_post_meta( $thumb_id, '_iwh_aperture',      true ) : '';
    $shutter  = $thumb_id ? get_post_meta( $thumb_id, '_iwh_shutter_speed', true ) : '';
    $iso      = $thumb_id ? get_post_meta( $thumb_id, '_iwh_iso',           true ) : '';
    $location = $thumb_id ? get_post_meta( $thumb_id, '_iwh_location_name', true ) : '';
    $camera   = trim( $make . ' ' . $model );

    // Media library fallbacks — used when the post itself has no content/excerpt
    $att_caption     = $thumb_id ? trim( get_post_field( 'post_excerpt', $thumb_id ) ) : '';
    $att_description = $thumb_id ? trim( get_post_field( 'post_content', $thumb_id ) ) : '';
    $use_att_caption = ! has_excerpt() && $att_caption;
    $use_att_desc    = ! trim( get_the_content() ) && $att_description;

    $has_exif = $camera || $lens || $aperture || $location;
?>

<main class="site-content single-portfolio">

    <?php if ( has_post_thumbnail() ) : ?>
        <div class="portfolio-hero">
            <?php the_post_thumbnail( 'hero-desktop', array( 'loading' => 'eager' ) ); ?>
        </div>
    <?php endif; ?>

    <div class="content-wrapper">
        <article id="post-<?php the_ID(); ?>" <?php post_class( 'portfolio-item-content' ); ?>>

            <header class="portfolio-header">
                <h1 class="entry-title"><?php the_title(); ?></h1>

                <?php if ( $cats && ! is_wp_error( $cats ) ) : ?>
                    <div class="portfolio-categories">
                        <?php foreach ( $cats as $cat ) : ?>
                            <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="portfolio-cat-link">
                                <?php echo esc_html( $cat->name ); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </header>

            <?php if ( has_excerpt() || $use_att_caption ) : ?>
                <div class="portfolio-excerpt">
                    <?php if ( has_excerpt() ) : ?>
                        <?php the_excerpt(); ?>
                    <?php else : ?>
                        <p><?php echo esc_html( $att_caption ); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="entry-content portfolio-gallery">
                <?php if ( $use_att_desc ) : ?>
                    <?php echo wp_kses_post( wpautop( $att_description ) ); ?>
                <?php else : ?>
                    <?php the_content(); ?>
                <?php endif; ?>
            </div>

            <?php if ( $has_exif ) : ?>
                <div class="portfolio-exif-panel">
                    <h3><?php esc_html_e( 'Shot with', 'folkphotography' ); ?></h3>
                    <dl class="exif-list">
                        <?php if ( $camera ) : ?>
                            <div class="exif-row">
                                <dt><?php esc_html_e( 'Camera', 'folkphotography' ); ?></dt>
                                <dd><?php echo esc_html( $camera ); ?></dd>
                            </div>
                        <?php endif; ?>
                        <?php if ( $lens ) : ?>
                            <div class="exif-row">
                                <dt><?php esc_html_e( 'Lens', 'folkphotography' ); ?></dt>
                                <dd><?php echo esc_html( $lens ); ?></dd>
                            </div>
                        <?php endif; ?>
                        <?php if ( $focal ) : ?>
                            <div class="exif-row">
                                <dt><?php esc_html_e( 'Focal length', 'folkphotography' ); ?></dt>
                                <dd><?php echo esc_html( $focal ); ?>mm</dd>
                            </div>
                        <?php endif; ?>
                        <?php if ( $aperture || $shutter || $iso ) :
                            $parts = array();
                            if ( $aperture ) $parts[] = 'f/' . $aperture;
                            if ( $shutter )  $parts[] = $shutter;
                            if ( $iso )      $parts[] = 'ISO ' . $iso;
                        ?>
                            <div class="exif-row">
                                <dt><?php esc_html_e( 'Exposure', 'folkphotography' ); ?></dt>
                                <dd><?php echo esc_html( implode( '  ·  ', $parts ) ); ?></dd>
                            </div>
                        <?php endif; ?>
                        <?php if ( $location ) : ?>
                            <div class="exif-row">
                                <dt><?php esc_html_e( 'Location', 'folkphotography' ); ?></dt>
                                <dd><?php echo esc_html( $location ); ?></dd>
                            </div>
                        <?php endif; ?>
                    </dl>
                </div>
            <?php endif; ?>

            <?php if ( $tags && ! is_wp_error( $tags ) ) : ?>
                <footer class="portfolio-footer">
                    <div class="tags">
                        <?php foreach ( $tags as $tag ) : ?>
                            <a href="<?php echo esc_url( get_term_link( $tag ) ); ?>" class="tag-link">
                                <?php echo esc_html( $tag->name ); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </footer>
            <?php endif; ?>

        </article>

        <?php
        // Navigate within the same portfolio_category when possible, otherwise across all portfolio items.
        $first_cat      = ( $cats && ! is_wp_error( $cats ) ) ? $cats[0] : null;
        $nav_args       = array(
            'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous:', 'folkphotography' ) . '</span> <span class="nav-title">%title</span>',
            'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next:', 'folkphotography' ) . '</span> <span class="nav-title">%title</span>',
        );
        if ( $first_cat ) {
            $nav_args['in_same_term'] = true;
            $nav_args['taxonomy']     = 'portfolio_category';
        }
        the_post_navigation( $nav_args );
        ?>

    </div>
</main>

<?php endwhile; ?>

<?php get_footer(); ?>
