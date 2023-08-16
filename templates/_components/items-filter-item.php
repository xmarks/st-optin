<?php // Prepare ACF data for Items

// Icon
//the_field('st-item-icon-rest');
//the_field('st-item-icon-hover');

// Content


?>

<div class="st-items-filter__item d-flex">

    <div class="inside d-flex flex-column align-items-center">
        <div class="item__image-wrapper">
            <img class="img-fluid" src="<?= the_field('st-item-icon-rest'); ?>" alt="item-<?= get_the_ID(); ?>-icon-rest"/>
            <img class="img-fluid" src="<?= the_field('st-item-icon-hover'); ?>" alt="item-<?= get_the_ID(); ?>-icon-hover"/>
        </div>

        <p class="m-0 text-center"><?= the_field('st-item-intro'); ?></p>
    </div>

</div>
