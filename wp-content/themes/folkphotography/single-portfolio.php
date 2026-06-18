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
    $make       = $thumb_id ? get_post_meta( $thumb_id, '_iwh_camera_make',   true ) : '';
    $model      = $thumb_id ? get_post_meta( $thumb_id, '_iwh_camera_model',  true ) : '';
    $lens       = $thumb_id ? get_post_meta( $thumb_id, '_iwh_lens',          true ) : '';
    $focal      = $thumb_id ? get_post_meta( $thumb_id, '_iwh_focal_length',  true ) : '';
    $aperture   = $thumb_id ? get_post_meta( $thumb_id, '_iwh_aperture',      true ) : '';
    $shutter    = $thumb_id ? get_post_meta( $thumb_id, '_iwh_shutter_speed', true ) : '';
    $iso        = $thumb_id ? get_post_meta( $thumb_id, '_iwh_iso',           true ) : '';
    $location   = $thumb_id ? get_post_meta( $thumb_id, '_iwh_place_name',    true ) : '';
    $date_taken = $thumb_id ? get_post_meta( $thumb_id, '_iwh_date_taken',    true ) : '';
    $camera     = trim( $make . ' ' . $model );
    $has_exif   = $camera || $lens || $aperture || $iso || $focal || $location || $date_taken;

    // Per-post photo meta toggle — defaults ON when the meta key has never been saved
    $show_photo_meta = get_post_meta( $post_id, '_folk_show_photo_meta', true );
    $show_photo_meta = ( $show_photo_meta === '' || $show_photo_meta === '1' );

    // Media library image description (shown when photo meta is enabled)
    $att_description = ( $show_photo_meta && $thumb_id ) ? trim( get_post_field( 'post_content', $thumb_id ) ) : '';

    // Format shutter speed fraction (e.g. "130/10" → "13s", "1/250" → "1/250s")
    $shutter_fmt = '';
    if ( $shutter ) {
        if ( strpos( $shutter, '/' ) !== false ) {
            list( $s_num, $s_den ) = explode( '/', $shutter, 2 );
            $s_num = (float) $s_num;
            $s_den = (float) $s_den;
            if ( $s_den > 0 ) {
                $s_val       = $s_num / $s_den;
                $shutter_fmt = $s_val >= 1
                    ? round( $s_val, 1 ) . 's'
                    : '1/' . round( $s_den / $s_num ) . 's';
            }
        } else {
            $shutter_fmt = $shutter . 's';
        }
    }

    // Format date taken ("2024:08:19 21:34:03" → site date format)
    $date_fmt = '';
    if ( $date_taken ) {
        $ts = strtotime( str_replace( ':', '-', substr( $date_taken, 0, 10 ) ) );
        if ( $ts ) {
            $date_fmt = date_i18n( get_option( 'date_format' ), $ts );
        }
    }
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

            <?php if ( has_excerpt() ) : ?>
                <div class="portfolio-excerpt">
                    <?php the_excerpt(); ?>
                </div>
            <?php endif; ?>

            <?php if ( $show_photo_meta && ( $att_description || $has_exif ) ) : ?>
                <div class="portfolio-photo-meta">
                    <?php if ( $att_description ) : ?>
                        <div class="photo-description">
                            <?php echo wp_kses_post( wpautop( $att_description ) ); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( $has_exif ) : ?>
                        <dl class="photo-exif">
                            <?php if ( $camera ) : ?>
                                <div class="exif-item">
                                    <dt><?php esc_html_e( 'Camera', 'folkphotography' ); ?></dt>
                                    <dd><?php echo esc_html( $camera ); ?></dd>
                                </div>
                            <?php endif; ?>
                            <?php if ( $lens ) : ?>
                                <div class="exif-item">
                                    <dt><?php esc_html_e( 'Lens', 'folkphotography' ); ?></dt>
                                    <dd><?php echo esc_html( $lens ); ?></dd>
                                </div>
                            <?php endif; ?>
                            <?php if ( $focal ) : ?>
                                <div class="exif-item">
                                    <dt><?php esc_html_e( 'Focal Length', 'folkphotography' ); ?></dt>
                                    <dd><?php echo esc_html( $focal ); ?>mm</dd>
                                </div>
                            <?php endif; ?>
                            <?php if ( $aperture ) : ?>
                                <div class="exif-item">
                                    <dt><?php esc_html_e( 'Aperture', 'folkphotography' ); ?></dt>
                                    <dd>ƒ/<?php echo esc_html( $aperture ); ?></dd>
                                </div>
                            <?php endif; ?>
                            <?php if ( $shutter_fmt ) : ?>
                                <div class="exif-item">
                                    <dt><?php esc_html_e( 'Shutter', 'folkphotography' ); ?></dt>
                                    <dd><?php echo esc_html( $shutter_fmt ); ?></dd>
                                </div>
                            <?php endif; ?>
                            <?php if ( $iso ) : ?>
                                <div class="exif-item">
                                    <dt><?php esc_html_e( 'ISO', 'folkphotography' ); ?></dt>
                                    <dd><?php echo esc_html( $iso ); ?></dd>
                                </div>
                            <?php endif; ?>
                            <?php if ( $date_fmt ) : ?>
                                <div class="exif-item">
                                    <dt><?php esc_html_e( 'Date', 'folkphotography' ); ?></dt>
                                    <dd><?php echo esc_html( $date_fmt ); ?></dd>
                                </div>
                            <?php endif; ?>
                            <?php if ( $location ) : ?>
                                <div class="exif-item">
                                    <dt><?php esc_html_e( 'Location', 'folkphotography' ); ?></dt>
                                    <dd><?php echo esc_html( $location ); ?></dd>
                                </div>
                            <?php endif; ?>
                        </dl>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ( trim( get_the_content() ) ) : ?>
                <div class="entry-content portfolio-gallery">
                    <?php the_content(); ?>
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
