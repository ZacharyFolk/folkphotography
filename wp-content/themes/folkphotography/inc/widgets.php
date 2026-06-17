<?php
/**
 * Custom Widgets for FolkPhotography Theme
 */

/**
 * Recent Portfolio Widget
 */
class FolkPhoto_Recent_Portfolio_Widget extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'folkphoto_recent_portfolio',
            __('Recent Portfolio Items', 'folkphotography'),
            array('description' => __('Display recent portfolio items in a grid', 'folkphotography'))
        );
    }

    public function widget($args, $instance)
    {
        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        $number = !empty($instance['number']) ? absint($instance['number']) : 6;
        $columns = !empty($instance['columns']) ? absint($instance['columns']) : 3;

        $query = new WP_Query(array(
            'post_type' => 'portfolio',
            'posts_per_page' => $number,
            'post_status' => 'publish',
        ));

        if ($query->have_posts()) :
            echo '<div class="portfolio-grid grid-columns-' . esc_attr($columns) . '">';
            while ($query->have_posts()) : $query->the_post();
                ?>
                <div class="portfolio-item">
                    <a href="<?php the_permalink(); ?>" class="portfolio-link">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('portfolio-medium'); ?>
                        <?php endif; ?>
                        <div class="portfolio-overlay">
                            <h3><?php the_title(); ?></h3>
                        </div>
                    </a>
                </div>
                <?php
            endwhile;
            echo '</div>';
            wp_reset_postdata();
        endif;

        echo $args['after_widget'];
    }

    public function form($instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $number = !empty($instance['number']) ? absint($instance['number']) : 6;
        $columns = !empty($instance['columns']) ? absint($instance['columns']) : 3;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e( 'Title:', 'folkphotography' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('number')); ?>"><?php esc_html_e( 'Number of items:', 'folkphotography' ); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('number')); ?>"
                name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="number" step="1" min="1"
                value="<?php echo esc_attr($number); ?>" size="3">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('columns')); ?>"><?php esc_html_e( 'Columns:', 'folkphotography' ); ?></label>
            <select id="<?php echo esc_attr($this->get_field_id('columns')); ?>"
                name="<?php echo esc_attr($this->get_field_name('columns')); ?>">
                <option value="2" <?php selected($columns, 2); ?>>2</option>
                <option value="3" <?php selected($columns, 3); ?>>3</option>
                <option value="4" <?php selected($columns, 4); ?>>4</option>
                <option value="6" <?php selected($columns, 6); ?>>6</option>
            </select>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? absint($new_instance['number']) : 6;
        $instance['columns'] = (!empty($new_instance['columns'])) ? absint($new_instance['columns']) : 3;
        return $instance;
    }
}

/**
 * Category Gallery Widget
 */
