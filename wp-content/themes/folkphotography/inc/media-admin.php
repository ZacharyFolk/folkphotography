<?php
/**
 * Media Library admin enhancements for FolkPhotography theme.
 *
 * Adds three things to the WordPress Media Library (upload.php):
 *
 * 1. "Post Status" column — shows which post(s) use an image as featured image,
 *    or "No post" if the image hasn't been used yet. Includes a Hero badge.
 *
 * 2. Filter dropdown — "No post yet / Has post / Hero images" so you can focus
 *    on images that still need to become posts.
 *
 * 3. Bulk action: "Create Draft Posts" — select any number of images, run this
 *    action, and it creates one draft post per image with the image pre-set as
 *    the featured image. Images already attached to a post are skipped.
 *    After running, a notice links directly to your draft posts.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// =============================================================================
// 1. POST STATUS COLUMN
// =============================================================================

/**
 * Register the custom column in Media Library list view.
 */
add_filter( 'manage_media_columns', function ( $columns ) {
    $columns['folk_usage'] = __( 'Post Status', 'folkphotography' );
    return $columns;
} );

/**
 * Render the Post Status column for each attachment row.
 *
 * Shows a Hero badge (if marked), then any posts that use this image
 * as their featured image, or "No post" if none.
 */
add_action( 'manage_media_custom_column', function ( $column_name, $attachment_id ) {
    if ( $column_name !== 'folk_usage' ) {
        return;
    }

    $parts = [];

    // Hero badge
    if ( get_post_meta( $attachment_id, '_folk_hero', true ) === '1' ) {
        $parts[] = '<span class="folk-badge folk-badge--hero">Hero</span>';
    }

    // Posts that use this image as their featured image (up to 3 distinct posts)
    global $wpdb;
    $post_ids = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT DISTINCT post_id FROM {$wpdb->postmeta}
             WHERE meta_key = '_thumbnail_id' AND meta_value = %d
             LIMIT 3",
            $attachment_id
        )
    );

    if ( $post_ids ) {
        foreach ( $post_ids as $pid ) {
            $post = get_post( (int) $pid );
            if ( ! $post ) {
                continue;
            }
            $status_obj = get_post_status_object( $post->post_status );
            $label      = $status_obj ? $status_obj->label : $post->post_status;
            $edit_link  = get_edit_post_link( $pid );
            $title      = esc_html( $post->post_title ?: __( '(no title)', 'folkphotography' ) );
            $label_html = ' <em>(' . esc_html( $label ) . ')</em>';

            // get_edit_post_link() returns null when the post type has no edit
            // screen or permissions are missing — guard before passing to esc_url().
            if ( $edit_link ) {
                $parts[] = '<a href="' . esc_url( $edit_link ) . '" class="folk-post-link">' . $title . $label_html . '</a>';
            } else {
                $parts[] = '<span class="folk-post-link">' . $title . $label_html . '</span>';
            }
        }
        if ( count( $post_ids ) >= 3 ) {
            $parts[] = '<span class="folk-overflow">…and more</span>';
        }
    } else {
        $parts[] = '<span class="folk-no-post">' . __( 'No post', 'folkphotography' ) . '</span>';
    }

    echo implode( '', $parts );
}, 10, 2 );

/**
 * Styles scoped to the Media Library screen only.
 */
