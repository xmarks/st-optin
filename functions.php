<?php
/**
 * Understrap Child Theme functions and definitions
 *
 * @package stOptin
 */

// Exit if accessed directly.
use JetBrains\PhpStorm\NoReturn;

defined('ABSPATH') || exit;

/**
 * Include Helpers
 */
include_once('inc/helpers.php');


/**
 * Enqueue stylesheet / javascript file
 */
function st_child_scripts(): void
{
    wp_enqueue_script('st-items-filter', get_stylesheet_directory_uri() . '/assets/js/main.js', array('jquery'), '', true);
    wp_enqueue_style('optin-styles', get_stylesheet_directory_uri() . '/style.css', false, '1.0', 'all');
}

add_action('wp_enqueue_scripts', 'st_child_scripts', 99);


/**
 * Contact 7
 * Remove auto p-tags
 *
 * ref: https://pineco.de/snippets/remove-p-tag-from-contact-form-7/
 */
add_filter('wpcf7_autop_or_not', '__return_false');


/**
 * Prevent WP from adding <p> tags on pages
 *
 * ref: https://templ.io/blog/prevent-wordpress-from-adding-p-tags-in-content/
 *
 * @param $content
 * @return mixed
 */
function disable_wp_auto_p($content): mixed
{
    if (is_singular('page')) {
        remove_filter('the_content', 'wpautop');
        remove_filter('the_excerpt', 'wpautop');
    }
    return $content;
}

add_filter('the_content', 'disable_wp_auto_p', 0);


/**
 * Custom Post Type "Items"
 */
function st_post_type_item(): void
{
    $labels = array(
        'name'                  => _x('Items', 'Post Type General Name', 'st_optin_domain'),
        'singular_name'         => _x('Item', 'Post Type Singular Name', 'st_optin_domain'),
        'menu_name'             => __('Items', 'st_optin_domain'),
        'name_admin_bar'        => __('Item', 'st_optin_domain'),
        'archives'              => __('Item Archives', 'st_optin_domain'),
        'attributes'            => __('Item Attributes', 'st_optin_domain'),
        'parent_item_colon'     => __('Parent Item:', 'st_optin_domain'),
        'all_items'             => __('All Items', 'st_optin_domain'),
        'add_new_item'          => __('Add New Item', 'st_optin_domain'),
        'add_new'               => __('Add New', 'st_optin_domain'),
        'new_item'              => __('New Item', 'st_optin_domain'),
        'edit_item'             => __('Edit Item', 'st_optin_domain'),
        'update_item'           => __('Update Item', 'st_optin_domain'),
        'view_item'             => __('View Item', 'st_optin_domain'),
        'view_items'            => __('View Items', 'st_optin_domain'),
        'search_items'          => __('Search Item', 'st_optin_domain'),
        'not_found'             => __('Not found', 'st_optin_domain'),
        'not_found_in_trash'    => __('Not found in Trash', 'st_optin_domain'),
        'featured_image'        => __('Featured Image', 'st_optin_domain'),
        'set_featured_image'    => __('Set featured image', 'st_optin_domain'),
        'remove_featured_image' => __('Remove featured image', 'st_optin_domain'),
        'use_featured_image'    => __('Use as featured image', 'st_optin_domain'),
        'insert_into_item'      => __('Insert into item', 'st_optin_domain'),
        'uploaded_to_this_item' => __('Uploaded to this item', 'st_optin_domain'),
        'items_list'            => __('Items list', 'st_optin_domain'),
        'items_list_navigation' => __('Items list navigation', 'st_optin_domain'),
        'filter_items_list'     => __('Filter items list', 'st_optin_domain'),
    );
    $args   = array(
        'label'               => __('Item', 'st_optin_domain'),
        'description'         => __('Custom post type for items', 'st_optin_domain'),
        'labels'              => $labels,
        'supports'            => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'taxonomies'          => array('category', 'post_tag'),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-superhero-alt',
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
    );
    register_post_type('item', $args);
}

add_action('init', 'st_post_type_item', 0);


/**
 * Include Theme Shortcodes
 */
// Include all PHP files from the "shortcodes" folder
$shortcodes_folder = get_stylesheet_directory() . '/shortcodes/';

if (is_dir($shortcodes_folder)) {
    $shortcode_files = scandir($shortcodes_folder);

    foreach ($shortcode_files as $shortcode_file) {
        if (pathinfo($shortcode_file, PATHINFO_EXTENSION) === 'php') {
            require_once $shortcodes_folder . $shortcode_file;
        }
    }
}


/**
 * AJAX Callback
 * Items Filter Shortcode
 * Category Switcher
 *
 * @return void
 */
#[NoReturn] function filter_projects(): void
{
    // Calculate offset based on the current page
    $paged = isset($_POST['page']) ? intval(htmlspecialchars($_POST['page'])) : 1;

    // Display items based on attributes
    $args = array(
        'post_type'      => 'item',
        'order_by'       => 'date',
        'order'          => 'desc',
        'posts_per_page' => htmlspecialchars($_POST['ppp']),
        'category__in'   => htmlspecialchars($_POST['cid']),
        'paged'          => $paged,
    );

    // Query Items
    $items = new WP_Query($args);

    $response = '';

    // Display Posts
    if ($items->have_posts()) {
        while ($items->have_posts()) {
            $items->the_post();
            $response .= get_template_part('templates/_components/items-filter-item');
        }
    } else {
        $response .= 'empty';
    }

    echo $response;

    wp_die();
}

add_action('wp_ajax_filter_projects', 'filter_projects');
add_action('wp_ajax_nopriv_filter_projects', 'filter_projects');


/**
 * Check if shortcode [st-hero] is called
 * Used to add conditional class
 * on '/page-templates/fluid-fullwidthpage.php'
 *
 * @return bool
 */
function using_shortcode_st_hero(): bool
{
    global $post;
    if (has_shortcode($post->post_content, 'st-hero')) {
        return true;
    }
    return false;
}