class FolkPhoto_Category_Gallery_Widget extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'folkphoto_category_gallery',
            __('Category Gallery', 'folkphotography'),
            array('description' => __('Display images from a specific category', 'folkphotography'))
        );
    }

    public function widget($args, $instance)
    {
        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        $category = !empty($instance['category']) ? absint($instance['category']) : 0;
        $number = !empty($instance['number']) ? absint($instance['number']) : 9;
        $columns = !empty($instance['columns']) ? absint($instance['columns']) : 3;

        if ($category) {
            $query = new WP_Query(array(
                'cat' => $category,
                'posts_per_page' => $number,
                'post_status' => 'publish',
            ));

            if ($query->have_posts()) :
                echo '<div class="category-gallery-grid grid-columns-' . esc_attr($columns) . '">';
                while ($query->have_posts()) : $query->the_post();
                    if (has_post_thumbnail()) :
                        ?>
                        <div class="gallery-item">
                            <a href="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'large')); ?>"
                                class="glightbox" data-gallery="category-gallery"
                                data-glightbox="title: <?php echo esc_attr(get_the_title()); ?>; description: .glightbox-desc-<?php the_ID(); ?>">
                                <?php the_post_thumbnail('gallery-thumb'); ?>
                            </a>
                            <div class="glightbox-desc glightbox-desc-<?php the_ID(); ?>" style="display:none;">
                                <p><?php the_excerpt(); ?></p>
                                <a href="<?php the_permalink(); ?>" class="view-post-link">View Full Post →</a>
                            </div>
                        </div>
                        <?php
                    endif;
                endwhile;
                echo '</div>';
                wp_reset_postdata();
            endif;
        }

        echo $args['after_widget'];
    }

    public function form($instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $category = !empty($instance['category']) ? absint($instance['category']) : 0;
        $number = !empty($instance['number']) ? absint($instance['number']) : 9;
        $columns = !empty($instance['columns']) ? absint($instance['columns']) : 3;

        $categories = get_categories(array('hide_empty' => false));
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e( 'Title:', 'folkphotography' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('category')); ?>"><?php esc_html_e( 'Category:', 'folkphotography' ); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('category')); ?>"
                name="<?php echo esc_attr($this->get_field_name('category')); ?>">
                <option value="0"><?php esc_html_e( 'Select Category', 'folkphotography' ); ?></option>
                <?php foreach ($categories as $cat) : ?>
                    <option value="<?php echo esc_attr($cat->term_id); ?>" <?php selected($category, $cat->term_id); ?>>
                        <?php echo esc_html($cat->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('number')); ?>"><?php esc_html_e( 'Number of images:', 'folkphotography' ); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('number')); ?>"
                name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="number" step="1" min="1"
                value="<?php echo esc_attr($number); ?>" size="3">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('columns')); ?>"><?php esc_html_e( 'Columns:', 'folkphotography' ); ?></label>
            <select id="<?php echo esc_attr($this->get_field_id('columns')); ?>"
                name="<?php echo esc_attr($this->get_field_name('columns')); ?>">
                <option value="2" <?php selected($columns, 2); ?>>2</option>
                <option value="3" <?php selected($columns, 3); ?>>3</option>
                <option value="4" <?php selected($columns, 4); ?>>4</option>
                <option value="6" <?php selected($columns, 6); ?>>6</option>
            </select>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['category'] = (!empty($new_instance['category'])) ? absint($new_instance['category']) : 0;
        $instance['number'] = (!empty($new_instance['number'])) ? absint($new_instance['number']) : 9;
        $instance['columns'] = (!empty($new_instance['columns'])) ? absint($new_instance['columns']) : 3;
        return $instance;
    }
}

/**
 * Location Map Widget
 */
