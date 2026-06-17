<?php
/**
 * Template Name: Masonry Gallery
 * Description: Pinterest-style masonry grid layout for portfolio showcase
 */

get_header();
?>

<main class="site-content masonry-gallery-page">
    <div class="content-wrapper">
        <?php while (have_posts()) : the_post(); ?>
            
            <header class="page-header">
                <h1 class="page-title"><?php the_title(); ?></h1>
                <?php if (get_the_content()) : ?>
                    <div class="page-intro">
                        <?php the_content(); ?>
                    </div>
                <?php endif; ?>
            </header>

            <?php
            // Get category from URL parameter or show all
            $cat_slug = isset($_GET['cat']) ? sanitize_title( wp_unslash( $_GET['cat'] ) ) : '';
            
            // Get portfolio type from URL parameter
            $portfolio_type = isset( $_GET['type'] ) ? sanitize_key( wp_unslash( $_GET['type'] ) ) : 'all';
            if ( ! in_array( $portfolio_type, array( 'all', 'portfolio', 'posts' ), true ) ) {
                $portfolio_type = 'all';
            }
            // Build query args
            $query_args = array(
                'posts_per_page' => 30,
                'post_status' => 'publish',
            );
            
            // Determine post type
            if ($portfolio_type === 'portfolio') {
                $query_args['post_type'] = 'portfolio';
            } elseif ($portfolio_type === 'posts') {
                $query_args['post_type'] = 'post';
            } else {
                // Show both
                $query_args['post_type'] = array('post', 'portfolio');
            }
            
            // Add category filter if specified.
            // Use slug-based lookup so each taxonomy resolves its own term ID independently.
            if ( $cat_slug ) {
                if ( $portfolio_type === 'portfolio' ) {
                    $query_args['tax_query'] = array(
                        array(
                            'taxonomy' => 'portfolio_category',
                            'field'    => 'slug',
                            'terms'    => $cat_slug,
                        )
                    );
                } elseif ( $portfolio_type === 'posts' ) {
                    $query_args['tax_query'] = array(
                        array(
                            'taxonomy' => 'category',
                            'field'    => 'slug',
                            'terms'    => $cat_slug,
                        )
                    );
                } else {
                    // Mixed: OR across both taxonomies, each resolved by slug in its own namespace.
                    $query_args['tax_query'] = array(
                        'relation' => 'OR',
                        array(
                            'taxonomy' => 'category',
                            'field'    => 'slug',
                            'terms'    => $cat_slug,
                        ),
                        array(
                            'taxonomy' => 'portfolio_category',
                            'field'    => 'slug',
                            'terms'    => $cat_slug,
                        ),
                    );
                }
            }
            
            // Query posts
            $masonry_query = new WP_Query($query_args);
            ?>

            <!-- Filter Options -->
            <div class="masonry-filters">
                <div class="filter-group">
                    <label><?php esc_html_e( 'Show:', 'folkphotography' ); ?></label>
                    <a href="<?php echo esc_url(remove_query_arg('type')); ?>"
                       class="filter-btn <?php echo ($portfolio_type === 'all') ? 'active' : ''; ?>">
                        <?php esc_html_e( 'All', 'folkphotography' ); ?>
                    </a>
                    <a href="<?php echo esc_url(add_query_arg('type', 'portfolio')); ?>"
                       class="filter-btn <?php echo ($portfolio_type === 'portfolio') ? 'active' : ''; ?>">
                        <?php esc_html_e( 'Portfolio', 'folkphotography' ); ?>
                    </a>
                    <a href="<?php echo esc_url(add_query_arg('type', 'posts')); ?>"
                       class="filter-btn <?php echo ($portfolio_type === 'posts') ? 'active' : ''; ?>">
                        <?php esc_html_e( 'Blog Posts', 'folkphotography' ); ?>
                    </a>
                </div>
                
                <?php
                // Get categories based on selected post type
                $filter_terms = array();
                
                if ($portfolio_type === 'portfolio') {
                    // Show portfolio categories only
                    $filter_terms = get_terms(array(
                        'taxonomy' => 'portfolio_category',
                        'hide_empty' => true,
                    ));
                } elseif ($portfolio_type === 'posts') {
                    // Show regular categories only
                    $filter_terms = get_categories(array('hide_empty' => true));
                } else {
                    // Show both - merge categories and portfolio categories
                    $post_cats = get_categories(array('hide_empty' => true));
                    $portfolio_cats = get_terms(array(
                        'taxonomy' => 'portfolio_category',
                        'hide_empty' => true,
                    ));
                    
                    // For mixed view, we need to show unique category names
                    // Note: This assumes categories and portfolio_categories share similar names
                    // In mixed mode, filtering by ID works for the matched taxonomy
                    $all_terms = array_merge(
                        is_array($post_cats) ? $post_cats : array(),
                        is_array($portfolio_cats) ? $portfolio_cats : array()
                    );
                    
                    // Remove duplicates by name
                    $unique_terms = array();
                    $seen_names = array();
                    foreach ($all_terms as $term) {
                        if (!in_array($term->name, $seen_names)) {
                            $unique_terms[] = $term;
                            $seen_names[] = $term->name;
                        }
                    }
                    $filter_terms = $unique_terms;
                }
                
                if (!empty($filter_terms) && !is_wp_error($filter_terms)) :
                ?>
                <div class="filter-group">
                    <label><?php esc_html_e( 'Category:', 'folkphotography' ); ?></label>
                    <a href="<?php echo esc_url(remove_query_arg('cat')); ?>"
                       class="filter-btn <?php echo ( $cat_slug === '' ) ? 'active' : ''; ?>">
                        <?php esc_html_e( 'All', 'folkphotography' ); ?>
                    </a>
                    <?php foreach ($filter_terms as $term) : ?>
                        <a href="<?php echo esc_url(add_query_arg('cat', $term->slug)); ?>"
                           class="filter-btn <?php echo ( $cat_slug === $term->slug ) ? 'active' : ''; ?>">
                            <?php echo esc_html($term->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($masonry_query->have_posts()) : ?>
                <div class="masonry-grid" id="masonry-container">
                    <?php while ($masonry_query->have_posts()) : $masonry_query->the_post(); ?>
                        <article class="masonry-item" data-post-id="<?php the_ID(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'large')); ?>" 
                                   class="masonry-link glightbox" 
                                   data-gallery="masonry-gallery"
                                   data-glightbox="title: <?php echo esc_attr(get_the_title()); ?>; description: .glightbox-desc-<?php the_ID(); ?>">
                                    <?php the_post_thumbnail('portfolio-large'); ?>
                                    <div class="masonry-overlay">
                                        <h3 class="masonry-title"><?php the_title(); ?></h3>
                                        <span class="masonry-type"><?php echo get_post_type() === 'portfolio' ? esc_html__( 'Portfolio', 'folkphotography' ) : esc_html__( 'Blog', 'folkphotography' ); ?></span>
                                    </div>
                                </a>
                                
                                <!-- Hidden description for lightbox -->
                                <div class="glightbox-desc glightbox-desc-<?php the_ID(); ?>" style="display:none;">
                                    <?php if (has_excerpt()) : ?>
                                        <p><?php echo wp_kses_post( get_the_excerpt() ); ?></p>
                                    <?php endif; ?>
                                    <?php
                                    // Show EXIF data if available
                                    $thumb_id  = get_post_thumbnail_id();
                                    $thumb_meta = $thumb_id ? get_post_meta( $thumb_id ) : array();
                                    $camera  = $thumb_meta['_iwh_camera_model'][0]  ?? '';
                                    $lens    = $thumb_meta['_iwh_lens'][0]           ?? '';
                                    $aperture = $thumb_meta['_iwh_aperture'][0]      ?? '';
                                    $shutter = $thumb_meta['_iwh_shutter_speed'][0]  ?? '';
                                    $iso     = $thumb_meta['_iwh_iso'][0]            ?? '';
                                    
                                    if ($camera || $lens || $aperture) :
                                    ?>
                                        <div class="exif-data">
                                            <?php if ($camera) : ?>
                                                <p><strong>Camera:</strong> <?php echo esc_html($camera); ?></p>
                                            <?php endif; ?>
                                            <?php if ($lens) : ?>
                                                <p><strong>Lens:</strong> <?php echo esc_html($lens); ?></p>
                                            <?php endif; ?>
                                            <?php if ($aperture || $shutter || $iso) : ?>
                                                <p><strong>Settings:</strong> 
                                                <?php 
                                                $settings = array();
                                                if ($aperture) $settings[] = 'f/' . $aperture;
                                                if ($shutter) $settings[] = $shutter;
                                                if ($iso) $settings[] = 'ISO ' . $iso;
                                                echo esc_html(implode(' • ', $settings));
                                                ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <a href="<?php the_permalink(); ?>" class="view-post-link">View Full Post →</a>
                                </div>
                            <?php endif; ?>
                        </article>
                    <?php endwhile; ?>
                </div>

                <?php wp_reset_postdata(); ?>

            <?php else : ?>
                <p class="no-results"><?php esc_html_e( 'No images found. Try a different filter!', 'folkphotography' ); ?></p>
            <?php endif; ?>

        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
