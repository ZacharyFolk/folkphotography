<?php get_header(); ?>

<main class="site-content">
    <div class="content-wrapper">
        <div class="error-404 not-found">
            <header class="page-header">
                <h1 class="page-title"><?php esc_html_e( '404', 'folkphotography' ); ?></h1>
                <p><?php esc_html_e( 'Page Not Found', 'folkphotography' ); ?></p>
            </header>

            <div class="page-content">
                <p><?php esc_html_e( 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'folkphotography' ); ?></p>

                <a href="<?php echo esc_url(home_url('/')); ?>" class="button">
                    <?php esc_html_e( 'Return to Homepage', 'folkphotography' ); ?>
                </a>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>
