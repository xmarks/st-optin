<?php

/**
 * Homepage HERO Shortcode
 *
 * @param $atts
 * @param $content
 * @return false|string
 */
function st_hero_shortcode($atts, $content)
{

    ob_start();

    // Extract shortcode attributes
    $atts = shortcode_atts(array(
        'contact_id' => '0',
        'bg_id'      => '0',
    ), $atts);

    $hero_bg = wp_get_attachment_image_url($atts['bg_id'], 'full');
    ?>

    <div id="st-hero" class="d-flex flex-column">

        <div class="st-hero__mask-gradient"></div>
        <div class="st-hero__mask-image" style='background-image: url("<?= $hero_bg; ?>")'></div>

        <div class="container flex-grow-1  d-flex align-items-center">
            <div class="st-hero__content">
                <?= do_shortcode($content) ?>

                <?= do_shortcode('[contact-form-7 id="' . $atts['contact_id'] . '"]') ?>
            </div>
        </div>


    </div>

    <?php

    return ob_get_clean();
}

add_shortcode('st-hero', 'st_hero_shortcode');
