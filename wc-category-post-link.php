<?php
/*
Plugin Name: Woo Category Post Selector
Description: Adds a post selector to WooCommerce categories and displays the selected post after product listings.
Version: 1.0
Author: Your Name
*/

if (!defined('ABSPATH')) exit;

// Add custom field to WooCommerce category (Admin form)
function wccps_add_category_field($term) {
    $term_id = $term->term_id;
    $selected_post = get_term_meta($term_id, 'wccps_selected_post', true);
    $posts = get_posts(['post_type' => 'post', 'numberposts' => -1]);

    echo '<tr class="form-field">
        <th scope="row" valign="top"><label for="wccps_selected_post">Select Post to Show</label></th>
        <td>
            <select name="wccps_selected_post" id="wccps_selected_post">
                <option value="">-- None --</option>';
                foreach ($posts as $post) {
                    $selected = selected($selected_post, $post->ID, false);
                    echo "<option value='{$post->ID}' {$selected}>{$post->post_title}</option>";
                }
            echo '</select>
            <p class="description">This post will appear below product listings in this category.</p>
        </td>
    </tr>';
}
add_action('product_cat_edit_form_fields', 'wccps_add_category_field');

// Save the custom field value
function wccps_save_category_meta($term_id) {
    if (isset($_POST['wccps_selected_post'])) {
        update_term_meta($term_id, 'wccps_selected_post', sanitize_text_field($_POST['wccps_selected_post']));
    }
}
add_action('edited_product_cat', 'wccps_save_category_meta');

// Show the selected post content on the category page
function wccps_display_selected_post_after_products() {
    if (is_product_category()) {
        $term = get_queried_object();
        $selected_post_id = get_term_meta($term->term_id, 'wccps_selected_post', true);

        if ($selected_post_id) {
            $post = get_post($selected_post_id);
            if ($post && $post->post_status === 'publish') {
                echo '<div class="wccps-category-post" style="margin-top: 40px; padding-top: 40px; border-top: 1px solid #ccc;">';
                echo '<h2>' . esc_html($post->post_title) . '</h2>';
                echo apply_filters('the_content', $post->post_content);
                echo '</div>';
            }
        }
    }
}
add_action('woocommerce_after_main_content', 'wccps_display_selected_post_after_products', 20);
