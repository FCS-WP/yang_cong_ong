<?php
remove_action('flatsome_product_box_after', 'woocommerce_template_loop_add_to_cart');

add_action('flatsome_product_box_after', 'custom_flatsome_quantity');

function custom_flatsome_quantity()
{

    global $product;

    if (! $product || ! $product->is_type('simple')) {
        return;
    }

    echo '<form class="cart" method="post">';

    woocommerce_quantity_input(array(
        'min_value'   => 1,
        'input_value' => 1,
    ));

    echo '<button type="submit"
            name="add-to-cart"
            value="' . esc_attr($product->get_id()) . '"
            class="button alt">
            Add to Cart
          </button>';

    echo '</form>';
}


add_shortcode('wc_page_breadcrumb', function ($atts) {

    if (function_exists('woocommerce_breadcrumb') && function_exists('is_woocommerce')) {
        if (is_woocommerce() || is_cart() || is_checkout() || is_account_page()) {
            ob_start();
            woocommerce_breadcrumb([
                'wrap_before' => '<nav class="woocommerce-breadcrumb" aria-label="Breadcrumb">',
                'wrap_after'  => '</nav>',
                'delimiter'   => '&nbsp;&#47;&nbsp;',
            ]);
            return ob_get_clean();
        }
    }

    if (is_page()) {
        global $post;

        $delimiter = '&nbsp;&#47;&nbsp;';
        $home_text = 'Home';

        $crumbs = [];
        $crumbs[] = '<a href="' . esc_url(home_url('/')) . '">' . esc_html($home_text) . '</a>';

        $parents = [];
        $parent_id = (int) $post->post_parent;

        while ($parent_id) {
            $p = get_post($parent_id);
            if (! $p) break;
            $parents[] = $p;
            $parent_id = (int) $p->post_parent;
        }

        $parents = array_reverse($parents);

        foreach ($parents as $p) {
            $crumbs[] = '<a href="' . esc_url(get_permalink($p->ID)) . '">' . esc_html(get_the_title($p->ID)) . '</a>';
        }

        $crumbs[] = '<span>' . esc_html(get_the_title($post->ID)) . '</span>';

        return '<nav class="woocommerce-breadcrumb" aria-label="Breadcrumb">' . implode($delimiter, $crumbs) . '</nav>';
    }

    return '';
});