class FolkPhoto_Location_Map_Widget extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'folkphoto_location_map',
            __('Photo Location Map', 'folkphotography'),
            array('description' => __('Display an interactive map showing photo locations from GPS data', 'folkphotography'))
        );
    }

    public function widget($args, $instance)
    {
        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        $height = !empty($instance['height']) ? absint($instance['height']) : 500;
        $category = !empty($instance['category']) ? absint($instance['category']) : 0;

        // Determine maximum number of images to load for the map (filterable).
        $max_markers = apply_filters('folkphoto_location_map_posts_per_page', 500);

        // Get images with non-empty GPS data (limited for performance).
        $args_query = array(
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'post_status'    => 'inherit',
            'posts_per_page' => $max_markers,
            'fields'         => 'ids',
            'meta_query'     => array(
                array(
                    'key'     => '_iwh_lat',
                    'value'   => '',
                    'compare' => '!=',
                ),
                array(
                    'key'     => '_iwh_lng',
                    'value'   => '',
                    'compare' => '!=',
                ),
            ),
        );

        // Add category filter if selected
        if ($category) {
            $args_query['tax_query'] = array(
                array(
                    'taxonomy' => 'category',
                    'field'    => 'term_id',
                    'terms'    => $category,
                ),
            );
        }

        $image_ids = get_posts($args_query);

        if (!empty($image_ids)) :
            // Generate unique ID for this map
            $map_id = 'map-' . uniqid();

            // Prepare markers data
            $markers = array();
            foreach ($image_ids as $image_id) {
                $lat = get_post_meta($image_id, '_iwh_lat', true);
                $lng = get_post_meta($image_id, '_iwh_lng', true);

                if ($lat && $lng) {
                    $markers[] = array(
                        'lat'        => floatval($lat),
                        'lng'        => floatval($lng),
                        'title'      => get_the_title($image_id),
                        'image'      => wp_get_attachment_image_url($image_id, 'thumbnail'),
                        'full_image' => wp_get_attachment_image_url($image_id, 'large'),
                        'post_url'   => get_attachment_link($image_id),
                    );
                }
            }
            ?>
            <div id="<?php echo esc_attr($map_id); ?>" class="folkphoto-location-map" style="height: <?php echo esc_attr($height); ?>px; width: 100%;"></div>
            <script>
            (function() {
                if (typeof L === 'undefined') {
                    console.error('Leaflet not loaded');
                    return;
                }
                
                var map = L.map('<?php echo esc_js($map_id); ?>').setView([20, 0], 2);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors',
                    maxZoom: 18
                }).addTo(map);
                
                var markers = <?php echo wp_json_encode($markers); ?>;
                var bounds = [];
                
                markers.forEach(function(marker) {
                    // Build popup content using DOM APIs to avoid HTML injection
                    var popupEl = document.createElement('div');
                    popupEl.className = 'map-popup';

                    if (marker.image) {
                        var imgEl = document.createElement('img');
                        imgEl.setAttribute('src', marker.image);
                        imgEl.setAttribute('alt', marker.title || '');
                        imgEl.style.width = '100%';
                        imgEl.style.maxWidth = '200px';
                        imgEl.style.display = 'block';
                        imgEl.style.marginBottom = '10px';
                        popupEl.appendChild(imgEl);
                    }

                    if (marker.title) {
                        var titleEl = document.createElement('strong');
                        titleEl.textContent = marker.title;
                        popupEl.appendChild(titleEl);
                        popupEl.appendChild(document.createElement('br'));
                    }

                    if (marker.full_image) {
                        var fullLink = document.createElement('a');
                        fullLink.setAttribute('href', marker.full_image);
                        fullLink.className = 'glightbox';
                        fullLink.setAttribute('data-gallery', 'map-images');
                        fullLink.textContent = 'View Full Size';
                        popupEl.appendChild(fullLink);
                    }

                    if (marker.full_image && marker.post_url) {
                        popupEl.appendChild(document.createTextNode(' | '));
                    }

                    if (marker.post_url) {
                        var postLink = document.createElement('a');
                        postLink.setAttribute('href', marker.post_url);
                        postLink.textContent = 'View Post';
                        popupEl.appendChild(postLink);
                    }
                    
                    var mapMarker = L.marker([marker.lat, marker.lng]).addTo(map)
                        .bindPopup(popupEl);
                    
                    bounds.push([marker.lat, marker.lng]);
                });
                
                // Fit map to show all markers
                if (bounds.length > 0) {
                    map.fitBounds(bounds, {padding: [50, 50]});
                }
                
                // Reinitialize GLightbox for map popups
                if (typeof GLightbox !== 'undefined') {
                    setTimeout(function() {
                        GLightbox({selector: '.map-popup .glightbox'});
                    }, 500);
                }
            })();
            </script>
            <?php
        else :
            echo '<p>' . esc_html__( 'No photos with GPS data found.', 'folkphotography' ) . '</p>';
        endif;

        echo $args['after_widget'];
    }

    public function form($instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : __('Photo Locations', 'folkphotography');
        $height = !empty($instance['height']) ? absint($instance['height']) : 500;
        $category = !empty($instance['category']) ? absint($instance['category']) : 0;

        $categories = get_categories(array('hide_empty' => false));
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e( 'Title:', 'folkphotography' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('height')); ?>"><?php esc_html_e( 'Map Height (px):', 'folkphotography' ); ?></label>
            <input class="small-text" id="<?php echo esc_attr($this->get_field_id('height')); ?>"
                name="<?php echo esc_attr($this->get_field_name('height')); ?>" type="number" step="50" min="300"
                value="<?php echo esc_attr($height); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('category')); ?>"><?php esc_html_e( 'Filter by Category (optional):', 'folkphotography' ); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('category')); ?>"
                name="<?php echo esc_attr($this->get_field_name('category')); ?>">
                <option value="0"><?php esc_html_e( 'All Categories', 'folkphotography' ); ?></option>
                <?php foreach ($categories as $cat) : ?>
                    <option value="<?php echo esc_attr($cat->term_id); ?>" <?php selected($category, $cat->term_id); ?>>
                        <?php echo esc_html($cat->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['height'] = (!empty($new_instance['height'])) ? absint($new_instance['height']) : 500;
        $instance['category'] = (!empty($new_instance['category'])) ? absint($new_instance['category']) : 0;
        return $instance;
    }
}

