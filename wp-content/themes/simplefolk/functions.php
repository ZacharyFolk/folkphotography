<?php

////////////////////////////////////////
//                                    //
//    Load them scripts and styles    //
//                                    //
////////////////////////////////////////

define('SIMPLE_THEME_VERSION', '0.6.1');

function main_scripts()
{
  wp_enqueue_style('main',  get_theme_file_uri() . '/style.css', array(), SIMPLE_THEME_VERSION, 'all');
  wp_enqueue_style('glightbox',  get_theme_file_uri() . '/assets/glightbox/css/glightbox.3.2.0.min.css', array(), SIMPLE_THEME_VERSION, 'all');
  wp_enqueue_script('embla', get_template_directory_uri() . '/js/embla/embla-carousel.js', array(), false, true);
  wp_enqueue_script('embla-autoplay', get_template_directory_uri() . '/js/embla/embla-autoplay.js', array('embla'), false, true);
  wp_enqueue_script('glightbox-script', get_template_directory_uri() . '/assets/glightbox/js/glightbox.3.2.0.min.js', array(), false, true);
  wp_enqueue_script('theme-scripts', get_template_directory_uri() . '/js/theme-scripts.js', array(), false, true);
  wp_enqueue_script('comment-ajax', get_template_directory_uri() . '/js/comment-ajax.js', array(), '1.0', true);
}

add_action('wp_enqueue_scripts', 'main_scripts');

// add_action('wp_enqueue_scripts', 'enqueue_woocommerce_scripts');
// function enqueue_woocommerce_scripts()
// {
//   if (class_exists('woocommerce')) {
//     wp_enqueue_script('wc-add-to-cart-variation');
//   }
// }


// Add stylesheet for admin

function custom_admin_styles()
{
  wp_enqueue_style('custom-admin-style', get_template_directory_uri() . '/admin-style.css');
}
add_action('admin_enqueue_scripts', 'custom_admin_styles');


function load_google_fonts()
{
  wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css?family=Poppins:400,600&display=swap&family=Lato:400,600&display=swap');
}
add_action('wp_enqueue_scripts', 'load_google_fonts');


include('customizer.php');

//////////////////////////////////
//                              //
//    Disable Gutenberg  CSS    //
//                              //
//////////////////////////////////

function remove_gutenberg_block_library_css()
{
  $disable = esc_attr(get_theme_mod('gutenberg_blocks'));
  if ($disable) {
    wp_dequeue_style('wp-block-library');
  }
}

add_action('wp_enqueue_scripts', 'remove_gutenberg_block_library_css', 100);


//////////////////////////////////////////
//                                      //
//    Remove some scripts from head     //
//                                      //
//////////////////////////////////////////

remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('wp_head', 'wp_resource_hints', 2);

////////////////////////////
//                        //
//    Create main menu    //
//                        //
////////////////////////////


function create_main_nav()
{
  register_nav_menu('top-nav', __('Top Nav', 'simplefolk'));
}
add_action('init', 'create_main_nav');


$menu_name   = 'Main Menu';
$menu_exists = wp_get_nav_menu_object($menu_name);
$menu_location = 'top-nav';

if (!$menu_exists) {
  $menu_id = wp_create_nav_menu($menu_name);

  wp_update_nav_menu_item($menu_id, 0, array(
    'menu-item-title'  =>  __('About', 'simplefolk'),
    'menu-item-url'    => home_url('/about/'),
    'menu-item-status' => 'publish'
  ));
  wp_update_nav_menu_item($menu_id, 0, array(
    'menu-item-title'  =>  __('Blog', 'simplefolk'),
    'menu-item-url'    => home_url('/blog/'),
    'menu-item-status' => 'publish'
  ));

  wp_update_nav_menu_item($menu_id, 0, array(
    'menu-item-title'  =>  __('Collections', 'simplefolk'),
    'menu-item-url'    => home_url('/collections/'),
    'menu-item-status' => 'publish'
  ));
  wp_update_nav_menu_item($menu_id, 0, array(
    'menu-item-title'  =>  __('Hashtags', 'simplefolk'),
    'menu-item-url'    => home_url('/hashtags/'),
    'menu-item-status' => 'publish'
  ));

  if (!has_nav_menu($menu_location)) {

    $locations = get_theme_mod('nav_menu_locations');
    $locations[$menu_location] = $menu_id;
    set_theme_mod('nav_menu_locations', $locations);
  }
}


///////////////////////////////////////////
//                                       //
//    Custom taxonomy for attachments    //
//                                       //
///////////////////////////////////////////


function simple_register_attachments_tax()
{
  register_taxonomy(
    'collections',
    'attachment',
    array(
      'labels' =>  array(
        'name'              => 'Collections',
        'singular_name'     => 'Project',
        'search_items'      => 'Search Collections',
        'all_items'         => 'All Collections',
        'edit_item'         => 'Edit Collections',
        'update_item'       => 'Update Project',
        'add_new_item'      => 'Add New Project',
        'new_item_name'     => 'New Project Name',
        'menu_name'         => 'Collections',
      ),
      'public' => true,
      'hierarchical' => true,
      'sort' => true,
      'show_admin_column' => true,
      'rewrite' => array('slug' => 'collections', 'with_front' => false)
    )
  );

  wp_insert_term(
    'Exclude Images',
    'collections',
    array(
      'description'  => 'This is used to exlude media from featured posts and archives.',
      'slug'     => 'exclude'
    )
  );

  register_taxonomy(
    'hashtags',
    'attachment',
    array(
      'labels' =>  array(
        'name'              => 'Hashtags',
        'singular_name'     => 'Hashtag',
        'search_items'      => 'Search Hashtags',
        'all_items'         => 'All Hashtags',
        'edit_item'         => 'Edit Hashtags',
        'update_item'       => 'Update Hashtag',
        'add_new_item'      => 'Add New Hashtag',
        'new_item_name'     => 'New Hashtag Name',

      ),
      'public' => true,
      'hierarchical' => false,
      'sort' => true,
      'show_admin_column' => true,
      'rewrite' => array('slug' => 'hashtags', 'with_front' => false)
    )
  );
}
add_action('init', 'simple_register_attachments_tax', 0);


///////////////////////////////////////////////
//                                           //
//    Create Root Pages for new taxonomy     //
//                                           //
///////////////////////////////////////////////

add_action('admin_init', 'add_collections_page');
function add_collections_page()
{
  if (!get_option('collections_installed')) {
    $new_page_id = wp_insert_post(array(
      'post_title'     => 'Collections',
      'post_type'      => 'page',
      'post_name'      => 'collections',
      'comment_status' => 'closed',
      'ping_status'    => 'closed',
      'post_content'   => '',
      'post_status'    => 'publish',
      'post_author'    => get_user_by('id', 1)->user_id,
      'menu_order'     => 0,
      'page_template'  => 'page-templates/collections-template.php'
    ));

    update_option('collections_installed', true);
  }
}


add_action('admin_init', 'add_hashtags_page');
function add_hashtags_page()
{
  if (!get_option('hashtags_installed')) {
    $new_page_id = wp_insert_post(array(
      'post_title'     => 'Hashtags',
      'post_type'      => 'page',
      'post_name'      => 'hashtags',
      'comment_status' => 'closed',
      'ping_status'    => 'closed',
      'post_content'   => '',
      'post_status'    => 'publish',
      'post_author'    => get_user_by('id', 1)->user_id,
      'menu_order'     => 0,
      'page_template'  => 'page-templates/hashtags-template.php'
    ));

    update_option('hashtags_installed', true);
  }
}

add_action('admin_init', 'add_blog_page');
function add_blog_page()
{
  if (!get_option('blog_page_installed')) {
    $new_page_id = wp_insert_post(array(
      'post_title'     => 'Blog',
      'post_type'      => 'page',
      'post_name'      => 'blog',
      'comment_status' => 'closed',
      'ping_status'    => 'closed',
      'post_content'   => '',
      'post_status'    => 'publish',
      'post_author'    => get_user_by('id', 1)->user_id,
      'menu_order'     => 0,
      'page_template'  => 'page-templates/blog-template.php'
    ));

    update_option('blog_page_installed', true);
  }
}
////////////////////////////
//                        //
//    Register widgets    //
//                        //
////////////////////////////
/**
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function simple_widgets_init()
{
  register_sidebar(
    array(
      'name'          => __('Blog Sidebar', 'simplefolk'),
      'id'            => 'sidebar-1',
      'description'   => __('Add widgets here to appear in your sidebar on single posts.', 'simplefolk'),
      'before_widget' => '<section id="%1$s" class="widget %2$s">',
      'after_widget'  => '</section>',
      'before_title'  => '<h2 class="widget-title">',
      'after_title'   => '</h2>',
    )
  );

  register_sidebar(
    array(
      'name'          => __('Home Full Top', 'simplefolk'),
      'id'            => 'home-full-top',
      'description'   => __('Widget for full width column on top of home page.', 'simplefolk'),
      'before_widget' => '<section id="%1$s" class="widget %2$s">',
      'after_widget'  => '</section>',
      'before_title'  => '<h2 class="widget-title">',
      'after_title'   => '</h2>',
    )
  );

  register_sidebar(
    array(
      'name'          => __('Home Column 1', 'simplefolk'),
      'id'            => 'home-1',
      'description'   => __('Widget for column on home page.', 'simplefolk'),
      'before_widget' => '<section id="%1$s" class="widget %2$s">',
      'after_widget'  => '</section>',
      'before_title'  => '<h2 class="widget-title">',
      'after_title'   => '</h2>',
    )
  );

  register_sidebar(
    array(
      'name'          => __('Home Column 2', 'simplefolk'),
      'id'            => 'home-2',
      'description'   => __('Widget for additional column on home page.', 'simplefolk'),
      'before_widget' => '<section id="%1$s" class="widget %2$s">',
      'after_widget'  => '</section>',
      'before_title'  => '<h2 class="widget-title">',
      'after_title'   => '</h2>',
    )
  );

  register_sidebar(
    array(
      'name'          => __('Footer 1', 'simplefolk'),
      'id'            => 'footer-1',
      'description'   => __('Add widgets here to appear in your footer column one.', 'simplefolk'),
      'before_widget' => '<section id="%1$s" class="widget %2$s">',
      'after_widget'  => '</section>',
      'before_title'  => '<h2 class="widget-title">',
      'after_title'   => '</h2>',
    )
  );

  register_sidebar(
    array(
      'name'          => __('Footer 2', 'simplefolk'),
      'id'            => 'footer-2',
      'description'   => __('Add widgets here to appear in your footer column two.', 'simplefolk'),
      'before_widget' => '<section id="%1$s" class="widget %2$s">',
      'after_widget'  => '</section>',
      'before_title'  => '<h2 class="widget-title">',
      'after_title'   => '</h2>',
    )
  );

  register_sidebar(
    array(
      'name'          => __('About Sidebar', 'simplefolk'),
      'id'            => 'about-1',
      'description'   => __('Add widgets here to appear in your sidebar on About page.', 'simplefolk'),
      'before_widget' => '<section id="%1$s" class="widget %2$s">',
      'after_widget'  => '</section>',
      'before_title'  => '<h2 class="widget-title">',
      'after_title'   => '</h2>',
    )
  );
}
add_action('widgets_init', 'simple_widgets_init');


///////////////////////////////////
//                               //
//    Light/Dark Mode toggle     //
//                               //
///////////////////////////////////
/**
 * @return string HTML for the sun/moon toggle 
 */
