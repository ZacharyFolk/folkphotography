<?php get_header(); ?>

<?php
// Get hero image
$hero_image = folkphotography_get_hero_image();
$parallax_speed = get_theme_mod('parallax_speed', 0.5);
?>

<?php if ($hero_image) : ?>
<section class="hero-section" id="hero-section" data-parallax-speed="<?php echo esc_attr($parallax_speed); ?>">
    <div class="hero-image" style="background-image: url('<?php echo esc_url($hero_image); ?>');"></div>
    <div class="hero-overlay"></div>
</section>
<?php endif; ?>

<main class="site-content">
    <?php if (is_active_sidebar('homepage-after-hero')) : ?>
        <div class="homepage-section homepage-after-hero">
            <?php dynamic_sidebar('homepage-after-hero'); ?>
        </div>
    <?php endif; ?>

    <?php if (is_active_sidebar('homepage-featured')) : ?>
        <div class="homepage-section homepage-featured">
            <?php dynamic_sidebar('homepage-featured'); ?>
        </div>
    <?php endif; ?>

    <div class="content-wrapper">
        <?php
        while (have_posts()) :
            the_post();
        ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('page-content'); ?>>
                <?php if (get_the_title()) : ?>
                    <header class="entry-header">
                        <h1 class="entry-title"><?php the_title(); ?></h1>
                    </header>
                <?php endif; ?>

                <div class="entry-content">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile; ?>
    </div>

    <?php if (is_active_sidebar('homepage-gallery')) : ?>
        <div class="homepage-section homepage-gallery-section">
            <?php dynamic_sidebar('homepage-gallery'); ?>
        </div>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
