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
                    <a href="<?php the_permalink(); ?>" class="portfolio-link glightbox" data-gallery="portfolio">
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
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'folkphotography'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('number')); ?>"><?php _e('Number of items:', 'folkphotography'); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('number')); ?>"
                name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="number" step="1" min="1"
                value="<?php echo esc_attr($number); ?>" size="3">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('columns')); ?>"><?php _e('Columns:', 'folkphotography'); ?></label>
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
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'folkphotography'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('category')); ?>"><?php _e('Category:', 'folkphotography'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('category')); ?>"
                name="<?php echo esc_attr($this->get_field_name('category')); ?>">
                <option value="0"><?php _e('Select Category', 'folkphotography'); ?></option>
                <?php foreach ($categories as $cat) : ?>
                    <option value="<?php echo esc_attr($cat->term_id); ?>" <?php selected($category, $cat->term_id); ?>>
                        <?php echo esc_html($cat->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('number')); ?>"><?php _e('Number of images:', 'folkphotography'); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('number')); ?>"
                name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="number" step="1" min="1"
                value="<?php echo esc_attr($number); ?>" size="3">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('columns')); ?>"><?php _e('Columns:', 'folkphotography'); ?></label>
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

        // Get images with GPS data (limited for performance).
        $args_query = array(
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'post_status' => 'inherit',
            'posts_per_page' => $max_markers,
            'meta_query' => array(
                array(
                    'key' => '_iwh_lat',
                    'compare' => 'EXISTS'
                ),
                array(
                    'key' => '_iwh_lng',
                    'compare' => 'EXISTS'
                )
            )
        );

        // Add category filter if selected
        if ($category) {
            $args_query['tax_query'] = array(
                array(
                    'taxonomy' => 'category',
                    'field' => 'term_id',
                    'terms' => $category,
                )
            );
        }

        $images = get_posts($args_query);

        if (!empty($images)) :
            // Generate unique ID for this map
            $map_id = 'map-' . uniqid();
            
            // Prepare markers data
            $markers = array();
            foreach ($images as $image) {
                $lat = get_post_meta($image->ID, '_iwh_lat', true);
                $lng = get_post_meta($image->ID, '_iwh_lng', true);
                
                if ($lat && $lng) {
                    $markers[] = array(
                        'lat' => floatval($lat),
                        'lng' => floatval($lng),
                        'title' => get_the_title($image->ID),
                        'image' => wp_get_attachment_image_url($image->ID, 'thumbnail'),
                        'full_image' => wp_get_attachment_image_url($image->ID, 'large'),
                        'post_url' => get_attachment_link($image->ID)
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
        else:
            echo '<p>' . __('No photos with GPS data found.', 'folkphotography') . '</p>';
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
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'folkphotography'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('height')); ?>"><?php _e('Map Height (px):', 'folkphotography'); ?></label>
            <input class="small-text" id="<?php echo esc_attr($this->get_field_id('height')); ?>"
                name="<?php echo esc_attr($this->get_field_name('height')); ?>" type="number" step="50" min="300"
                value="<?php echo esc_attr($height); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('category')); ?>"><?php _e('Filter by Category (optional):', 'folkphotography'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('category')); ?>"
                name="<?php echo esc_attr($this->get_field_name('category')); ?>">
                <option value="0"><?php _e('All Categories', 'folkphotography'); ?></option>
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
                        <span class="stat-label">Total Photos</span>
                        <span class="stat-value"><?php echo esc_html(number_format($stats['total_photos'])); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($stats['favorite_camera'])) : ?>
                    <div class="stat-item">
                        <span class="stat-label">Favorite Camera</span>
                        <span class="stat-value"><?php echo esc_html($stats['favorite_camera']); ?></span>
                        <?php if (!empty($stats['camera_count'])) : ?>
                            <span class="stat-detail"><?php echo esc_html($stats['camera_count']); ?> photos</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($stats['favorite_lens'])) : ?>
                    <div class="stat-item">
                        <span class="stat-label">Favorite Lens</span>
                        <span class="stat-value"><?php echo esc_html($stats['favorite_lens']); ?></span>
                        <?php if (!empty($stats['lens_count'])) : ?>
                            <span class="stat-detail"><?php echo esc_html($stats['lens_count']); ?> photos</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($stats['favorite_aperture'])) : ?>
                    <div class="stat-item">
                        <span class="stat-label">Favorite Aperture</span>
                        <span class="stat-value">f/<?php echo esc_html($stats['favorite_aperture']); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($stats['favorite_iso'])) : ?>
                    <div class="stat-item">
                        <span class="stat-label">Most Used ISO</span>
                        <span class="stat-value">ISO <?php echo esc_html($stats['favorite_iso']); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($stats['locations_count'])) : ?>
                    <div class="stat-item">
                        <span class="stat-label">Shooting Locations</span>
                        <span class="stat-value"><?php echo esc_html($stats['locations_count']); ?> places</span>
                    </div>
                <?php endif; ?>
            </div>
            <?php
        else:
            echo '<p>' . __('No camera data available yet. Upload photos with EXIF data!', 'folkphotography') . '</p>';
        endif;

        echo $args['after_widget'];
    }

    private function get_camera_stats()
    {
        global $wpdb;
        $stats = array();

        // Total photos with EXIF
        $total = $wpdb->get_var(
            "SELECT COUNT(DISTINCT post_id) 
             FROM {$wpdb->postmeta} 
             WHERE meta_key = '_iwh_camera_make' 
             AND meta_value != ''"
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
             WHERE lat.meta_key = '_iwh_latitude' 
             AND lng.meta_key = '_iwh_longitude'
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
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'folkphotography'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                value="<?php echo esc_attr($title); ?>">
        </p>
        <p class="description">
            <?php _e('This widget automatically displays statistics from your photo EXIF data. No configuration needed!', 'folkphotography'); ?>
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
 * Register Widgets
 */
function folkphotography_register_widgets()
{
    register_widget('FolkPhoto_Recent_Portfolio_Widget');
    register_widget('FolkPhoto_Category_Gallery_Widget');
    register_widget('FolkPhoto_Location_Map_Widget');
    register_widget('FolkPhoto_Camera_Stats_Widget');
}
add_action('widgets_init', 'folkphotography_register_widgets');