function createLightSwitch($item)
{
  $lightSwitch = <<<END
  <li id="event-toggle">
  <input id="mode-toggle" type="checkbox">
  <label class="mode-button-container" for="mode-toggle">
      <span class="mode-button">
          <svg class="lightsoff" version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
              viewBox="0 0 32 32">
              <title>sun</title>
              <path
                  d="M16 26c1.105 0 2 0.895 2 2v2c0 1.105-0.895 2-2 2s-2-0.895-2-2v-2c0-1.105 0.895-2 2-2zM16 6c-1.105 0-2-0.895-2-2v-2c0-1.105 0.895-2 2-2s2 0.895 2 2v2c0 1.105-0.895 2-2 2zM30 14c1.105 0 2 0.895 2 2s-0.895 2-2 2h-2c-1.105 0-2-0.895-2-2s0.895-2 2-2h2zM6 16c0 1.105-0.895 2-2 2h-2c-1.105 0-2-0.895-2-2s0.895-2 2-2h2c1.105 0 2 0.895 2 2zM25.899 23.071l1.414 1.414c0.781 0.781 0.781 2.047 0 2.828s-2.047 0.781-2.828 0l-1.414-1.414c-0.781-0.781-0.781-2.047 0-2.828s2.047-0.781 2.828 0zM6.101 8.929l-1.414-1.414c-0.781-0.781-0.781-2.047 0-2.828s2.047-0.781 2.828 0l1.414 1.414c0.781 0.781 0.781 2.047 0 2.828s-2.047 0.781-2.828 0zM25.899 8.929c-0.781 0.781-2.047 0.781-2.828 0s-0.781-2.047 0-2.828l1.414-1.414c0.781-0.781 2.047-0.781 2.828 0s0.781 2.047 0 2.828l-1.414 1.414zM6.101 23.071c0.781-0.781 2.047-0.781 2.828 0s0.781 2.047 0 2.828l-1.414 1.414c-0.781 0.781-2.047 0.781-2.828 0s-0.781-2.047 0-2.828l1.414-1.414z">
              </path>
              <path
                  d="M16 8c-4.418 0-8 3.582-8 8s3.582 8 8 8c4.418 0 8-3.582 8-8s-3.582-8-8-8zM16 21c-2.761 0-5-2.239-5-5s2.239-5 5-5 5 2.239 5 5-2.239 5-5 5z">
              </path>
          </svg>
          <svg class="lightson" version="1.1" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
              viewBox="0 0 24 24">
              <title>crescent_moon</title>
              <path
                  d="M9.984 2.016q4.172 0 7.102 2.93t2.93 7.055-2.93 7.055-7.102 2.93q-2.719 0-4.969-1.313 2.297-1.313 3.633-3.633t1.336-5.039-1.336-5.039-3.633-3.633q2.25-1.313 4.969-1.313z">
              </path>
          </svg>
      </span>
  </label>
  </li>
  END;
  return $item . $lightSwitch;
}

add_filter('wp_nav_menu_items', 'createLightSwitch');


////////////////////////////////////////
//                                    //
//    Featured collection widget        //
//                                    //
////////////////////////////////////////

/**
 * 
 * Creates a widget that allows to select from category dropdown
 * Selected category will generate an archive card that includes
 * Category thumbnail, title, description and link to archive collection
 */
class project_thumbs_widget extends WP_Widget
{

  function __construct()
  {
    parent::__construct(

      'project_thumbs_widget',
      __('Featured Collection', 'simplefolk'),
      array('description' => __('Get random collection of images from specific collection', 'simplefolk'),)
    );
  }

  public function widget($args, $instance)
  {
    $title = apply_filters('widget_title', $instance['title']);
    $cat = apply_filters('widget_text', $instance['cat']);

    echo $args['before_widget'];
    if (!empty($title)) {
      echo $args['before_title'] . $title . $args['after_title'];
    }
    featured_cat_card($cat);
    echo $args['after_widget'];
  }

  public function form($instance)
  {
    if (isset($instance['title'])) {
      $title = $instance['title'];
    } else {
      $title = __('New title', 'simplefolk');
    }
    if (isset($instance['cat'])) {
      $cat = $instance['cat'];
    } else {
      $cat = 1; // 1 should be Uncategorized as default
    }

?>
    <?php if ($cat) {
      featured_cat_card($cat);
    }
    ?>
    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>"><?php __('Title:', 'simplefolk'); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('cat'); ?>">
        Select featured collection:
        <select class="widefat" id="<?php echo $this->get_field_id('cat'); ?>" name="<?php echo $this->get_field_name('cat'); ?>" />
        <?php
        echo '<option>' . __('No Category', 'simplefolk') . '</option>';
        $args = array('show_option_none' => 'No Category', 'hide_empty' => 0);
        $tax = 'collections';
        $categories =  get_terms(
          array(
            'taxonomy' => $tax,
            'hide_empty' => false,
          )
        );
        foreach ($categories as $category) :
          $selected = ($cat ==  $category->term_id) ? 'selected' : '';
          echo '<option value="' . $category->term_id . '" ' . $selected . '>' . $category->name . '</option>';
        endforeach; ?>
        </select>
      </label>
    </p>
  <?php
  }
  public function update($new_instance, $old_instance)
  {
    $instance = array();
    $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
    $instance['cat'] = (!empty($new_instance['cat'])) ? strip_tags($new_instance['cat']) : '';
    return $instance;
  }
}

function load_cat_thumb_widget()
{
  register_widget('project_thumbs_widget');
}
add_action('widgets_init', 'load_cat_thumb_widget');

/**
 * Returns card elements from a category id
 * HTML of category name, description, and attachment
 * @param string $catID Category id to build elements
 * @param string $tax Taxonmy type to generate, default = collections
 * @return string HTML of image with category title and description
 * 
 * */
function featured_cat_card($catID, $tax = 'collections')
{
  $name = strtolower(get_term($catID)->name);
  echo '<a title="View all photos from the ' . $name . ' collection" href="' . get_category_link($catID) . '">';
  echo '<div class="featured-cat-banner">';
  echo get_attachment_by_cat_id($catID, 'landscape_thumb', $tax);
  echo '<header><h3>' . $name . '</h3></header>';
  echo '</div></a>';
  // echo category_description($catID);
  // echo '<div class="category-link" style="text-align: right"><a title="View all photos from the ' . $name . ' collection" href="' . get_category_link($catID) . '">View collection &raquo; </a>';
}


////////////////////////////////////////
//                                    //
//    Featured Tag widget        //
//                                    //
////////////////////////////////////////
/**
 * 
 * Creates a widget that allows to select from dropdown of all tags
 * Selected tag will generate a small gallery of thumbs from that tag
 * Tag thumbnail, title, and link to archive collection
 */
class tag_thumbs_widget extends WP_Widget
{

  function __construct()
  {
    parent::__construct(

      'tag_thumbs_widget',
      __('Tag thumbs', 'simplefolk'),
      array('description' => __('Get random collection of images from specific hashtag', 'simplefolk'),)
    );
  }

  public function widget($args, $instance)
  {
    $title = apply_filters('widget_title', $instance['title']);
    $tag = apply_filters('widget_text', $instance['tag']);

    echo $args['before_widget'];
    if (!empty($title)) {
      echo $args['before_title'] . $title . $args['after_title'];
    }
    featured_tag_card($tag);
    echo $args['after_widget'];
  }

  public function form($instance)
  {
    if (isset($instance['title'])) {
      $title = $instance['title'];
    } else {
      $title = __('New title', 'simplefolk');
    }
    if (isset($instance['tag'])) {
      $tag = $instance['tag'];
    } else {
      $tag = '';
    }

  ?>
    <?php if ($tag) {
      featured_tag_card($tag);
    }
    ?>
    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>"><?php __('Title:', 'simplefolk'); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('tag'); ?>">
        Select featured tag:
        <select class="widefat" id="<?php echo $this->get_field_id('tag'); ?>" name="<?php echo $this->get_field_name('tag'); ?>" />
        <?php
        echo '<option>' . __('No Category', 'simplefolk') . '</option>';
        $args = array('show_option_none' => 'No Category', 'hide_empty' => 0);

        $tags =  get_terms(
          array(
            'taxonomy' => 'hashtags',
            'hide_empty' => false,
          )
        );


        // wut($tags);
        foreach ($tags as $t) :
          $selected = ($tag ==  $t->term_id) ? 'selected' : '';
          echo '<option value="' . $t->term_id . '" ' . $selected . '>' . $t->name . '</option>';
        endforeach; ?>
        </select>
      </label>
    </p>
  <?php
  }
  public function update($new_instance, $old_instance)
  {
    $instance = array();
    $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
    $instance['tag'] = (!empty($new_instance['tag'])) ? strip_tags($new_instance['tag']) : '';
    return $instance;
  }
}

