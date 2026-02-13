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