/**
 * Camera Stats Widget
 */
class FolkPhoto_Camera_Stats_Widget extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'folkphoto_camera_stats',
            __('Camera & Photography Stats', 'folkphotography'),
            array('description' => __('Display photography statistics from EXIF data', 'folkphotography'))
        );
    }

    public function widget($args, $instance)
    {
        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        // Get all stats from images with EXIF data
        $stats = $this->get_camera_stats();

        if (!empty($stats)) :
            ?>
            <div class="camera-stats-widget">
                <?php if (!empty($stats['total_photos'])) : ?>
                    <div class="stat-item">
                        <span class="stat-label"><?php esc_html_e( 'Total Photos', 'folkphotography' ); ?></span>
                        <span class="stat-value"><?php echo esc_html(number_format($stats['total_photos'])); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($stats['favorite_camera'])) : ?>
                    <div class="stat-item">
                        <span class="stat-label"><?php esc_html_e( 'Favorite Camera', 'folkphotography' ); ?></span>
                        <span class="stat-value"><?php echo esc_html($stats['favorite_camera']); ?></span>
                        <?php if (!empty($stats['camera_count'])) : ?>
                            <span class="stat-detail"><?php printf( esc_html__( '%s photos', 'folkphotography' ), esc_html($stats['camera_count']) ); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($stats['favorite_lens'])) : ?>
                    <div class="stat-item">
                        <span class="stat-label"><?php esc_html_e( 'Favorite Lens', 'folkphotography' ); ?></span>
                        <span class="stat-value"><?php echo esc_html($stats['favorite_lens']); ?></span>
                        <?php if (!empty($stats['lens_count'])) : ?>
                            <span class="stat-detail"><?php printf( esc_html__( '%s photos', 'folkphotography' ), esc_html($stats['lens_count']) ); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($stats['favorite_aperture'])) : ?>
                    <div class="stat-item">
                        <span class="stat-label"><?php esc_html_e( 'Favorite Aperture', 'folkphotography' ); ?></span>
                        <span class="stat-value">f/<?php echo esc_html($stats['favorite_aperture']); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($stats['favorite_iso'])) : ?>
                    <div class="stat-item">
                        <span class="stat-label"><?php esc_html_e( 'Most Used ISO', 'folkphotography' ); ?></span>
                        <span class="stat-value">ISO <?php echo esc_html($stats['favorite_iso']); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($stats['locations_count'])) : ?>
                    <div class="stat-item">
                        <span class="stat-label"><?php esc_html_e( 'Shooting Locations', 'folkphotography' ); ?></span>
                        <span class="stat-value"><?php printf( esc_html__( '%s places', 'folkphotography' ), esc_html($stats['locations_count']) ); ?></span>
                    </div>
                <?php endif; ?>
            </div>
            <?php
        else:
            echo '<p>' . esc_html__( 'No camera data available yet. Upload photos with EXIF data!', 'folkphotography' ) . '</p>';
        endif;

        echo $args['after_widget'];
    }

    private function get_camera_stats()
    {
        global $wpdb;
        $stats = array();

        // Total photos with any EXIF data (uses the dedicated flag set by the plugin)
        $total = $wpdb->get_var(
            "SELECT COUNT(DISTINCT pm.post_id)
             FROM {$wpdb->postmeta} pm
             INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
             WHERE pm.meta_key = '_iwh_has_exif'
             AND pm.meta_value = '1'
             AND p.post_type = 'attachment'"
        );
        $stats['total_photos'] = $total;

        // Favorite camera (most used)
        $camera = $wpdb->get_row(
            "SELECT meta_value, COUNT(*) as count 
             FROM {$wpdb->postmeta} 
             WHERE meta_key = '_iwh_camera_model' 
             AND meta_value != '' 
             GROUP BY meta_value 
             ORDER BY count DESC 
             LIMIT 1"
        );
        if ($camera) {
            $stats['favorite_camera'] = $camera->meta_value;
            $stats['camera_count'] = $camera->count;
        }

        // Favorite lens
        $lens = $wpdb->get_row(
            "SELECT meta_value, COUNT(*) as count 
             FROM {$wpdb->postmeta} 
             WHERE meta_key = '_iwh_lens' 
             AND meta_value != '' 
             GROUP BY meta_value 
             ORDER BY count DESC 
             LIMIT 1"
        );
        if ($lens) {
            $stats['favorite_lens'] = $lens->meta_value;
            $stats['lens_count'] = $lens->count;
        }

        // Favorite aperture
        $aperture = $wpdb->get_var(
            "SELECT meta_value 
             FROM {$wpdb->postmeta} 
             WHERE meta_key = '_iwh_aperture' 
             AND meta_value != '' 
             GROUP BY meta_value 
             ORDER BY COUNT(*) DESC 
             LIMIT 1"
        );
        if ($aperture) {
            $stats['favorite_aperture'] = $aperture;
        }

        // Most used ISO
        $iso = $wpdb->get_var(
            "SELECT meta_value 
             FROM {$wpdb->postmeta} 
             WHERE meta_key = '_iwh_iso' 
             AND meta_value != '' 
             GROUP BY meta_value 
             ORDER BY COUNT(*) DESC 
             LIMIT 1"
        );
        if ($iso) {
            $stats['favorite_iso'] = $iso;
        }

        // Count of unique locations (photos with GPS)
        $locations = $wpdb->get_var(
            "SELECT COUNT(DISTINCT CONCAT(lat.meta_value, ',', lng.meta_value)) 
             FROM {$wpdb->postmeta} lat
             INNER JOIN {$wpdb->postmeta} lng ON lat.post_id = lng.post_id
             WHERE lat.meta_key = '_iwh_lat' 
             AND lng.meta_key = '_iwh_lng'
             AND lat.meta_value != '' 
             AND lng.meta_value != ''"
        );
        if ($locations) {
            $stats['locations_count'] = $locations;
        }

        return $stats;
    }

    public function form($instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : __('By The Numbers', 'folkphotography');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e( 'Title:', 'folkphotography' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                value="<?php echo esc_attr($title); ?>">
        </p>
        <p class="description">
            <?php esc_html_e( 'This widget automatically displays statistics from your photo EXIF data. No configuration needed!', 'folkphotography' ); ?>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }
}