function load_tag_thumb_widget()
{
  register_widget('tag_thumbs_widget');
}
add_action('widgets_init', 'load_tag_thumb_widget');

/**
 * Returns card elements from a category id
 * HTML of category name, description, and attachment
 * @param string $catID Category id to build elements
 * @return string HTML of image with category title and description
 * 
 * */
function featured_tag_card($tag)
{

  $name = get_term($tag);
  getThumbGallery('hashtags', $name);
}


////////////////////////////////////////
//                                    //
//    Carousel widget                 //
//                                    //
////////////////////////////////////////

/**
 * 
 * Creates a carousel from all of the collections
 */
class collections_carousel_widget extends WP_Widget
{

  function __construct()
  {
    parent::__construct(
      'collections_carousel_widget',
      __('Collection Carousel', 'simplefolk'),
      array('description' => __('Display a carousel built from the collections', 'simplefolk'),)
    );
  }

  public function widget($args, $instance)
  {
    $title = apply_filters('widget_title', $instance['title']);

    echo $args['before_widget'];
    if (!empty($title)) {
      echo $args['before_title'] . $title . $args['after_title'];
    }

    // Get all published collections
    $collections = get_terms(array(
      'taxonomy' => 'collections',
      'hide_empty' => false,
    ));

    echo '<div id="collection_carousel" class="embla">
    <div class="embla__viewport">
    <div class="embla__container">';
    foreach ($collections as $collection) {
      // Get attachments for the current collection ID
      $attachments = get_posts(array(
        'post_type' => 'attachment',
        'posts_per_page' => -1,
        'orderby' => 'rand',
        'tax_query' => array(
          array(
            'taxonomy' => 'collections',
            'field' => 'id',
            'terms' => $collection->term_id,
          ),
        ),
        'meta_query' => array(
          array(
            'key' => 'featured_image',
            'value' => 'on',
          ),
        ),
      ));

      if (!empty($attachments)) {
        $random_attachment = $attachments[array_rand($attachments)]; // Get a random attachment
        $image_url = wp_get_attachment_image_url($random_attachment->ID, 'landscape_carousel');
        $collection_link = get_term_link($collection); // Get the link to the collection page
        echo '<div class="embla__slide"><div class="collection-image-list-item" style="background-image: url(' . esc_url($image_url) . ');">';
        echo '<a class="collection-link" href="' . esc_url($collection_link) . '">' . esc_html($collection->name) . '</a>';
        echo '</div></div>';
      }
    }
    echo '</div>
    </div>
    <div class="embla-buttons">
    <button type="button" class="embla__prev" title="Previous image">  
    <svg class="embla__button__svg" viewBox="0 0 532 532">
    <path
      fill="currentColor"
      d="M355.66 11.354c13.793-13.805 36.208-13.805 50.001 0 13.785 13.804 13.785 36.238 0 50.034L201.22 266l204.442 204.61c13.785 13.805 13.785 36.239 0 50.044-13.793 13.796-36.208 13.796-50.002 0a5994246.277 5994246.277 0 0 0-229.332-229.454 35.065 35.065 0 0 1-10.326-25.126c0-9.2 3.393-18.26 10.326-25.2C172.192 194.973 332.731 34.31 355.66 11.354Z"
    >
    </path>
    </svg>
  </button>
    <button type="button" class="embla__next" title="Next image">
    <svg class="embla__button__svg" viewBox="0 0 532 532">
    <path
      fill="currentColor"
      d="M176.34 520.646c-13.793 13.805-36.208 13.805-50.001 0-13.785-13.804-13.785-36.238 0-50.034L330.78 266 126.34 61.391c-13.785-13.805-13.785-36.239 0-50.044 13.793-13.796 36.208-13.796 50.002 0 22.928 22.947 206.395 206.507 229.332 229.454a35.065 35.065 0 0 1 10.326 25.126c0 9.2-3.393 18.26-10.326 25.2-45.865 45.901-206.404 206.564-229.332 229.52Z"
    ></path>
  </svg>
    </button>
    </div>
    </div>';
  }
  public function form($instance)
  {
    $title = isset($instance['title']) ? esc_attr($instance['title']) : '';

    // Input field for entering the widget title
  ?>
    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
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

function load_carousel_collections_widget()
{
  register_widget('collections_carousel_widget');
}

add_action('widgets_init', 'load_carousel_collections_widget');


////////////////////////////////////////////////////
//                                                //
//    Create new permalink for archive pages      //
//                                                //
////////////////////////////////////////////////////

// add_filter('attachment_link', 'updated_attachment_link', 20, 2);
function updated_attachment_link($link, $attachment_id)
{

  $attachment = get_post($attachment_id);
  // Only for attachments actually attached to a parent post
  if (!empty($attachment->post_parent)) {
    $parent_link = get_permalink($attachment->post_parent);
    // make the link compatible with permalink settings with or without "/" at the end
    $parent_link = rtrim($parent_link, "/");
    $link =  $parent_link . '/gallery/' . $attachment_id;
  }

  return $link;
}


add_action('init', function () {

  // Tell WordPress how to handle the new structure
  // add_rewrite_rule('(.+)/gallery/([0-9]{1,})/?$', 'index.php?attachment_id=$matches[2]', 'top');
});


//////////////////////////////
//                          //
//    Debug highlighter     //
//                          //
//////////////////////////////

function wut($var, $exit = false)
{
  echo '<pre style="font-size:14px;">';

  if (is_array($var) || is_object($var)) {
    echo htmlentities(print_r($var, true));
  } elseif (is_string($var)) {
    echo "string(" . strlen($var) . ") \"" . htmlentities($var) . "\"\n";
  } else {
    var_dump($var);
  }

  echo "\n</pre>";

  if ($exit) {
    exit;
  }
}

/////////////////////////
//                     //
//    Theme support    //
//                     //
/////////////////////////


add_theme_support('custom-logo');
add_theme_support('post-thumbnails');
add_theme_support('align-wide');
add_theme_support('title-tag');
add_theme_support('automatic-feed-links');
$args = array(
  'script',
  'style',
  'search-form',
  'comment-form',
  'comment-list',
  'gallery',
  'caption',
);
add_theme_support('html5', $args);
add_theme_support('responsive-embeds');
add_theme_support('wp-block-styles');

// Add WooCommerce support
function simplefolk_add_woocommerce_support()
{
  add_theme_support('woocommerce');
  //  add_theme_support('wc-product-gallery-zoom');
  add_theme_support('wc-product-gallery-lightbox');
  add_theme_support('wc-product-gallery-slider');
}

add_action('after_setup_theme', 'simplefolk_add_woocommerce_support');

register_nav_menus(array(
  'primary' => __('Main Menu', 'simplefolk'),
  'side-nav-menu' => __('Side Menu', 'simplefolk'),
  'social-link'  => __('Add Social Icons Only', 'simplefolk'),
));

///////////////////////////
//                       //
//    Set image sizes    //
//                       //
///////////////////////////
add_image_size('excerpt_thumb', 480, 283, true);
add_image_size('landscape_carousel', 1600, 650, true);
add_image_size('landscape_thumb', 750, 220, true);
add_image_size('square_hero', 450, 450, true);
add_image_size('tiny_thumb', 80, 80, true);

/* Add new sizes to admin menus */
function new_image_sizes($size_names)
{
  $new_sizes = array(
    'landscape_carousel' => 'Landscape Carousel 1600 x 650',
    'landscape_thumb' => 'Landscape Thumbmail 750x220',
    'square_hero' => 'Square 450',
    'tiny_thumb' => 'Square 80'

  );
  return array_merge($size_names, $new_sizes);
}
add_filter('image_size_names_choose', 'new_image_sizes');

// disable srcset on frontend
function disable_wp_responsive_images()
{
  return 1;
}
add_filter('max_srcset_image_width', 'disable_wp_responsive_images');


//////////////////////////
//                      //
//    Get site info     //
//                      //
//////////////////////////
/**
 * Returns the site title and description
 * TODO : Include option for logo and hook with customizer
 *
 * @return string The html that contains title and description
 */
function get_site_info()
{
  $blog_info    = get_bloginfo('name');
  // todo
  $show_title   = (true === get_theme_mod('display_title_and_tagline', true));
  ?>
  <?php if ($blog_info) : ?>
    <?php if (is_front_page() && !is_paged()) : ?>
      <h1 class="site-title"><?php echo esc_html($blog_info); ?></h1>
    <?php elseif (is_front_page() && !is_home()) : ?>
      <h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html($blog_info); ?></a>
      </h1>
    <?php else : ?>
      <h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html($blog_info); ?></a></h1>
    <?php endif; ?>
    <?php endif;
}

// TODO : Connect with customizer options and add description
// $description  = get_bloginfo( 'description', 'display' );
// if ( $description && true === get_theme_mod( 'display_title_and_tagline', true ) ) :
// echo $description; // phpcs:ignore WordPress.Security.EscapeOutput 


//////////////////////////////////
//                              //
//    Display a random image    //
//                              //
//////////////////////////////////

/**
 * Returns a random image from a selected category
 * @param string $cat Category name to retrieve from, default is 'uncategorized'
 * @return string HTML generated from get_the_post_thumbnail 
 * */


function get_random_image_src($cat = 'uncategorized')
{
  $query = new WP_Query(array(
    'posts_per_page' =>  1,
    'orderby' => 'rand',
    'post_type' => 'post',
    'category_name' => esc_attr($cat),
  ));
  while ($query->have_posts()) : $query->the_post();
    $id = get_the_ID();
    if (has_post_thumbnail($id)) :
      echo get_the_post_thumbnail($id, 'post-thumbnail');
    endif;
  endwhile;
}


function get_random_img_src_by_tag($tag = '')
{
  if ($tag) {
    $args = array(
      'post_type' => 'post',
      'posts_per_page' => 1,
      'orderby' => 'rand',
      'tax_query' => array(
        array(
          'taxonomy' => 'post_tag',
          'field'    => 'name',
          'terms'    => $tag,
        ),
      ),
    );
    $query = new WP_Query($args);
    while ($query->have_posts()) : $query->the_post();
      $id = get_the_ID();
      if (has_post_thumbnail($id)) :
        echo get_img_with_sizes('medium_large');
      endif;
    endwhile;
  }
}


function get_random_atta_img_src_by_tag($tag = '')
{
  if ($tag) {

    $args = array(
      'post_type' => 'attachment',
      'post_status' => 'inherit',
      'posts_per_page' => 1,
      'orderby' => 'rand',
      'tax_query' => array(
        array(
          'taxonomy' => 'post_tag',
          'field'    => 'name',
          'terms'    => $tag,
        ),
      ),
    );

    $query = new WP_Query($args);
    if ($query->have_posts()) :
      while ($query->have_posts()) : $query->the_post();
        $id = get_the_ID();
        $atta_img = wp_get_attachment_image($id, 'medium_large');
        if ($atta_img) {
          echo $atta_img;
        }
      endwhile;
    endif;
  }
}



function get_random_atta_img_src_by_term($tax, $term = '')
{
  // Note :  Found passing slug breaks this because the spaces are hyphenated
  // Passing title for $term seems to work ok

  if ($term) {
    $args = array(
      'post_type' => 'attachment',
      'post_status' => 'inherit',
      'posts_per_page' => 1,
      'orderby' => 'rand',
      'tax_query' => array(
        array(
          'taxonomy' => $tax,
          'field'    => 'name',
          'terms'    => $term,
        ),
      ),
    );

    $query = new WP_Query($args);
    if ($query->have_posts()) :
      while ($query->have_posts()) : $query->the_post();
        $id = get_the_ID();
        $atta_img = wp_get_attachment_image($id, 'medium_large');
        if ($atta_img) {
          echo $atta_img;
        }
      endwhile;
    endif;
  }
}


/**
 * Returns the display for each tag on the root tags/ page 
 * @param string $tax Taxonomy to use for the query eg: post_tag
 * @param object $single_tag WP_Term Object
 * @return string HTML generated from get_the_post_thumbnail - the element is sized depending on the number of images for that term
 * */
function get_tag_display($tax, $single_tag)
{
  // Note :  Found passing slug breaks this because the spaces are hyphenated
  //   $single_tag->name for $term seems to work ok


  $term = $single_tag->name;
  if ($term) {
    $args = array(
      'post_type' => 'attachment',
      'post_status' => 'inherit',
      'posts_per_page' => -1,
      'orderby' => 'rand',
      'tax_query' => array(
        array(
          'taxonomy' => $tax,
          'field'    => 'name',
          'terms'    => $term,
        ),
      ),
    );

    $name = '#' . strtolower($term);
    $tag_banner_array = array('<span>' . $name . '</span>');

    $query = new WP_Query($args);
    if ($query->have_posts()) :
      while ($query->have_posts()) : $query->the_post();
        $id = get_the_ID();
        $atta_img = wp_get_attachment_image($id, 'tiny_thumb');
        if ($atta_img) {
          array_push($tag_banner_array, $atta_img);
          // echo $atta_img;
        }
      endwhile;
    endif;

    shuffle($tag_banner_array);
    foreach ($tag_banner_array as $output) {
      echo $output;
    }
  }
}

/**
 * Returns image for open graph in header
 */

function get_graph_image($id)
{
  return is_attachment() ? wp_get_attachment_image_url($id, 'full') : get_the_post_thumbnail_url('full');
}
/** 
 * Returns thumb gallery for particular term
 */


function getThumbGallery($tax, $term)
{

  if ($tax && $term) :
    $name = $term->name;
    $args = array(
      'post_type' => 'attachment',
      'post_status' => 'inherit',
      'posts_per_page' => -1,
      'orderby' => 'rand',
      'tax_query' => array(
        array(
          'taxonomy' => $tax,
          'field'    => 'name',
          'terms'    => $name,
        ),
      ),
    );

    $query = new WP_Query($args);
    if ($query->have_posts()) :
      echo '<div id="tag_thumbs">';

      while ($query->have_posts()) : $query->the_post();
        $id = get_the_ID();
        get_lightbox_image($id, 'tiny_thumb');

      endwhile;
      echo '</div>';
    endif;
  endif;
}

/**
 * Returns a random image from attachments with that category id
 * @param int $id (required) - Category id (term_id)  
 * @param string $size (optional) - Default value of 'thumbnail'
 * @param string $tax (optional) - Default 'category'
 * @return string HTML of random image src 
 * 
 * */
function get_attachment_by_cat_id($id, $size = 'thumbnail', $tax = 'category')
{
  $args = array(
    'post_type' => 'attachment',
    'post_status' => 'inherit',
    'posts_per_page' => 10,
    'orderby' => 'rand',
    'tax_query' => array(
      array(
        'taxonomy' => $tax,
        'field'    => 'term_id',
        'terms'    => $id,
      ),
    ),
  );

  $query = new WP_Query($args);
  if ($query->have_posts()) :
    while ($query->have_posts()) : $query->the_post();
      $id = get_the_ID();
      $atta_img = wp_get_attachment_image($id, $size);
      if ($atta_img) {
        return $atta_img;
      }
    endwhile;
  endif;
}

/**
 * 
 */
function get_hashtags($id)
{
  $prefix = '#';
  $separator = ' ';
  $output = '';
  $string = '';
  $hashtags =  wp_get_post_terms(
    $id,
    'hashtags',
    array(

      'hide_empty' => false,
    )
  );

  if (!empty($hashtags)) :
    foreach ($hashtags as $hashtag) :
      $output .= '<a title="View all photos with the tag #' . strtolower($hashtag->name) . '"
        href="' . esc_attr(get_tag_link($hashtag->term_id)) . '">' . $prefix . __($hashtag->name) . '</a>' . $separator;
    endforeach;
    $string .= trim($output, $separator);
  endif;
  echo $string;
}
//////////////////////////////////
//                              //
//    Get image and lightbox    //
//                              //
//////////////////////////////////

/**
 * @param int $id Term id to get attachment and meta
 * @return string HTML of image with modal link
 * 
 * Returns an image with lightbox enabled and image meta available in the modal
 */


function get_lightbox_image($id, $size = "medium_large")
{
  // todo: add configs for glightbox options
  $full_image_link = wp_get_attachment_image_url($id, 'full');
  $slide_class = "full-meta-" . $id;
  $atta_img = wp_get_attachment_image($id, $size);
  $image_title = get_the_title($id);

  if ($atta_img) :
    echo '<a href="' . $full_image_link . '" 
  class="glightbox"
  data-desc-position="right"  
  data-glightbox="description: .' . $slide_class . '" title="Open ' . esc_attr($image_title) . ' full size">';
    echo $atta_img;
    echo '</a>';
    echo '<div class="glightbox-desc ' . $slide_class .  '">';
    modal_display_photo_meta($id);
    echo '</div>';
  endif;
}

///////////////////////////////////
//                               //
//    Updates for page title     //
//                               //
///////////////////////////////////
/**
 * Fix for empty title on home page
 * 
 * @param string $title The original title.
 * @return string The title to use.
 */

add_filter('wp_title', 'wp_title_home');
function wp_title_home($title)
{
  if (empty($title) && (is_home() || is_front_page())) {
    $title = get_bloginfo('name') . ' | ' . get_bloginfo('description');
  }

  return $title;
}


////////////////////////////
//                        //
//    Add Facebook SDK    //
//                        //
////////////////////////////
function getFBSDK()
{
  echo <<<END

  END;
}
add_action('wp_head', 'getFBSDK');


// FB Share button 

function get_fb_button()
{
  echo <<<END

END;
}


/////////////////////////////////////////
//                                     //
//    Get loop with excluded terms     //
//                                     //
/////////////////////////////////////////
/**
 * Perform get_terms querty with list of excluded slugs
 * @param array $exclusions An array of slug names to be excluded. Default : empty array //* TODO : Replace this with values from customizer // 
 * @param string $tax Type of taxonomy for query. Default : 'category'
 * @return array of objects from the query
 */
function get_terms_with_exclusions($exclusions = array(), $tax = 'category')
{

  $ids_to_exclude = array();
  $get_terms_to_exclude = get_terms(
    array(
      'fields'    => 'ids',
      'hide_empty' => false,
      'slug'      => $exclusions,
      'taxonomy' => $tax
    )
  );

  if (!is_wp_error($get_terms_to_exclude) && count($get_terms_to_exclude) > 0) {
    $ids_to_exclude = $get_terms_to_exclude;
  }

  $new_query = get_terms(
    array(
      'taxonomy' => $tax,
      'hide_empty' => false,
      'exclude' => $ids_to_exclude
    )
  );

  return $new_query;
}

//////////////////////////////////////
//                                  //
//    Optimize page descriptions    //
//                                  //
//////////////////////////////////////
/**
 * Dynamic meta descriptions
 * 
 * @return string The description of the page based on this fallback:
 * 1) Check if custom meta description ('meta_desc') is defined from the page or post 
 * 2) If no meta_desc then use custom excerpt
 * 3) No custom excerpt then auto excerpt generated WP with 260 char limit
 * 4) No auto excerpt (no text) and it is a single post check for image caption 
 * 5) No meta, no excerpts, no caption, then it just shows default site description
 */
function get_meta_description()
{
  $field = get_post_meta(get_queried_object_id(), 'meta_desc', true);
  if (is_single()) {
    $post_excerpt = the_excerpt();
    $image_id = get_post_thumbnail_id();
    $image_caption =  wp_get_attachment_caption($image_id);
    $desc = (empty($field) ? (empty($post_excerpt) ? (empty($image_caption) ? get_bloginfo('description') : $image_caption) : $post_excerpt) : $field);
  } else {
    $desc = (empty($field) ?  get_bloginfo('description')  : $field);
  }

  return esc_attr(trim($desc));
}

///////////////////////////////
//                           //
//    Update excerpt link    //
//                           //
///////////////////////////////

// function new_excerpt_more($more)
// {
//   global $post;
//   return '… <a title="View the full post: ' . get_the_title($post->ID) . '" href="' . get_permalink($post->ID) . '">' . '<span class="more">Read more &raquo;</span>' . '</a>';
// }
// add_filter('excerpt_more', 'new_excerpt_more');

// //////////////////////////////
//                          //
//    Gallery by tag    //
//                          //
//////////////////////////////

function get_gallery_by_tag($post_tag)
{

  $args = array(
    'tag' => $post_tag
  );

  $tag_query = new WP_Query($args);

  if ($tag_query->have_posts()) :
    echo '<div id="tag_thumbs">';
    while ($tag_query->have_posts()) :
      $tag_query->the_post();
      if (has_post_thumbnail()) :
        $image_id = get_post_thumbnail_id();
        $image_caption =  wp_get_attachment_caption($image_id);
    ?>
        <a data-fancybox="gallery" data-caption="<?php echo $image_caption; ?>" href="<?php the_post_thumbnail_url(); ?>">
          <?php get_img_with_sizes('thumbnail'); ?>
        </a>
    <?php endif;
    endwhile;
    echo '</div>';
  endif;
}

/**
 * Returns collection of thumbs for current collection
 * @param string $id
 * @return string HTML gallery of images that link to their single attachment page
 * 
 */

function this_collections_thumbs($id)
{

  $collection =  get_the_terms($id, 'collections');
  if ($collection) :
    $name = $collection[0]->name;
    $args = array(
      'post_type' => 'attachment',
      'post_status' => 'inherit',
      'posts_per_page' => 9,
      'post__not_in' => array(get_the_ID()),
      'tax_query' => array(
        array(
          'taxonomy' => 'collections',
          'field' => 'slug',
          'terms' => $name,
        )
      ),
    );
    $atta_query = new WP_Query($args);
    if ($atta_query->have_posts()) :
      echo '<h3>More from the ' . strtolower($name) . ' collection:</h3>';
      echo '<div id="cat_thumbs">';
      while ($atta_query->have_posts()) :
        $atta_query->the_post();
        $atta_img = wp_get_attachment_image(get_the_ID(), 'thumbnail');
        $atta_link = get_attachment_link();
        if ($atta_img) {
          echo '<a href="' . $atta_link . '">' . $atta_img . '</a>';
        }
      endwhile;
      echo '</div>';
    endif;
  endif;
}




/**
 * ? Still need this function
 * Remove inline width/height attributes
 */

add_filter('post_thumbnail_html', 'remove_width_attribute', 10);
add_filter('image_send_to_editor', 'remove_width_attribute', 10);

function remove_width_attribute($html)
{
  $html = preg_replace('/(width|height)="\d*"\s/', "", $html);
  return $html;
}


////////////////////////////////////////////////
// Add featured post image in admin post list //
///////////////////////////////////////////////

/**
 * Add featured image column to WP admin panel - posts AND pages
 * See: https://bloggerpilot.com/featured-image-admin/
 */

// Set thumbnail size
add_image_size('j0e_admin-featured-image', 60, 60, false);

// Add the posts and pages columns filter. Same function for both.
add_filter('manage_posts_columns', 'j0e_add_thumbnail_column', 2);
add_filter('manage_pages_columns', 'j0e_add_thumbnail_column', 2);
function j0e_add_thumbnail_column($j0e_columns)
{
  $j0e_columns['j0e_thumb'] = __('Image', 'simplefolk');
  return $j0e_columns;
}

// Add featured image thumbnail to the WP Admin table.
add_action('manage_posts_custom_column', 'j0e_show_thumbnail_column', 5, 2);
add_action('manage_pages_custom_column', 'j0e_show_thumbnail_column', 5, 2);
function j0e_show_thumbnail_column($j0e_columns, $j0e_id)
{
  switch ($j0e_columns) {
    case 'j0e_thumb':
      if (function_exists('the_post_thumbnail'))
        echo the_post_thumbnail('j0e_admin-featured-image');
      break;
  }
}

// Move the new column at the first place.
add_filter('manage_posts_columns', 'j0e_column_order');
function j0e_column_order($columns)
{
  $n_columns = array();
  $move = 'j0e_thumb'; // which column to move
  $before = 'title'; // move before this column

  foreach ($columns as $key => $value) {
    if ($key == $before) {
      $n_columns[$move] = $move;
    }
    $n_columns[$key] = $value;
  }
  return $n_columns;
}

// Format the column width with CSS
add_action('admin_head', 'j0e_add_admin_styles');
function j0e_add_admin_styles()
{
  echo '<style>
    .column-j0e_thumb {
        width: 60px;
    }
    </style>';
}



/**
 * Returns an image with alt, width, and height attributes
 * @param string $size thumbnail | medium | medium-full | full
 * @return string HTML for image with attributes
 * */
function get_img_with_sizes($size)
{
  $image_id = get_post_thumbnail_id();
  $image_attributes = wp_get_attachment_image_src(get_post_thumbnail_id(), $size, true);
  $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', TRUE);

  if ($image_attributes) :
    ?>
    <img src="<?php echo $image_attributes[0]; ?>" width="<?php echo $image_attributes[1]; ?>" height="<?php echo $image_attributes[2]; ?>" alt="<?php echo $image_alt ?>" />
  <?php endif;
};


///////////////////////////////////////////////////////////////////////////////////////
//                                                                                   //
//    Return only the first category when outputting the previous/next post links    //
//                                                                                   //
///////////////////////////////////////////////////////////////////////////////////////

function my_custom_post_navigation($terms, $object_ids, $taxonomies, $args)
{

  return array_slice($terms, 0, 1);
}


////////////////////////////////////////////////
//                                            //
//    Filter to prepend tags with a hash #    //
//                                            //
////////////////////////////////////////////////

/**
 * Retrieves a list of all tags with the '#' symbol prefix
 *
 * @since 0.0.1
 * @return string HTML of links of hashed tags
 * */


function hashed_tags()
{
  $post_tags = get_the_tags();
  $prefix = '#';
  $separator = ' ';
  $output = '';
  $string = '';

  if (!empty($post_tags)) :
    $string = '<div id="photo_tag_container">Tags: ';
    foreach ($post_tags as $tag) :
      $output .= '<a title="View all photos with the tag #' . strtolower($tag->name) . '"
        href="' . esc_attr(get_tag_link($tag->term_id)) . '">' . $prefix . __($tag->name) . '</a>' . $separator;
    endforeach;
    $string .= trim($output, $separator);
    $string .= '</div>';
  endif;
  return $string;
}


////////////////////////
//                    //
//    Breadcrumbs     //
//                    //
////////////////////////
/**
 * Retrieves the breadcrumb for the header
 * @return string HTML of links and current location
 */

// function the_breadcrumb()
// {
//   /* TODO : Add configs for this */
//   $showOnHome = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show
//   $delimiter = '<span class="delimiter"> &raquo; </span>'; // delimiter between crumbs
//   $home = 'Home'; // text for the 'Home' link
//   $showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
//   $before = '<span class="current">'; // tag before the current crumb
//   $after = '</span>'; // tag after the current crumb
//   $hashtagLink = '<a href="/hashtags/">hashtags</a>';
//   $collectionRootLink = '<a href="/collections/"> Collections</a> ';

//   global $post;
//   $homeLink = esc_url(home_url());
//   if (is_home() || is_front_page()) {
//     if ($showOnHome == 1) {
//       echo '<div id="crumbs" class="breadcrumb"><a href="' . $homeLink . '">' . $home . '</a></div>';
//     }
//   } else {
//     echo '<div id="crumbs"><a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . ' ';
//     // archive / category
//     if (is_archive()) {
//       if (is_category()) {
//         $thisCat = get_category(get_query_var('cat'), false);
//         if ($thisCat->parent != 0) {
//           echo get_category_parents($thisCat->parent, true, ' ' . $delimiter . ' ');
//         }
//         echo $collectionRootLink  . $delimiter . $before . ' ' . single_cat_title('', false) . $after;
//       }
//     }
//     if (is_tax('hashtags')) {
//       echo $hashtagLink  . $delimiter . $before . ' ' . get_queried_object()->name . $after;
//     }
//     if (is_tax('collections')) {
//       echo $collectionRootLink  . $delimiter . $before . ' ' . get_queried_object()->name . $after;
//     }
//     // is_tax()
//     // if (is_tag()) {
//     //   echo $tagLink  . $delimiter . $before . ' ' . get_queried_object()->name . $after;
//     // } elseif (is_single() && !is_attachment()) {
//     //   echo ' ' . $before . get_the_title() . $after;
//     // }


//     if (is_attachment()) {

//       // NOTE: This may be unorthodox, reason is to distinguish between if the previous page
//       // was from custom terms (hashtag and collections).  If neither then it just defaults to the root.  
//       // TODO : This along with actual permalink path need to be consistent
//       // Does this cause duplicate content SEO problems?

//       if (isset($_SERVER['HTTP_REFERER'])) {
//         $ref = ($_SERVER['HTTP_REFERER']);

//         if (strpos($ref, 'hashtags') !== false) {
//           $parts = explode('/hashtags/', $ref);
//           $hashtag_name = str_replace('/', '', $parts[1]);
//           $hashtag_path = $parts[0] . '/hashtags/';
//           echo '<a href="' . $hashtag_path . '">hashtags</a>' . $delimiter;
//           echo '<a href="' . $hashtag_path . $parts[1] . '"> ' . $hashtag_name . '</a>' . $delimiter;
//         } else {
//           // did not come from hashtag page then show as child of collection;
//           $collection = get_the_terms($post, 'collections');
//           if ($collection) {
//             $collection_name = $collection[0]->name;
//             $collection_link = get_term_link($collection_name, 'collections');
//             $collection_link_HTML = '<a href="' . $collection_link . '">' . $collection_name . '</a>';
//             echo $collectionRootLink . $delimiter . $collection_link_HTML . $delimiter;
//           };
//         }
//       }

//       echo ' ' . $before . get_the_title() . $after;
//     }

//     if (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
//       // $post_type = get_post_type_object(get_post_type());
//       // echo $before . $post_type->labels->singular_name . $after;
//     } elseif (is_page() && !$post->post_parent) {
//       if ($showCurrent == 1) {
//         echo $before . get_the_title() . $after;
//       }
//     } elseif (is_page() && $post->post_parent) {
//       $parent_id = $post->post_parent;
//       $breadcrumbs = array();
//       while ($parent_id) {
//         $page = get_post($parent_id);
//         $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
//         $parent_id = $page->post_parent;
//       }
//       $breadcrumbs = array_reverse($breadcrumbs);
//       for ($i = 0; $i < count($breadcrumbs); $i++) {
//         echo $breadcrumbs[$i];
//         if ($i != count($breadcrumbs) - 1) {
//           echo ' ' .
//             $delimiter . ' ';
//         }
//       }
//       if ($showCurrent == 1) {
//         echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
//       }
//     }
//     echo '</div>';
//   }
// }


////////////////////////////////////////
//                                    //
//    Check cookie for light theme    //
//                                    //
////////////////////////////////////////

function light_mode($classes)
{
  if (isset($_COOKIE["mode"])) :
    if ($_COOKIE["mode"] == 'light') :
      return array_merge($classes, array('lightmode'));
    endif;
  endif;
  return $classes;
}
add_filter('body_class', 'light_mode');


/////////////////////////
// Metaboxes //
/////////////////////////

/**
 * Register meta box for media attachment
 **/

function photo_meta_boxes()
{
  add_meta_box('simple_exif', 'Photo Meta', 'photo_meta_callback', 'attachment', 'side', 'low');
}
add_action('add_meta_boxes_attachment', 'photo_meta_boxes');


// wp_read_image_metadata() converts the raw EXIF with wp_exif_frac2dec()
// @see wp-admin/includes/image.php
// Convert decimal to fraction
// @https://stackoverflow.com/a/14357170/82330

function float2rat($n, $tolerance = 1.e-6)
{
  $h1 = 1;
  $h2 = 0;
  $k1 = 0;
  $k2 = 1;
  $b = 1 / $n;
  do {
    $b = 1 / $b;
    $a = floor($b);
    $aux = $h1;
    $h1 = $a * $h1 + $h2;
    $h2 = $aux;
    $aux = $k1;
    $k1 = $a * $k1 + $k2;
    $k2 = $aux;
    $b = $b - $a;
  } while (abs($n - $h1 / $k1) > $n * $tolerance);
  return "$h1/$k1";
}


/**
 * Display meta boxes for image
 *
 * @param WP_Post $post Current post object.
 */
function photo_meta_callback($post)
{
  wp_nonce_field(basename(__FILE__), "meta-box-nonce");
  require_once ABSPATH . '/wp-admin/includes/image.php'; // Without this wp_read_image_metadata() throws Fatal error: Uncaught Error: Call to undefined function

  $id = get_the_ID();
  $filepath = get_attached_file($id);
  $imageEXIF = wp_read_image_metadata($filepath);


  // error_log("Image EXIF Data: " . print_r($imageEXIF, true));



  // Extract EXIF data and existing meta data
  $exif_data = array(
    'aperture' => empty($imageEXIF['aperture']) ? '' : $imageEXIF['aperture'],
    'camera' => empty($imageEXIF['camera']) ? '' : ucwords(strtolower(explode(' ', $imageEXIF['camera'])[0])) . (isset(explode(' ', $imageEXIF['camera'])[1]) ? ' ' . explode(' ', $imageEXIF['camera'])[1] : ''),
    'iso' => empty($imageEXIF['iso']) ? '' : $imageEXIF['iso'],
    'shutter' => empty($imageEXIF['shutter_speed']) ? '' : float2rat($imageEXIF['shutter_speed']),
    'focal' => empty($imageEXIF['focal_length']) ? '' : $imageEXIF['focal_length'] . 'mm',
    'time' => empty($imageEXIF['created_timestamp']) ? '' : date("F j, Y, g:i A", $imageEXIF['created_timestamp'])
  );

  // error_log("Extracted EXIF Data: " . print_r($exif_data, true));



  $meta_data = array(
    'aperture' => esc_attr(get_post_meta($id, 'aperture', true)),
    'camera' => esc_attr(get_post_meta($id, 'camera', true)),
    'iso' => esc_attr(get_post_meta($id, 'iso', true)),
    'shutter' => esc_attr(get_post_meta($id, 'shutter', true)),
    'focal' => esc_attr(get_post_meta($id, 'focal', true)),
    'time' => esc_attr(get_post_meta($id, 'time', true)),
    'film' => esc_attr(get_post_meta($id, 'film', true)),
    'location' => esc_attr(get_post_meta($id, 'location', true)),
    'print_available' => esc_attr(get_post_meta($id, 'print_available', true)),
    'featured_image' => esc_attr(get_post_meta($id, 'featured_image', true)),
    'make_product' => esc_attr(get_post_meta($id, 'make_product', true)),
    'product_template' => esc_attr(get_post_meta($id, 'product_template', true))
  );

  // error_log("Existing Meta Data: " . print_r($meta_data, true));

  // Fallback to EXIF data if meta data is empty
  foreach ($exif_data as $key => $value) {
    if (empty($meta_data[$key])) {
      $meta_data[$key] = $value;
    }
  }

  // error_log("Final Meta Data: " . print_r($meta_data, true));

  ?>
  <p>
    <label for="camera">Camera : </label>
    <input id="camera" type="text" name="camera" style="margin-right: 10px; width: 100%" value="<?php echo esc_attr($meta_data['camera']); ?>" />
  </p>
  <p>
    <label for="iso">ISO : </label>
    <input id="iso" type="text" name="iso" style="margin-right: 10px; width:100%; text-align: center;" value="<?php echo esc_attr($meta_data['iso']); ?>" />
  </p>
  <p>
    <label for="aperture">Aperture : </label>
    <input id="aperture" type="text" name="aperture" style="margin-right: 10px; width:100%; text-align: center;" value="<?php echo esc_attr($meta_data['aperture']); ?>" />
  </p>
  <p>
    <label for="shutter">Shutter Speed : </label>
    <input id="shutter" type="text" name="shutter" style="margin-right: 10px; width:100%; text-align: center;" value="<?php echo esc_attr($meta_data['shutter']); ?>" />
  </p>
  <p>
    <label for="focal">Focal Length : </label>
    <input id="focal" type="text" name="focal" style="margin-right: 10px; width:100%; text-align: center;" value="<?php echo esc_attr($meta_data['focal']); ?>" />
  </p>
  <p>
    <label for="time">Date Taken : </label>
    <input id="time" type="text" name="time" style="margin-right: 10px; width:100%; text-align: center;" value="<?php echo esc_attr($meta_data['time']); ?>" />
  </p>
  <p>
    <label for="film">Film Type : </label>
    <input id="film" type="text" name="film" style="margin-right: 10px; width:100%; text-align: center;" value="<?php echo esc_attr($meta_data['film']); ?>" />
  </p>
  <p>
    <label for="location">Location : </label>
    <input id="location" type="text" name="location" style="margin-right: 10px; width:100%; text-align: center;" value="<?php echo esc_attr($meta_data['location']); ?>" />
  </p>
  <p>
    <label for="print_available">Print Available : </label>
    <input id="print_available" type="checkbox" name="print_available" <?php checked($meta_data['print_available'], 'on'); ?> />
  </p>
  <p>
    <label for="featured_image">Featured Image : </label>
    <input id="featured_image" type="checkbox" name="featured_image" <?php checked($meta_data['featured_image'], 'on'); ?> />
  </p>
  <?php

  if (class_exists('WooCommerce')) {

    $products = wc_get_products(array('limit' => -1));
  ?>
    <p>
      <label for="make_product">Make Product : </label>
      <input id="make_product" type="checkbox" name="make_product" <?php checked($meta_data['make_product'], 'on'); ?> />
    </p>
    <p>
      <label for="product_template">Product Template : </label>
      <select id="product_template" name="product_template" style="width: 100%">
        <option value="">Select a template</option>
        <?php foreach ($products as $product) : ?>
          <option value="<?php echo $product->get_id(); ?>" <?php selected($meta_data['product_template'], $product->get_id()); ?>><?php echo $product->get_name(); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </p>
  <?php
  }
}

/**
 * Save post meta box content.
 *
 * @param int $post_id Post ID
 */

function simple_save_meta($post_id)
{
  // Check if this is an autosave, if so, return
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  // Check if the user has permissions to save data
  if (!current_user_can('edit_post', $post_id)) {
    return;
  }

  // Check if this is a revision, if so, return
  if (wp_is_post_revision($post_id)) {
    return;
  }

  // Verify the nonce
  if (!isset($_POST['meta-box-nonce']) || !wp_verify_nonce($_POST['meta-box-nonce'], basename(__FILE__))) {
    return;
  }

  // Ensure this is for attachments
  if (get_post_type($post_id) !== 'attachment') {
    return;
  }

  $textfields = [
    'camera', 'iso', 'aperture', 'shutter', 'focal',
    'film', 'time', 'location', 'print_available',
    'featured_image', 'make_product', 'product_template'
  ];

  foreach ($textfields as $field) {
    if (array_key_exists($field, $_POST)) {
      update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
    }
  }

  if (isset($_POST['make_product']) && $_POST['make_product'] == 'on') {
    create_product_from_image($post_id);
  }
}

add_action('save_post', 'simple_save_meta', 10, 1);
add_action('edit_attachment', 'simple_save_meta');


//////////////////////////////////////////////
//                                          //
//    Make product from media attachment    //
//                                          //
//////////////////////////////////////////////


function create_product_from_image($post_id)
{
  if (!class_exists('WooCommerce')) {
    error_log('WooCommerce not found.');
    return;
  }

  // Check if the product already exists
  $product_id = get_post_meta($post_id, '_product_id', true);
  if ($product_id) {
    error_log('Product already exists with ID: ' . $product_id);
    return; // Product already created
  }

  $title = get_the_title($post_id);
  $description = get_post_meta($post_id, 'description', true);
  $price = 10.00; // Default price
  $template_id = get_post_meta($post_id, 'product_template', true);

  $sku = sanitize_title($title) . '-' . uniqid();

  if ($template_id) {
    error_log('Template ID found: ' . $template_id);
    $template_product = wc_get_product($template_id);

    if ($template_product && $template_product->is_type('variable')) {
      error_log('Template product is variable.');

      $product = new WC_Product_Variable();
      $product->set_name($title);
      $product->set_description($description);
      $product->set_image_id($post_id);
      $product->set_sku($sku);

      // Get the attributes of the template product
      $attributes = $template_product->get_attributes();
      $product->set_attributes($attributes);

      // Save the variable product
      $product->save();
      $product_id = $product->get_id();

      // Create variations based on template product
      $variation_ids = $template_product->get_children();
      foreach ($variation_ids as $variation_id) {
        $variation = wc_get_product($variation_id);

        if ($variation) {
          $new_variation = new WC_Product_Variation();
          $new_variation->set_parent_id($product_id);
          $new_variation->set_attributes($variation->get_attributes());
          $new_variation->set_regular_price($variation->get_regular_price());
          $new_variation->set_image_id($post_id);
          $new_variation->set_stock_status('instock');
          $new_variation->save();

          error_log('New variation created with ID: ' . $new_variation->get_id());
        }
      }

      // Ensure the variable product is in stock
      $product->set_stock_status('instock');
      $product->save();

      error_log('Variable product saved with ID: ' . $product_id);
    } else {
      error_log('Template product is not variable.');
    }
  } else {
    $product = new WC_Product_Simple();
    $product->set_name($title);
    $product->set_description($description);
    $product->set_regular_price($price);
    $product->set_image_id($post_id);
    $product->set_sku($sku);
    $product->set_stock_status('instock');
    $product->save();
    $product_id = $product->get_id();

    error_log('Simple product created with ID: ' . $product_id);
  }

  // Save the product ID and details in the image meta to avoid duplicate creation
  update_post_meta($post_id, '_product_id', $product_id);
  update_post_meta($post_id, '_product_price', $price);
  update_post_meta($post_id, '_product_template', $template_id);
}

/**
 * Display data from the media meta boxes
 *
 * Displays optional location, camera type, film, and prints available
 *
 * @param $id The current post id  
 * @return string HTML of additional data as paragraphs
 */

function display_photo_meta($id)
{
  $attachment_id = (get_post_thumbnail_id($id));

  error_log("Attachment ID: " . $attachment_id);



  $core_meta = wp_get_attachment($attachment_id);
  // Check if the ID is a product ID or an attachment ID
  $attachment_id = (get_post_type($id) == 'product') ? get_post_thumbnail_id($id) : $id;
  error_log("Attachment ID: " . $attachment_id);
  $core_meta = wp_get_attachment($attachment_id);

  error_log("Core Meta: " . print_r($core_meta, true));

  if (isset($core_meta) && !empty($core_meta)) {
    if (!empty($core_meta['description'])) {
      echo '<p>' . $core_meta['description'] . '</p>';
    }
  }
  get_hashtags($id);

  $attachment_fields = get_post_custom($attachment_id);

  echo '<ul class="photo-exif">';

  if (array_key_exists('camera', $attachment_fields)) {
    $cam = $attachment_fields['camera'][0];
    if ($cam) :
      echo '<li>Camera: ' . $cam . '</li>';
    endif;
  }

  if (array_key_exists('iso', $attachment_fields)) {
    $iso = $attachment_fields['iso'][0];
    if ($iso) :
      echo '<li>ISO: ' . $iso . '</li>';
    endif;
  }

  if (array_key_exists('aperture', $attachment_fields)) {
    $aperture = $attachment_fields['aperture'][0];
    if ($aperture) :
      echo '<li>Aperture: ' . $aperture . '</li>';
    endif;
  }

  if (array_key_exists('shutter', $attachment_fields)) {
    $shutter = $attachment_fields['shutter'][0];
    if ($shutter) :
      echo '<li>Shutter speed:' . $shutter . '</li>';
    endif;
  }

  if (array_key_exists('focal', $attachment_fields)) {
    $focal = $attachment_fields['focal'][0];
    if ($focal) :
      echo '<li>Focal length: ' . $focal . '</li>';
    endif;
  }

  if (array_key_exists('film', $attachment_fields)) {
    $film = $attachment_fields['film'][0];
    if ($film) :
      echo '<li>Film: ' . $film . '</li>';
    endif;
  }

  if (array_key_exists('time', $attachment_fields)) {
    $time = $attachment_fields['time'][0];
    if ($time) :
      echo '<li>Time of capture: ' . $time . '</li>';
    endif;
  }

  if (array_key_exists('location', $attachment_fields)) {
    $loc = $attachment_fields['location'][0];
    if ($loc) :
      echo '<li>Location: ' . $loc . '</li>';
    endif;
  }

  echo '</ul>';
}



/**
 * Display partial data from the media meta boxes
 *
 * Displays title, caption, and link to full attachment
 *
 * @param $id The current post id
 * @return string HTML of additional data
 */
function modal_display_photo_meta($id)
{
  $attachment_id = (get_post_thumbnail_id($id));

  $image_title = get_the_title($attachment_id);
  $image_caption =  wp_get_attachment_caption($attachment_id);
  $image_link = get_attachment_link();

  $attachment_fields = get_post_custom($attachment_id);
  // wut($attachment_fields);
  if ($image_title) :
    echo '<h1>' . $image_title . '</h1>';
  endif;
  if ($image_caption) :
    echo '<p>' . $image_caption . '</p>';
  endif;
  if ($image_link) :
    echo '<a href ="' . $image_link . '">View full post &raquo; </a>';
  endif;
}

/**
 * Return all of the data from attachment

 *
 * @param $attachment_id The id of the media attachment, a featured post can use get_post_thumbnail_id($post);
 * @return array HTML and data to sidebar.php
 */
function wp_get_attachment($attachment_id)
{

  $attachment = get_post($attachment_id);
  return array(
    'alt' => get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
    'caption' => $attachment->post_excerpt,
    'description' => $attachment->post_content,
    'href' => get_permalink($attachment->ID),
    'src' => $attachment->guid,
    'title' => $attachment->post_title
  );
}

////////////////////////////////////////////////////////////
//                                                        //
//    Get all media that has been converted to product    //
//                                                        //
////////////////////////////////////////////////////////////

function get_product_media_attachments()
{
  $args = array(
    'post_type'      => 'attachment',
    'posts_per_page' => -1,
    'post_status'    => 'inherit',
    'meta_query'     => array(
      array(
        'key'     => '_product_id',
        'compare' => 'EXISTS',
      ),
    ),
  );

  $attachments = new WP_Query($args);
  return $attachments;
}

// COMMENTS

// Need this to be able to check for user info if user is logged in
function localize_current_user()
{
  if (is_user_logged_in()) {
    $current_user = wp_get_current_user();
    wp_localize_script('comment-ajax', 'ajax_params', array(
      'ajax_url' => admin_url('admin-ajax.php'),
      'nonce' => wp_create_nonce('comment_nonce'),
      'current_user' => array(
        'ID' => $current_user->ID,
        'display_name' => $current_user->display_name,
        'user_email' => $current_user->user_email
      )
    ));
  } else {
    wp_localize_script('comment-ajax', 'ajax_params', array(
      'ajax_url' => admin_url('admin-ajax.php'),
      'nonce' => wp_create_nonce('comment_nonce'),
      'current_user' => false
    ));
  }
}
add_action('wp_enqueue_scripts', 'localize_current_user');


// AJAX handler for comments
function submit_comment_form()
{
  check_ajax_referer('comment_nonce', 'nonce');

  // Get the submitted data
  $comment_post_ID = sanitize_text_field($_POST['comment_post_ID']);
  $author = isset($_POST['author']) ? sanitize_text_field($_POST['author']) : '';
  $comment_content = isset($_POST['comment']) ? sanitize_text_field($_POST['comment']) : '';
  $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : ''; // Get email value



  // Check if name and message are provided
  if (empty($author) || empty($comment_content)) {

    $response = array('success' => false, 'message' => 'Name and message are required fields.');
  } else {
    // Prepare comment data
    $comment_data = array(
      'comment_post_ID' => $comment_post_ID,
      'comment_author' => $author,
      'comment_content' => $comment_content,
      'email' => $email,
      'comment_author_url' => '', // Set to empty string if not provided
      'comment_author_email' => $email, // Use provided email or set to empty string
    );

    // Create the comment
    $comment_id = wp_new_comment($comment_data);

    if ($comment_id) {
      $response = array('success' => true, 'message' => 'Your comment has been submitted successfully.');
    } else {
      $response = array('success' => false, 'message' => 'Error occurred while processing your comment.');
    }
  }

  // Send JSON response
  wp_send_json($response);
}

add_action('wp_ajax_submit_comment_form', 'submit_comment_form');
add_action('wp_ajax_nopriv_submit_comment_form', 'submit_comment_form');

function custom_comment($comment, $args, $depth)
{
  $GLOBALS['comment'] = $comment;
  ?>
  <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
    <article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
      <footer class="comment-meta">
        <div class="comment-author-avatar">
          <?php echo get_avatar($comment, 64); ?>
        </div>
        <div class="comment-author vcard">
          <?php echo get_comment_author_link() ?>
        </div>
        <div class="comment-metadata">
          <?php printf('<time datetime="%1$s">%2$s</time>', get_comment_time('c'), sprintf(__('%1$s at %2$s', 'simplefolk'), get_comment_date(), get_comment_time())); ?>
          <span class="comment-permalink">
            <a href="<?php echo esc_url(get_comment_link($comment->comment_ID)); ?>" title="Comment link">Link
            </a>
          </span>
          <?php edit_comment_link(__('Edit', 'simplefolk'), '<span class="edit-link">', '</span>'); ?>
        </div>
      </footer>
      <div class="comment-content">
        <?php comment_text(); ?>
      </div>
      <?php
      comment_reply_link(array_merge($args, array(
        'depth' => $depth,
        'max_depth' => $args['max_depth'],
        'before' => '<span class="reply-link">',
        'after' => '</span>',
      )));
      ?>
    </article>
    <?php
  }

  // Prevents Wordpress from setting user as anonymous if it can not locate their account
  function set_comment_user($comment_data)
  {
    if (!is_user_logged_in() && empty($comment_data['comment_author_email'])) {
      $comment_data['user_ID'] = 0;
      $comment_data['comment_author'] = sanitize_text_field($_POST['author']);
    }
    return $comment_data;
  }
  add_filter('preprocess_comment', 'set_comment_user');

  // Custom Form

  function custom_comment_form($args = array(), $post_id = null)
  {
    if (null === $post_id) {
      $post_id = get_the_ID();
    } else {
      $id = $post_id;
    }

    $commenter = wp_get_current_commenter();
    $req       = get_option('require_name_email');
    $aria_req  = ($req ? " aria-required='true'" : '');

    $fields = array(
      'author' => '<p class="comment-form-author"><label for="author">' . __('Name', 'simplefolk') . ($req ? ' <span class="required">*</span>' : '') . '</label>
                  <input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30" maxlength="100"' . $aria_req . ' /></p>',
      'email'  => '<p class="comment-form-email"><label for="email">' . __('Email', 'simplefolk') . ($req ? ' <span class="required">*</span>' : '') . '</label>
                  <input id="email" name="email" type="email" value="' . esc_attr($commenter['comment_author_email']) . '" size="30" maxlength="100"' . $aria_req . ' /></p>',
      'comment_field' => '<p class="comment-form-comment"><label for="comment">' . __('Comment', 'simplefolk') . '<span class="required">*</span></label>
                          <textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>',
      'website' => '<p class="website-comment" style="display: none;"><label for="website">' . __('Website', 'simplefolk') . '</label> <span class="required">*</span><textarea id="website" name="website" cols="45" rows="8" maxlength="50" ></textarea></p>'
    );

    ob_start();
    comment_form(array(
      'fields'               => apply_filters('comment_form_default_fields', $fields),
      'comment_field'        => '',
      'comment_notes_before' => '',
      'comment_notes_after'  => '',
      'title_reply'          => __('Leave a Comment', 'simplefolk'),
      'cancel_reply_link'    => __('Cancel reply', 'simplefolk'),
      'class_submit'         => 'submit',
    ));
    $form = ob_get_contents();
    ob_end_clean();

    echo apply_filters('custom_comment_form_output', $form);
  }


  ////////////////////////////////
  //                            //
  //    WooCommerce related     //
  //                            //
  ////////////////////////////////

  // Theme support is defined above... 

  /*
 * Variation Dropdown to Radio Button Code by wpcookie
 * https://redpishi.com/wordpress-tutorials/dropdown-to-radio-button/
 */
  add_action('woocommerce_variable_add_to_cart', function () {
    add_action('wp_print_footer_scripts', function () {
      $color = "#1c1c1c";

    ?>
      <script type="text/javascript">
        // DOM Loaded
        document.addEventListener('DOMContentLoaded', function() {

          // Get Variation Pricing Data
          var variations_form = document.querySelector('form.variations_form');
          var data = variations_form.getAttribute('data-product_variations');
          data = JSON.parse(data);

          // Loop Drop Downs
          document.querySelectorAll('table.variations select')
            .forEach(function(select) {

              // Loop Drop Down Options
              select.querySelectorAll('option')
                .forEach(function(option) {

                  // Skip Empty
                  if (!option.value) {
                    return;
                  }

                  // Get Pricing For This Option
                  var pricing = '';
                  data.forEach(function(row) {
                    if (row.attributes[select.name] == option.value) {
                      pricing = row.price_html;
                    }
                  });

                  var span = document.createElement('span');

                  // Create Radio
                  var radio = document.createElement('input');
                  radio.type = 'radio';
                  radio.name = select.name;
                  radio.value = option.value;
                  radio.checked = option.selected;
                  radio.setAttribute('id', option.value);
                  var label = document.createElement('label');
                  label.htmlFor = option.value;
                  label.appendChild(document.createTextNode(' ' + option.text + ' '));

                  span.appendChild(radio);
                  span.appendChild(label);


                  // Insert Radio
                  select.closest('td').appendChild(span);

                  // Handle Clicking
                  radio.addEventListener('click', function(event) {
                    select.value = radio.value;
                    jQuery(select).trigger('change');
                  });

                }); // End Drop Down Options Loop

              // Hide Drop Down
              select.style.display = 'none';

            }); // End Drop Downs Loop

        }); // End Document Loaded
      </script>

    <?php
    });
  });

  /**
   * Remove Product Tabs & Move Long Description and move it into the main area
   */
  remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
  add_action('woocommerce_single_product_summary', 'simple_wc_output_long_description', 125);
  remove_action('woocommerce_after_main_content', 'woocommerce_output_product_data_tabs', 10);
  // add_action('woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 60);



  function simple_wc_output_long_description()
  {
    ?>
    <?php the_content();
    ?>
  <?php
  }


  // Remove the default short description action
  remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);

