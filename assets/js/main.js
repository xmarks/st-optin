/**
 * AJAX Script for Items Load More
 */
jQuery(function ($) {
    $('.items-filter__cat-list-item').on('click', function () {
        $('.items-filter__cat-list-item').removeClass('active');
        $(this).addClass('active');

        // Reset the page counter when switching categories
        $('.load-more-button').data('page', 2);

        $.ajax({
            type: 'POST',
            url: '/wp-admin/admin-ajax.php',
            dataType: 'html',
            data: {
                action: 'filter_projects',
                cid: $(this).data('cid'),
                ppp: $(this).data('ppp'),
            },
            success: function (res) {
                $('.st-items-filter__items-wrapper').html(res);
                // Show the "Load More" button
                $('.load-more').show();
            },
            // error: function () {
            //     $('.st-items-filter__items-wrapper').html('error');
            // }
        });
    });

    $('.load-more-button').on('click', function () {
        var $this = $(this);
        var nextPage = $this.data('page');

        $.ajax({
            type: 'POST',
            url: '/wp-admin/admin-ajax.php',
            dataType: 'html',
            data: {
                action: 'filter_projects',
                cid: $('.items-filter__cat-list-item.active').data('cid'),
                ppp: $('.items-filter__cat-list-item.active').data('ppp'),
                page: nextPage,
            },
            success: function (res) {
                if (res.trim() !== 'empty') {
                    $('.st-items-filter__items-wrapper').append(res);
                    nextPage++; // Increment the page counter
                    $this.data('page', nextPage);
                } else {
                    // Hide the "Load More" button if no more items are available
                    $('.load-more').hide();
                }
            },
            // error: function () {
            //     $('.st-items-filter__items-wrapper').html('error');
            // }
        });
    });
});


/**
 * JS class manipulation for Main Menu
 * MegaMenu styling logic
 */
document.addEventListener("DOMContentLoaded", function () {
    // Get the main menu ul element
    var mainMenu = document.getElementById("main-menu");

    // Find if there are 'First Level' <li> with submenus
    var topLevelSubmenuHolders = mainMenu.querySelectorAll("#main-menu > li.menu-item-has-children");

    // Loop through each 'First Level' <li> items
    topLevelSubmenuHolders.forEach(function (topLevelItem) {
        // Find the direct ul element with class "dropdown-menu" inside the current top-level menu item
        var firstLevelDropdown = topLevelItem.querySelector("ul.dropdown-menu");

        if (firstLevelDropdown) {
            // Find direct <li> items with drop-down menus 'Second Levels'
            var secondLevelSubmenuHolders = firstLevelDropdown.querySelectorAll("li.menu-item-has-children");

            // If 'Second Level' submenus found > convert to MegaMenu Dropdown
            if (secondLevelSubmenuHolders.length > 0) {

                // Add class="is-megamenu" to firstLevelDropdown
                firstLevelDropdown.classList.add("megamenu-wrapper");

                // Add class="is-megamenu" to direct parent <li class="menu-item-has-children"> of firstLevelDropdown
                var parentLi = firstLevelDropdown.closest("li.menu-item-has-children");
                if (parentLi) {
                    parentLi.classList.add("has-megamenu");
                }

                // firstLevelDropdown.querySelector("li.menu-item").forEach( function(secondLevelItem) {
                //     console.log(secondLevelItem);
                // });

                // Add class="is-megamenu" to secondLevelSubmenuHolders > ul.dropdown-menu
                secondLevelSubmenuHolders.forEach(function (secondLevelMegamenuItem) {
                    var secondLevelDropdown = secondLevelMegamenuItem.querySelector("ul.dropdown-menu");
                    if (secondLevelDropdown) {
                        secondLevelDropdown.classList.add("dropdown-megamenu");
                    }
                });
            }
        }
    });
});


