<?php

/**
 * Title: Featured Gallery
 * Slug: simplefolk/featured-gallery
 * Categories: gallery
 * Keywords: featured, gallery, images
 * Inserter: yes
 * 
 * @package simplefolk
 */

$args = array(
    'post_type' => 'attachment',
    'post_status' => 'inherit',
    'posts_per_page' => 6, // Adjust to fetch the number of images you want
    'orderby' => 'date', // Ordering by date to get the latest
    'order' => 'DESC', // Order descending to get the latest first
);

$atta_query = new WP_Query($args);

if ($atta_query->have_posts()) : ?>
<div class="featured-gallery-content">
    <h2><?php echo esc_html(get_theme_mod('featured_heading', 'Featured Collections')); ?></h2>

    <div class="gallery-items">
        <?php while ($atta_query->have_posts()) : $atta_query->the_post();
                $id = get_the_ID();
            ?>
        <article class="gallery-item">
            <?php echo get_lightbox_image($id); ?>
        </article>
        <?php endwhile; ?>
    </div>
</div>
<?php
endif;
wp_reset_postdata();
?>