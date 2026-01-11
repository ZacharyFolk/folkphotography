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
 * Register Widgets
 */
function folkphotography_register_widgets()
{
    register_widget('FolkPhoto_Recent_Portfolio_Widget');
    register_widget('FolkPhoto_Category_Gallery_Widget');
}
add_action('widgets_init', 'folkphotography_register_widgets');
