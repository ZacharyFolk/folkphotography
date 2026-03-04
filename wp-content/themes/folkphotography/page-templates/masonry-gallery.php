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
            $category_id = isset($_GET['cat']) ? absint($_GET['cat']) : 0;
            
            // Get portfolio type from URL parameter
            $portfolio_type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : 'all';
            
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
            
            // Add category filter if specified
            // Use tax_query to support both 'category' (posts) and 'portfolio_category' (portfolio)
            if ($category_id) {
                if ($portfolio_type === 'portfolio') {
                    // Portfolio only: use portfolio_category
                    $query_args['tax_query'] = array(
                        array(
                            'taxonomy' => 'portfolio_category',
                            'field' => 'term_id',
                            'terms' => $category_id,
                        )
                    );
                } elseif ($portfolio_type === 'posts') {
                    // Posts only: use category
                    $query_args['cat'] = $category_id;
                } else {
                    // Mixed view: use OR relation to match either taxonomy
                    $query_args['tax_query'] = array(
                        'relation' => 'OR',
                        array(
                            'taxonomy' => 'category',
                            'field' => 'term_id',
                            'terms' => $category_id,
                        ),
                        array(
                            'taxonomy' => 'portfolio_category',
                            'field' => 'term_id',
                            'terms' => $category_id,
                        )
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
                       class="filter-btn <?php echo ($category_id === 0) ? 'active' : ''; ?>">
                        <?php esc_html_e( 'All', 'folkphotography' ); ?>
                    </a>
                    <?php foreach ($filter_terms as $term) : ?>
                        <a href="<?php echo esc_url(add_query_arg('cat', $term->term_id)); ?>" 
                           class="filter-btn <?php echo ($category_id === $term->term_id) ? 'active' : ''; ?>">
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
                                    $camera = get_post_meta(get_post_thumbnail_id(), '_iwh_camera_model', true);
                                    $lens = get_post_meta(get_post_thumbnail_id(), '_iwh_lens', true);
                                    $aperture = get_post_meta(get_post_thumbnail_id(), '_iwh_aperture', true);
                                    $shutter = get_post_meta(get_post_thumbnail_id(), '_iwh_shutter_speed', true);
                                    $iso = get_post_meta(get_post_thumbnail_id(), '_iwh_iso', true);
                                    
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
                <p class="no-results"><?php _e('No images found. Try a different filter!', 'folkphotography'); ?></p>
            <?php endif; ?>

        <?php endwhile; ?>
    </div>
</main>

<script>
// Masonry layout initialization
(function() {
    function initMasonry() {
        const container = document.getElementById('masonry-container');
        if (!container) return;
        
        const items = container.querySelectorAll('.masonry-item');
        const columnCount = window.innerWidth > 1024 ? 3 : (window.innerWidth > 768 ? 2 : 1);
        const columns = Array.from({length: columnCount}, () => []);
        
        // Distribute items across columns
        items.forEach((item, index) => {
            columns[index % columnCount].push(item);
        });
        
        // Apply positioning
        container.style.position = 'relative';
        container.style.width = '100%';
        
        const columnWidth = 100 / columnCount;
        let maxHeight = 0;
        
        columns.forEach((column, colIndex) => {
            let yPos = 0;
            column.forEach(item => {
                item.style.position = 'absolute';
                item.style.left = (colIndex * columnWidth) + '%';
                item.style.top = yPos + 'px';
                item.style.width = 'calc(' + columnWidth + '% - 10px)';
                
                const height = item.offsetHeight;
                yPos += height + 10; // 10px gap
            });
            maxHeight = Math.max(maxHeight, yPos);
        });
        
        container.style.height = maxHeight + 'px';
    }
    
    // Initialize on load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMasonry);
    } else {
        initMasonry();
    }
    
    // Re-initialize on resize (debounced)
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(initMasonry, 250);
    });
    
    // Re-initialize after images load
    window.addEventListener('load', initMasonry);
})();
</script>

<?php get_footer(); ?>
