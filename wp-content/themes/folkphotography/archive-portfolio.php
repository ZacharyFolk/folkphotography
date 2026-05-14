<?php
/**
 * Portfolio archive template — /portfolio/
 *
 * Masonry grid of all portfolio items with portfolio_category filtering.
 * Lightbox opens the featured image; description includes EXIF + link to
 * the full single-portfolio page.
 */

get_header();

$category_id    = isset( $_GET['cat'] ) ? absint( $_GET['cat'] ) : 0;
$portfolio_cats = get_terms( array( 'taxonomy' => 'portfolio_category', 'hide_empty' => true ) );

$query_args = array(
    'post_type'      => 'portfolio',
    'post_status'    => 'publish',
    'posts_per_page' => 24,
);

if ( $category_id ) {
    $query_args['tax_query'] = array( array(
        'taxonomy' => 'portfolio_category',
        'field'    => 'term_id',
        'terms'    => $category_id,
    ) );
}

$portfolio_query = new WP_Query( $query_args );
?>

<main class="site-content portfolio-archive-page">
    <div class="content-wrapper">

        <header class="page-header">
            <?php if ( $category_id ) :
                $active_cat = get_term( $category_id, 'portfolio_category' );
                if ( $active_cat && ! is_wp_error( $active_cat ) ) : ?>
                    <h1 class="page-title"><?php echo esc_html( $active_cat->name ); ?></h1>
                    <?php if ( $active_cat->description ) : ?>
                        <p class="archive-description"><?php echo esc_html( $active_cat->description ); ?></p>
                    <?php endif; ?>
                <?php endif; ?>
            <?php else : ?>
                <h1 class="page-title"><?php esc_html_e( 'Portfolio', 'folkphotography' ); ?></h1>
            <?php endif; ?>
        </header>

        <?php if ( ! empty( $portfolio_cats ) && ! is_wp_error( $portfolio_cats ) ) : ?>
        <div class="masonry-filters">
            <div class="filter-group">
                <label><?php esc_html_e( 'Category:', 'folkphotography' ); ?></label>
                <a href="<?php echo esc_url( remove_query_arg( 'cat' ) ); ?>"
                   class="filter-btn <?php echo ( $category_id === 0 ) ? 'active' : ''; ?>">
                    <?php esc_html_e( 'All', 'folkphotography' ); ?>
                </a>
                <?php foreach ( $portfolio_cats as $term ) : ?>
                    <a href="<?php echo esc_url( add_query_arg( 'cat', $term->term_id ) ); ?>"
                       class="filter-btn <?php echo ( $category_id === $term->term_id ) ? 'active' : ''; ?>">
                        <?php echo esc_html( $term->name ); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ( $portfolio_query->have_posts() ) : ?>
            <div class="masonry-grid portfolio-archive-grid">
                <?php while ( $portfolio_query->have_posts() ) : $portfolio_query->the_post(); ?>
                    <?php if ( ! has_post_thumbnail() ) : continue; endif; ?>

                    <?php
                    $post_id    = get_the_ID();
                    $thumb_id   = get_post_thumbnail_id();
                    $thumb_url  = get_the_post_thumbnail_url( $post_id, 'large' );
                    $title      = get_the_title();
                    $desc_class = 'glightbox-desc-pa-' . $post_id;
                    $cat_terms  = get_the_terms( $post_id, 'portfolio_category' );

                    $model    = get_post_meta( $thumb_id, '_iwh_model', true );
                    $lens     = get_post_meta( $thumb_id, '_iwh_lens', true );
                    $aperture = get_post_meta( $thumb_id, '_iwh_aperture', true );
                    $shutter  = get_post_meta( $thumb_id, '_iwh_shutter_speed', true );
                    $iso      = get_post_meta( $thumb_id, '_iwh_iso', true );
                    ?>

                    <article class="masonry-item" data-post-id="<?php echo esc_attr( $post_id ); ?>">
                        <a href="<?php echo esc_url( $thumb_url ); ?>"
                           class="masonry-link glightbox"
                           data-gallery="portfolio-archive"
                           data-glightbox="title: <?php echo esc_attr( $title ); ?>; description: .<?php echo esc_attr( $desc_class ); ?>">
                            <?php the_post_thumbnail( 'portfolio-large' ); ?>
                            <div class="masonry-overlay">
                                <h3 class="masonry-title"><?php the_title(); ?></h3>
                                <?php if ( $cat_terms && ! is_wp_error( $cat_terms ) ) : ?>
                                    <span class="masonry-type"><?php echo esc_html( $cat_terms[0]->name ); ?></span>
                                <?php endif; ?>
                            </div>
                        </a>

                        <div class="glightbox-desc <?php echo esc_attr( $desc_class ); ?>" style="display:none;">
                            <?php if ( has_excerpt() ) : ?>
                                <p><?php echo wp_kses_post( get_the_excerpt() ); ?></p>
                            <?php endif; ?>
                            <?php if ( $model || $lens || $aperture ) : ?>
                                <div class="exif-data">
                                    <?php if ( $model ) : ?>
                                        <p><strong><?php esc_html_e( 'Camera:', 'folkphotography' ); ?></strong> <?php echo esc_html( $model ); ?></p>
                                    <?php endif; ?>
                                    <?php if ( $lens ) : ?>
                                        <p><strong><?php esc_html_e( 'Lens:', 'folkphotography' ); ?></strong> <?php echo esc_html( $lens ); ?></p>
                                    <?php endif; ?>
                                    <?php if ( $aperture || $shutter || $iso ) :
                                        $parts = array();
                                        if ( $aperture ) $parts[] = 'f/' . $aperture;
                                        if ( $shutter )  $parts[] = $shutter;
                                        if ( $iso )      $parts[] = 'ISO ' . $iso;
                                    ?>
                                        <p><strong><?php esc_html_e( 'Settings:', 'folkphotography' ); ?></strong> <?php echo esc_html( implode( ' • ', $parts ) ); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <a href="<?php the_permalink(); ?>" class="view-post-link">
                                <?php esc_html_e( 'View Full Portfolio Item →', 'folkphotography' ); ?>
                            </a>
                        </div>
                    </article>

                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            </div>

        <?php else : ?>
            <p class="no-results"><?php esc_html_e( 'No portfolio items found.', 'folkphotography' ); ?></p>
        <?php endif; ?>

    </div>
</main>

<?php get_footer(); ?>
