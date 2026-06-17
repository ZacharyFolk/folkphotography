<?php
/**
 * Gutenberg block registration for FolkPhotography theme.
 */

// =============================================================================
// EDITOR ASSETS
// =============================================================================

function folkphotography_enqueue_block_editor_assets() {
    wp_enqueue_script(
        'folkphotography-block-masonry-gallery',
        get_template_directory_uri() . '/js/block-masonry-gallery.js',
        array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-data' ),
        FOLKPHOTO_VERSION,
        true
    );
}
add_action( 'enqueue_block_editor_assets', 'folkphotography_enqueue_block_editor_assets' );

// =============================================================================
// MASONRY GALLERY BLOCK
// =============================================================================

function folkphotography_register_blocks() {
    register_block_type( 'folkphotography/masonry-gallery', array(
        'editor_script'   => 'folkphotography-block-masonry-gallery',
        'attributes'      => array(
            'termId'   => array( 'type' => 'number',  'default' => 0 ),
            'taxonomy' => array( 'type' => 'string',  'default' => 'portfolio_category' ),
            'columns'  => array( 'type' => 'number',  'default' => 3 ),
            'count'    => array( 'type' => 'number',  'default' => 12 ),
        ),
        'render_callback' => 'folkphotography_render_masonry_gallery_block',
    ) );
}
add_action( 'init', 'folkphotography_register_blocks' );

/**
 * Server-side render callback for the Masonry Gallery block.
 *
 * @param  array $attributes Block attributes.
 * @return string            HTML output.
 */
function folkphotography_render_masonry_gallery_block( $attributes ) {
    $term_id  = absint( $attributes['termId'] ?? 0 );
    $taxonomy = sanitize_key( $attributes['taxonomy'] ?? 'portfolio_category' );
    $columns  = max( 2, min( 4, absint( $attributes['columns'] ?? 3 ) ) );
    $count    = max( 4, min( 30, absint( $attributes['count'] ?? 12 ) ) );

    $allowed_taxonomies = array( 'portfolio_category', 'portfolio_tag', 'category', 'post_tag' );
    if ( ! in_array( $taxonomy, $allowed_taxonomies, true ) ) {
        $taxonomy = 'portfolio_category';
    }

    $post_type = in_array( $taxonomy, array( 'portfolio_category', 'portfolio_tag' ), true )
        ? 'portfolio'
        : 'post';

    $query_args = array(
        'post_type'      => $post_type,
        'post_status'    => 'publish',
        'posts_per_page' => $count,
    );

    if ( $term_id ) {
        $query_args['tax_query'] = array( array(
            'taxonomy' => $taxonomy,
            'field'    => 'term_id',
            'terms'    => $term_id,
        ) );
    }

    $query = new WP_Query( $query_args );

    if ( ! $query->have_posts() ) {
        return '<p class="no-results">' . esc_html__( 'No images found.', 'folkphotography' ) . '</p>';
    }

    // --masonry-cols custom property lets the CSS variable system handle responsive breakpoints.
    $output  = '<div class="masonry-grid folk-masonry-block" style="--masonry-cols:' . $columns . '">';

    while ( $query->have_posts() ) {
        $query->the_post();
        if ( ! has_post_thumbnail() ) {
            continue;
        }

        $post_id   = get_the_ID();
        $thumb_id  = get_post_thumbnail_id();
        $thumb_url = get_the_post_thumbnail_url( $post_id, 'large' );
        $title     = get_the_title();
        $desc_class = 'glightbox-desc-blk-' . $post_id;

        $output .= '<article class="masonry-item">';
        $output .= '<a href="' . esc_url( $thumb_url ) . '" ';
        $output .= 'class="masonry-link glightbox" ';
        $output .= 'data-gallery="folk-masonry-blk" ';
        $output .= 'data-glightbox="title: ' . esc_attr( $title ) . '; description: .' . esc_attr( $desc_class ) . '">';
        $output .= get_the_post_thumbnail( $post_id, 'portfolio-large' );
        $output .= '<div class="masonry-overlay">';
        $output .= '<h3 class="masonry-title">' . esc_html( $title ) . '</h3>';
        $output .= '</div>';
        $output .= '</a>';

        // Hidden lightbox description — EXIF + permalink
        $make    = get_post_meta( $thumb_id, '_iwh_camera_make',  true );
        $camera  = trim( $make . ' ' . get_post_meta( $thumb_id, '_iwh_camera_model', true ) );
        $lens    = get_post_meta( $thumb_id, '_iwh_lens', true );
        $ap      = get_post_meta( $thumb_id, '_iwh_aperture', true );
        $shutter = get_post_meta( $thumb_id, '_iwh_shutter_speed', true );
        $iso     = get_post_meta( $thumb_id, '_iwh_iso', true );

        $output .= '<div class="glightbox-desc ' . esc_attr( $desc_class ) . '" style="display:none;">';
        if ( $camera || $lens || $ap ) {
            $output .= '<div class="exif-data">';
            if ( $camera )               $output .= '<p><strong>' . esc_html__( 'Camera:', 'folkphotography' ) . '</strong> ' . esc_html( $camera ) . '</p>';
            if ( $lens )                 $output .= '<p><strong>' . esc_html__( 'Lens:', 'folkphotography' ) . '</strong> ' . esc_html( $lens ) . '</p>';
            if ( $ap || $shutter || $iso ) {
                $parts = array();
                if ( $ap )      $parts[] = 'f/' . $ap;
                if ( $shutter ) $parts[] = $shutter;
                if ( $iso )     $parts[] = 'ISO ' . $iso;
                $output .= '<p><strong>' . esc_html__( 'Settings:', 'folkphotography' ) . '</strong> ' . esc_html( implode( ' • ', $parts ) ) . '</p>';
            }
            $output .= '</div>';
        }
        $output .= '<a href="' . esc_url( get_the_permalink() ) . '" class="view-post-link">' . esc_html__( 'View Full Post →', 'folkphotography' ) . '</a>';
        $output .= '</div>';

        $output .= '</article>';
    }
    wp_reset_postdata();

    $output .= '</div>';
    return $output;
}
