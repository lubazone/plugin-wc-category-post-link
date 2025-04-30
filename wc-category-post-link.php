<?php
/*
Plugin Name: Woo Category Custom WYSIWYG Content
Description: Adds a rich text editor to WooCommerce categories and displays it after the product listings.
Version: 1.1
Author: Your Name
*/

if (!defined('ABSPATH')) exit;

// Add WYSIWYG editor to Product Category Edit screen
function wccc_add_wysiwyg_editor_to_category($term) {
    $term_id = $term->term_id;
    $content = get_term_meta($term_id, 'wccc_custom_wysiwyg_content', true);

    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="wccc_custom_wysiwyg_content">Text Block Under Products</label></th>
        <td>
            <?php
            wp_editor(
                htmlspecialchars_decode($content),
                'wccc_custom_wysiwyg_content',
                [
                    'textarea_name' => 'wccc_custom_wysiwyg_content',
                    'textarea_rows' => 10,
                    'media_buttons' => true,
                ]
            );
            ?>
            <p class="description">Detailed category info to appear below the product list.</p>
        </td>
    </tr>
    <?php
}
add_action('product_cat_edit_form_fields', 'wccc_add_wysiwyg_editor_to_category', 10, 1);

// Save the WYSIWYG content
function wccc_save_wysiwyg_editor_content($term_id) {
    if (isset($_POST['wccc_custom_wysiwyg_content'])) {
        update_term_meta(
            $term_id,
            'wccc_custom_wysiwyg_content',
            wp_kses_post($_POST['wccc_custom_wysiwyg_content'])
        );
    }
}
add_action('edited_product_cat', 'wccc_save_wysiwyg_editor_content');

// Show the content after the product list on category page
function wccc_display_wysiwyg_content_on_category_page() {
    if (is_product_category()) {
        $term = get_queried_object();
        $content = get_term_meta($term->term_id, 'wccc_custom_wysiwyg_content', true);

        if (!empty($content)) {
            echo '<div class="wccc-category-extra-content" style="margin-top:40px;padding-top:40px;border-top:1px solid #ccc;">';
            echo do_shortcode(wpautop($content));
            echo '</div>';
        }
    }
}
add_action('woocommerce_after_main_content', 'wccc_display_wysiwyg_content_on_category_page', 20);