add_action( 'admin_head-upload.php', function () {
    ?>
    <style>
        .column-folk_usage      { width: 180px; }
        .folk-badge             { display: inline-block; font-size: 10px; font-weight: 600;
                                  padding: 2px 7px; border-radius: 3px; margin-bottom: 5px;
                                  text-transform: uppercase; letter-spacing: .05em; }
        .folk-badge--hero       { background: #2271b1; color: #fff; }
        .folk-post-link         { display: block; font-size: 12px; line-height: 1.5; color: #2271b1; }
        .folk-post-link em      { color: #999; font-style: normal; }
        .folk-no-post           { color: #999; font-size: 12px; }
        .folk-overflow          { display: block; color: #999; font-size: 11px; }
    </style>
    <?php
} );

// =============================================================================
// 2. FILTER DROPDOWN
// =============================================================================

/**
 * Add a "Post Status" filter dropdown to the Media Library toolbar.
 */
add_action( 'restrict_manage_posts', function ( $post_type ) {
    if ( $post_type !== 'attachment' ) {
        return;
    }
    $current = isset( $_GET['folk_usage_filter'] ) ? $_GET['folk_usage_filter'] : '';
    ?>
    <select name="folk_usage_filter">
        <option value=""><?php esc_html_e( 'All images', 'folkphotography' ); ?></option>
        <option value="no_post"  <?php selected( $current, 'no_post' ); ?>><?php esc_html_e( 'No post yet', 'folkphotography' ); ?></option>
        <option value="has_post" <?php selected( $current, 'has_post' ); ?>><?php esc_html_e( 'Has post', 'folkphotography' ); ?></option>
        <option value="hero"     <?php selected( $current, 'hero' ); ?>><?php esc_html_e( 'Hero images', 'folkphotography' ); ?></option>
    </select>
    <?php
} );

/**
 * Apply the filter to the Media Library query.
 *
 * "has_post" / "no_post" are determined by whether the attachment ID appears
 * as a _thumbnail_id value on any post — i.e. is it someone's featured image.
 */
add_action( 'pre_get_posts', function ( $query ) {
    global $pagenow;
    if ( ! is_admin() || $pagenow !== 'upload.php' || ! $query->is_main_query() ) {
        return;
    }

    $filter = isset( $_GET['folk_usage_filter'] ) ? $_GET['folk_usage_filter'] : '';
    if ( ! $filter ) {
        return;
    }

    if ( $filter === 'hero' ) {
        $query->set( 'meta_query', [ [
            'key'     => '_folk_hero',
            'value'   => '1',
            'compare' => '=',
        ] ] );
        return;
    }

    // Get all attachment IDs currently used as featured images
    global $wpdb;
    $used_ids = $wpdb->get_col(
        "SELECT DISTINCT CAST(meta_value AS UNSIGNED)
         FROM {$wpdb->postmeta}
         WHERE meta_key = '_thumbnail_id' AND meta_value != ''"
    );
    $used_ids = array_filter( array_map( 'intval', $used_ids ) );

    if ( $filter === 'has_post' ) {
        $query->set( 'post__in', ! empty( $used_ids ) ? $used_ids : [ 0 ] );
    } elseif ( $filter === 'no_post' ) {
        if ( ! empty( $used_ids ) ) {
            $query->set( 'post__not_in', $used_ids );
        }
    }
} );

// =============================================================================
// 3. BULK ACTION: CREATE DRAFT POSTS
// =============================================================================

/**
 * Register the bulk action in the Media Library dropdown.
 */
add_filter( 'bulk_actions-upload', function ( $actions ) {
    $actions['folk_create_posts'] = __( 'Create Draft Posts', 'folkphotography' );
    return $actions;
} );

/**
 * Handle the "Create Draft Posts" bulk action.
 *
 * For each selected image:
 *  - Skips if the image is already a featured image of any post.
 *  - Creates a draft post titled from the attachment title (or filename).
 *  - Sets the image as the draft's featured image.
 *
 * Redirects back to the Media Library with counts in the URL for the notice.
 */
add_filter( 'handle_bulk_actions-upload', function ( $redirect_url, $action, $post_ids ) {
    if ( $action !== 'folk_create_posts' ) {
        return $redirect_url;
    }

    $created = 0;
    $skipped = 0;

    foreach ( $post_ids as $attachment_id ) {
        $attachment_id = (int) $attachment_id;
        $attachment    = get_post( $attachment_id );

        if ( ! $attachment || $attachment->post_type !== 'attachment' ) {
            continue;
        }

        // Skip if already the featured image of an existing post
        global $wpdb;
        $existing = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT post_id FROM {$wpdb->postmeta}
                 WHERE meta_key = '_thumbnail_id' AND meta_value = %d
                 LIMIT 1",
                $attachment_id
            )
        );
        if ( $existing ) {
            $skipped++;
            continue;
        }

        // Build a readable title from attachment title or filename
        $title = trim( $attachment->post_title );
        if ( empty( $title ) ) {
            $filename = get_attached_file( $attachment_id );
            $title    = pathinfo( $filename, PATHINFO_FILENAME );
            $title    = ucwords( str_replace( [ '-', '_' ], ' ', $title ) );
        }

        $post_id = wp_insert_post( [
            'post_title'  => sanitize_text_field( $title ),
            'post_status' => 'draft',
            'post_type'   => 'post',
            'post_author' => get_current_user_id(),
        ] );

        if ( $post_id && ! is_wp_error( $post_id ) ) {
            set_post_thumbnail( $post_id, $attachment_id );
            $created++;
        }
    }

    return add_query_arg(
        [ 'folk_created' => $created, 'folk_skipped' => $skipped ],
        $redirect_url
    );
}, 10, 3 );

/**
 * Show a result notice after "Create Draft Posts" runs.
 */
add_action( 'admin_notices', function () {
    if ( ! isset( $_GET['folk_created'] ) ) {
        return;
    }

    $created = (int) $_GET['folk_created'];
    $skipped = (int) ( isset( $_GET['folk_skipped'] ) ? $_GET['folk_skipped'] : 0 );

    if ( $created > 0 ) {
        $msg = sprintf(
            _n( '%d draft post created.', '%d draft posts created.', $created, 'folkphotography' ),
            $created
        );
        $msg .= ' <a href="' . esc_url( admin_url( 'edit.php?post_status=draft' ) ) . '">'
              . __( 'Edit Drafts &rarr;', 'folkphotography' ) . '</a>';
    } else {
        $msg = __( 'No posts created.', 'folkphotography' );
    }

    if ( $skipped > 0 ) {
        $msg .= ' ' . sprintf(
            _n(
                '%d image skipped — already has a post.',
                '%d images skipped — already have posts.',
                $skipped,
                'folkphotography'
            ),
            $skipped
        );
    }

    echo '<div class="notice notice-success is-dismissible"><p>' . $msg . '</p></div>';
} );

// =============================================================================
// 4. FEATURED IMAGE COLUMN IN ADMIN POSTS / PORTFOLIO LISTS
// =============================================================================

/**
 * Insert a thumbnail column immediately before the Title column on the Posts
 * and Portfolio admin list screens.
 *
 * Uses the 'folk-admin-thumb' image size (60×60 hard-crop, registered in
 * functions.php). The column header is intentionally left blank — the tiny
 * square thumbnails are self-explanatory and a label wastes space.
 */
function folkphotography_add_thumb_column( $columns ) {
    $reordered = [];
    foreach ( $columns as $key => $label ) {
        if ( $key === 'title' ) {
            $reordered['folk_thumb'] = ''; // blank header — no label needed
        }
        $reordered[ $key ] = $label;
    }
    return $reordered;
}
add_filter( 'manage_posts_columns',     'folkphotography_add_thumb_column' );
add_filter( 'manage_portfolio_posts_columns', 'folkphotography_add_thumb_column' );

/**
 * Render the thumbnail for each row.
 *
 * Wraps the image in an edit link so clicking the thumbnail opens the post editor.
 */
function folkphotography_thumb_column_content( $column, $post_id ) {
    if ( $column !== 'folk_thumb' ) {
        return;
    }
    if ( has_post_thumbnail( $post_id ) ) {
        printf(
            '<a href="%s">%s</a>',
            esc_url( get_edit_post_link( $post_id ) ),
            get_the_post_thumbnail( $post_id, 'folk-admin-thumb' )
        );
    } else {
        // Placeholder so the column height stays consistent
        echo '<span style="display:block;width:60px;height:60px;background:#2c2c2c;border-radius:3px;"></span>';
    }
}
add_action( 'manage_posts_custom_column',          'folkphotography_thumb_column_content', 10, 2 );
add_action( 'manage_portfolio_posts_custom_column', 'folkphotography_thumb_column_content', 10, 2 );

/**
 * Styles for the thumbnail column — scoped to edit.php only.
 */
add_action( 'admin_head-edit.php', function () {
    ?>
    <style>
        .column-folk_thumb            { width: 66px; padding: 8px 4px !important; }
        .column-folk_thumb img        { display: block; width: 60px; height: 60px;
                                        object-fit: cover; border-radius: 3px; }
        .column-folk_thumb a:hover img { opacity: .8; }
    </style>
    <?php
} );