  // Add the short description above the price
  add_action('woocommerce_single_product_summary', 'custom_move_short_description', 5);
  function custom_move_short_description()
  {
    global $product;

    // Display the short description
    if (!empty($product->get_short_description())) {
      echo '<div class="woocommerce-product-short-description">';
      echo apply_filters('woocommerce_short_description', $product->get_short_description());
      echo '</div>';
    }
  }



  /**
   * Prevent redirect after adding item to cart
   */

  function modify_add_to_cart_redirect($url)
  {
    if (is_attachment()) {
      // return wc_get_cart_url(); // Redirect to the cart page
      // return get_permalink(); // Stay on the current page
      return false;
    }
    return $url;
  }
  add_filter('woocommerce_add_to_cart_redirect', 'modify_add_to_cart_redirect');


  /* 
 Add my own class to button - must be a better way to adjust this color
 */

  function custom_add_to_cart_button_classes($classes, $product)
  {
    $classes[] = 'shop-button';
    return $classes;
  }
  add_filter('woocommerce_product_add_to_cart_class', 'custom_add_to_cart_button_classes', 10, 2);




  function custom_product_variation_add_to_cart($atts)
  {
    if (!isset($atts['id'])) {
      return '<p>No product ID provided.</p>';
    }

    $product = wc_get_product($atts['id']);

    if (!$product || !$product->is_type('variable')) {
      return '<p>Invalid product ID or the product is not a variable product.</p>';
    }

    // Ensure product is loaded for variations
    if (!$product->is_purchasable() || !$product->is_in_stock()) {
      return '<p>This product is currently out of stock and unavailable.</p>';
    }

    ob_start();
  ?>
    <form class="variations_form cart" method="post" enctype="multipart/form-data" data-product_id="<?php echo esc_attr($product->get_id()); ?>" data-product_variations="<?php echo htmlspecialchars(json_encode($product->get_available_variations())); ?>">
      <?php
      $attributes = $product->get_variation_attributes();
      $selected_attributes = $product->get_default_attributes();

      if (empty($attributes)) {
        echo '<p class="stock out-of-stock">' . __('This product is currently out of stock and unavailable.', 'woocommerce') . '</p>';
      } else {
      ?>
        <table class="variations" cellspacing="0">
          <tbody>
            <?php foreach ($attributes as $attribute_name => $options) : ?>
              <tr>
                <td class="label">
                  <label for="<?php echo esc_attr(sanitize_title($attribute_name)); ?>">
                    <?php echo wc_attribute_label($attribute_name); ?>
                  </label>
                </td>
                <td class="value">
                  <?php
                  wc_dropdown_variation_attribute_options(array(
                    'options' => $options,
                    'attribute' => $attribute_name,
                    'product' => $product,
                    'selected' => isset($selected_attributes[$attribute_name]) ? $selected_attributes[$attribute_name] : '',
                  ));
                  echo '<a class="reset_variations" href="#">' . esc_html__('Clear', 'woocommerce') . '</a>';
                  ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php
        do_action('woocommerce_before_add_to_cart_button');
        ?>
        <div class="single_variation_wrap">
          <?php
          do_action('woocommerce_before_single_variation');
          do_action('woocommerce_single_variation');
          do_action('woocommerce_after_single_variation');
          ?>
        </div>
        <?php
        do_action('woocommerce_after_add_to_cart_button');
        ?>
      <?php } ?>
    </form>
  <?php
    return ob_get_clean();
  }
  add_shortcode('custom_variation_add_to_cart', 'custom_product_variation_add_to_cart');



  // Debug with template hooks being visible 

  // add_filter('woocommerce_locate_template', 'check_woocommerce_template', 10, 3);
  // function check_woocommerce_template($template, $template_name, $template_path)
  // {
  //   error_log("Template loaded: $template");
  //   return $template;
  // }


//   add_filter('woocommerce_locate_template', 'debug_woocommerce_template_loading', 10, 3);
// function debug_woocommerce_template_loading($template, $template_name, $template_path) {
//     $debug_info = [
//         'Template Name' => $template_name,
//         'Template Path' => $template_path,
//         'Template Chosen' => $template
//     ];
//     error_log(print_r($debug_info, true));
//     return $template;
// }