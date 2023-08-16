<?php

/**
 * Shortcode Load More Filter for Items post type
 *
 * @param $atts
 * @return false|string
 */
function st_items_filter_shortcode($atts)
{

    ob_start();

    // Extract shortcode attributes
    $atts = shortcode_atts(array(
        'categories' => '',
        'per_page'   => '3',
    ), $atts);

    // Clean Spaces if present
    $cats = str_replace(' ', '', $atts['categories']);;

    // Display items based on the shortcode attributes
    $args = array(
        'post_type'      => 'item',
        'order_by'       => 'date',
        'order'          => 'desc',
        'posts_per_page' => $atts['per_page'],
    );

    // Add Categories to Query $args
    if (!empty($cats)) {
        // $args['category__in'] = explode(',', $cats);
        $args['category__in'] = explode(',', $cats)[0]; // First Category Only
    }

    // Query Items
    $items = new WP_Query($args);

    // Categories data
    $categories = array_map(function ($category_id) {
        return get_category($category_id);
    }, array_map('trim', explode(',', $atts['categories'])));

    ?>

    <?php // Display Category Filter
    ?>

    <div class="container">
        <div class="st-items-filter">
            <div class="st-items-filter__cat-list-wrapper d-flex justify-content-center">
                <ul class="st-items-filter__cat-list">
                    <?php foreach ($categories as $key => $category) : ?>
                        <li>
                            <a class="items-filter__cat-list-item <?= $key == 0 ? 'active' : '' ?>" href="#!"
                               data-ppp="<?= $args['posts_per_page'] ?>"
                               data-cid="<?= $category->cat_ID; ?>">
                                <?= $category->name; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <?php
            // Display Posts
            if ($items->have_posts()): ?>
                <div class="st-items-filter__items-wrapper">
                    <?php
                    while ($items->have_posts()) : $items->the_post(); ?>
                        <?php get_template_part('templates/_components/items-filter-item'); ?>
                    <?php endwhile;
                    ?>
                </div>
                <?php wp_reset_postdata(); ?>
            <?php endif; ?>

            <div class="load-more">
                <button class="load-more-button" data-page="2">Load more...</button>
            </div>
        </div>
    </div>


    <?php

    return ob_get_clean();
}

add_shortcode('st-items-filter', 'st_items_filter_shortcode');


//$categories = get_categories();