/**
 * Random Category Photos Widget
 *
 * Displays a randomised set of photos from a chosen taxonomy term.
 * Image source: featured image of each queried post.
 * Supports masonry, grid, and strip layouts.
 */
class FolkPhoto_Random_Category_Widget extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'folkphoto_random_category',
            __( 'Random Category Photos', 'folkphotography' ),
            array( 'description' => __( 'Display random photos from a selected category in masonry, grid, or strip layout.', 'folkphotography' ) )
        );
    }

    public function widget( $args, $instance )
    {
        $title    = apply_filters( 'widget_title', $instance['title'] ?? '' );
        $taxonomy = $instance['taxonomy'] ?? 'portfolio_category';
        $term_id  = absint( $instance['term_id'] ?? 0 );
        $layout   = $instance['layout'] ?? 'masonry';
        $count    = absint( $instance['count'] ?? 8 );

        echo $args['before_widget'];

        if ( $title ) {
            echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
        }

        $post_type = in_array( $taxonomy, array( 'portfolio_category', 'portfolio_tag' ), true ) ? 'portfolio' : 'post';

        // Fetch more than needed to account for posts without thumbnails.
        $query_args = array(
            'post_type'      => $post_type,
            'post_status'    => 'publish',
            'posts_per_page' => $count * 3,
            'orderby'        => 'rand',
            'meta_key'       => '_thumbnail_id',
            'meta_compare'   => 'EXISTS',
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
            echo '<p class="no-results">' . esc_html__( 'No photos found.', 'folkphotography' ) . '</p>';
            echo $args['after_widget'];
            return;
        }

        if ( 'masonry' === $layout ) {
            echo '<div class="masonry-grid folk-random-masonry">';
        } elseif ( 'grid' === $layout ) {
            echo '<div class="category-gallery-grid grid-columns-3 folk-random-grid">';
        } else {
            echo '<div class="folk-random-strip">';
        }

        $shown = 0;
        while ( $query->have_posts() && $shown < $count ) {
            $query->the_post();
            if ( ! has_post_thumbnail() ) {
                continue;
            }

            $post_id    = get_the_ID();
            $thumb_id   = get_post_thumbnail_id();
            $thumb_url  = get_the_post_thumbnail_url( $post_id, 'large' );
            $title_str  = get_the_title();
            $desc_class = 'glightbox-rdesc-' . $post_id;

            if ( 'masonry' === $layout ) {
                echo '<article class="masonry-item">';
            } elseif ( 'grid' === $layout ) {
                echo '<div class="gallery-item">';
            } else {
                echo '<div class="strip-item">';
            }

            echo '<a href="' . esc_url( $thumb_url ) . '" ';
            echo 'class="glightbox" ';
            echo 'data-gallery="folk-rand-cat-widget" ';
            echo 'data-glightbox="title: ' . esc_attr( $title_str ) . '; description: .' . esc_attr( $desc_class ) . '">';
            the_post_thumbnail( 'portfolio-medium' );
            echo '</a>';

            // Hidden EXIF + permalink for lightbox description
            $make    = get_post_meta( $thumb_id, '_iwh_camera_make',  true );
            $camera  = trim( $make . ' ' . get_post_meta( $thumb_id, '_iwh_camera_model', true ) );
            $lens    = get_post_meta( $thumb_id, '_iwh_lens', true );
            $ap      = get_post_meta( $thumb_id, '_iwh_aperture', true );
            $shutter = get_post_meta( $thumb_id, '_iwh_shutter_speed', true );
            $iso     = get_post_meta( $thumb_id, '_iwh_iso', true );

            echo '<div class="glightbox-desc ' . esc_attr( $desc_class ) . '" style="display:none;">';
            if ( $camera || $lens || $ap ) {
                echo '<div class="exif-data">';
                if ( $camera ) {
                    echo '<p><strong>' . esc_html__( 'Camera:', 'folkphotography' ) . '</strong> ' . esc_html( $camera ) . '</p>';
                }
                if ( $lens ) {
                    echo '<p><strong>' . esc_html__( 'Lens:', 'folkphotography' ) . '</strong> ' . esc_html( $lens ) . '</p>';
                }
                if ( $ap || $shutter || $iso ) {
                    $parts = array();
                    if ( $ap )      $parts[] = 'f/' . $ap;
                    if ( $shutter ) $parts[] = $shutter;
                    if ( $iso )     $parts[] = 'ISO ' . $iso;
                    echo '<p><strong>' . esc_html__( 'Settings:', 'folkphotography' ) . '</strong> ' . esc_html( implode( ' • ', $parts ) ) . '</p>';
                }
                echo '</div>';
            }
            echo '<a href="' . esc_url( get_the_permalink() ) . '" class="view-post-link">' . esc_html__( 'View Full Post →', 'folkphotography' ) . '</a>';
            echo '</div>';

            echo ( 'masonry' === $layout ) ? '</article>' : '</div>';
            $shown++;
        }
        wp_reset_postdata();

        echo '</div>';
        echo $args['after_widget'];
    }

    public function form( $instance )
    {
        $title    = $instance['title'] ?? '';
        $taxonomy = $instance['taxonomy'] ?? 'portfolio_category';
        $term_id  = absint( $instance['term_id'] ?? 0 );
        $layout   = $instance['layout'] ?? 'masonry';
        $count    = absint( $instance['count'] ?? 8 );

        $terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => true ) );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'folkphotography' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text"
                value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'taxonomy' ) ); ?>"><?php esc_html_e( 'Taxonomy:', 'folkphotography' ); ?></label>
            <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'taxonomy' ) ); ?>"
                name="<?php echo esc_attr( $this->get_field_name( 'taxonomy' ) ); ?>">
                <option value="portfolio_category" <?php selected( $taxonomy, 'portfolio_category' ); ?>><?php esc_html_e( 'Portfolio Categories', 'folkphotography' ); ?></option>
                <option value="category" <?php selected( $taxonomy, 'category' ); ?>><?php esc_html_e( 'Blog Categories', 'folkphotography' ); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'term_id' ) ); ?>"><?php esc_html_e( 'Category:', 'folkphotography' ); ?></label>
            <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'term_id' ) ); ?>"
                name="<?php echo esc_attr( $this->get_field_name( 'term_id' ) ); ?>">
                <option value="0"><?php esc_html_e( 'All', 'folkphotography' ); ?></option>
                <?php if ( ! is_wp_error( $terms ) ) : foreach ( $terms as $term ) : ?>
                    <option value="<?php echo esc_attr( $term->term_id ); ?>" <?php selected( $term_id, $term->term_id ); ?>>
                        <?php echo esc_html( $term->name ); ?>
                    </option>
                <?php endforeach; endif; ?>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'layout' ) ); ?>"><?php esc_html_e( 'Layout:', 'folkphotography' ); ?></label>
            <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'layout' ) ); ?>"
                name="<?php echo esc_attr( $this->get_field_name( 'layout' ) ); ?>">
                <option value="masonry" <?php selected( $layout, 'masonry' ); ?>><?php esc_html_e( 'Masonry', 'folkphotography' ); ?></option>
                <option value="grid"    <?php selected( $layout, 'grid' ); ?>><?php esc_html_e( 'Grid (3 col)', 'folkphotography' ); ?></option>
                <option value="strip"   <?php selected( $layout, 'strip' ); ?>><?php esc_html_e( 'Horizontal Strip', 'folkphotography' ); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php esc_html_e( 'Number of photos:', 'folkphotography' ); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"
                name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>"
                type="number" step="1" min="2" max="20"
                value="<?php echo esc_attr( $count ); ?>" size="3">
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance )
    {
        $allowed_layouts   = array( 'masonry', 'grid', 'strip' );
        $allowed_taxonomies = array( 'portfolio_category', 'category' );

        $instance             = array();
        $instance['title']    = sanitize_text_field( $new_instance['title'] ?? '' );
        $instance['taxonomy'] = in_array( $new_instance['taxonomy'] ?? '', $allowed_taxonomies, true ) ? $new_instance['taxonomy'] : 'portfolio_category';
        $instance['term_id']  = absint( $new_instance['term_id'] ?? 0 );
        $instance['layout']   = in_array( $new_instance['layout'] ?? '', $allowed_layouts, true ) ? $new_instance['layout'] : 'masonry';
        $instance['count']    = max( 2, min( 20, absint( $new_instance['count'] ?? 8 ) ) );
        return $instance;
    }
}

/**
 * Register Widgets
 */
function folkphotography_register_widgets()
{
    register_widget('FolkPhoto_Recent_Portfolio_Widget');
    register_widget('FolkPhoto_Category_Gallery_Widget');
    register_widget('FolkPhoto_Location_Map_Widget');
    register_widget('FolkPhoto_Camera_Stats_Widget');
    register_widget('FolkPhoto_Random_Category_Widget');
}
add_action('widgets_init', 'folkphotography_register_widgets');
