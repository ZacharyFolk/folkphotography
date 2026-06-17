<footer class="site-footer">
    <div class="footer-content">
        <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
        <?php
        wp_nav_menu(array(
            'theme_location' => 'footer',
            'menu_class' => 'footer-menu',
            'container' => false,
            'fallback_cb' => false,
        ));
        ?>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
