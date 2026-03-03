<?php

add_shortcode('post_title', function () {
    return get_the_title();
});

add_shortcode('post_featured_image', function ($atts) {
    $atts = shortcode_atts([
        'size'  => 'full',
        'class' => 'post-featured-image'
    ], $atts);

    if (has_post_thumbnail()) {
        return get_the_post_thumbnail(
            null,
            $atts['size'],
            ['class' => $atts['class']]
        );
    }
    return '';
});


add_shortcode('post_content', function () {
    return apply_filters('the_content', get_the_content());
});

add_shortcode('post_excerpt', function () {
    return get_the_excerpt();
});

add_shortcode('post_date', function ($atts) {
    $atts = shortcode_atts([
        'format' => 'd/m/Y'
    ], $atts);

    return get_the_date($atts['format']);
});

add_shortcode('post_author', function () {
    return get_the_author();
});

add_shortcode('post_terms', function ($atts) {
    $atts = shortcode_atts([
        'taxonomy' => 'category',
        'separator' => ', '
    ], $atts);

    return get_the_term_list(
        get_the_ID(),
        $atts['taxonomy'],
        '',
        $atts['separator']
    );
});

add_shortcode('post_meta', function ($atts) {
    $atts = shortcode_atts([
        'key' => '',
    ], $atts);

    if (!$atts['key']) return '';

    return get_post_meta(get_the_ID(), $atts['key'], true);
});

add_shortcode('post_gallery', function () {
    return get_post_gallery();
});

add_shortcode('post_gallery_image', function ($atts) {
    $atts = shortcode_atts([
        'key'   => '',
        'index' => 0,
        'size'  => 'large',
        'class' => ''
    ], $atts);

    if (!$atts['key']) return '';

    $gallery = get_field($atts['key']);
    if (!$gallery || !isset($gallery[$atts['index']])) return '';

    return wp_get_attachment_image(
        $gallery[$atts['index']]['ID'],
        $atts['size'],
        false,
        ['class' => $atts['class']]
    );
});

// Contact Form Shortcode
function contact_form_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'title' => 'Contact Us',
        'submit_text' => 'Send Message',
    ), $atts);

    ob_start();
?>
    <div class="contact-form-wrapper">
        <?php if (!empty($atts['title'])) : ?>
            <h2 class="contact-form-title"><?php echo esc_html($atts['title']); ?></h2>
        <?php endif; ?>

        <form class="contact-form" id="contact-form" method="post" action="">
            <?php wp_nonce_field('contact_form_action', 'contact_form_nonce'); ?>

            <div class="form-row">
                <div class="form-group">
                    <label for="contact_name">Name <span class="required">*</span></label>
                    <input type="text" id="contact_name" name="contact_name" required placeholder="Your Name">
                </div>

                <div class="form-group">
                    <label for="contact_email">Email <span class="required">*</span></label>
                    <input type="email" id="contact_email" name="contact_email" required placeholder="your.email@example.com">
                </div>
            </div>
            
            <div class="form-group">
                <label for="contact_number">Contact Number <span class="required">*</span></label>
                <input type="text" id="contact_number" name="contact_number" required placeholder="Contact Number..">
            </div>
            <div class="form-group">
                <label for="contact_message">Message <span class="required">*</span></label>
                <textarea id="contact_message" name="contact_message" rows="6" required placeholder="Your message here..."></textarea>
            </div>

            <div class="form-submit ">
                <button type="submit" class="btn-primary submit-button">
                    <?php echo esc_html($atts['submit_text']); ?>
                </button>
            </div>

            <div class="form-response" style="display: none;"></div>
        </form>
    </div>
<?php
    return ob_get_clean();
}
add_shortcode('contact_form', 'contact_form_shortcode');

// Add-Ons Package Shortcode
function addon_packages_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'title' => 'Our Add-Ons',
        'detail_title' => 'Add-On Information',
    ), $atts);

    // Get packages from 'add-ons' category
    $addon_cat = get_term_by('slug', 'add-ons', 'package_category');

    if (!$addon_cat) {
        return '<p>Add-ons category not found.</p>';
    }

    $args = array(
        'post_type' => 'package',
        'posts_per_page' => -1,
        'orderby' => 'menu_order',
        'order' => 'ASC',
        'tax_query' => array(
            array(
                'taxonomy' => 'package_category',
                'field' => 'term_id',
                'terms' => $addon_cat->term_id,
            ),
        ),
    );

    $addons = new WP_Query($args);

    if (!$addons->have_posts()) {
        return '<p>No add-ons found.</p>';
    }

    ob_start();
?>
    <div class="addon-packages-wrapper">
        <!-- Left: Add-ons List -->
        <div class="addon-list-section">
            <h2 class="addon-section-title"><?php echo esc_html($atts['title']); ?></h2>
            <div class="addon-list">
                <?php
                $first = true;
                while ($addons->have_posts()) : $addons->the_post();
                    $price = get_field('package_price');
                ?>
                    <div class="addon-item <?php echo $first ? 'active' : ''; ?>" data-addon-id="<?php echo get_the_ID(); ?>">
                        <div class="addon-item-content">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="addon-thumb">
                                    <?php the_post_thumbnail('thumbnail'); ?>
                                </div>
                            <?php endif; ?>
                            <div class="addon-item-info">
                                <h3 class="addon-item-title"><?php the_title(); ?></h3>
                                <?php if ($price) : ?>
                                    <span class="addon-item-price">$<?php echo esc_html($price); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <svg class="addon-arrow" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M7.5 5L12.5 10L7.5 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                <?php
                    $first = false;
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        </div>

        <!-- Right: Add-on Details -->
        <div class="addon-detail-section">
            <h2 class="addon-section-title"><?php echo esc_html($atts['detail_title']); ?></h2>
            <div class="addon-detail-wrapper">
                <?php
                $addons->rewind_posts();
                $first = true;
                while ($addons->have_posts()) : $addons->the_post();
                    $price = get_field('package_price');
                ?>
                    <div class="addon-detail <?php echo $first ? 'active' : ''; ?>" data-addon-id="<?php echo get_the_ID(); ?>">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="addon-detail-image">
                                <?php the_post_thumbnail('large'); ?>
                            </div>
                        <?php endif; ?>

                        <div class="addon-detail-content">
                            <h3 class="addon-detail-title"><?php the_title(); ?></h3>

                            <?php if ($price) : ?>
                                <div class="addon-detail-price">
                                    <span class="price-value">$<?php echo esc_html($price); ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="addon-description">
                                <?php the_content(); ?>
                            </div>
                        </div>
                    </div>
                <?php
                    $first = false;
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        </div>
    </div>
<?php
    return ob_get_clean();
}
add_shortcode('addon_packages', 'addon_packages_shortcode');

// FAQ Accordion Shortcode
function faq_accordion_shortcode($atts, $content = null)
{
    $atts = shortcode_atts(array(
        'title' => 'Frequently Asked Questions',
    ), $atts);

    ob_start();
?>
    <div class="faq-accordion-wrapper">
        <?php if (!empty($atts['title'])) : ?>
            <h2 class="faq-title"><?php echo esc_html($atts['title']); ?></h2>
        <?php endif; ?>

        <div class="faq-accordion">
            <?php echo do_shortcode($content); ?>
        </div>
    </div>
<?php
    return ob_get_clean();
}
add_shortcode('faq_accordion', 'faq_accordion_shortcode');

// FAQ Item Shortcode
function faq_item_shortcode($atts, $content = null)
{
    $atts = shortcode_atts(array(
        'question' => '',
    ), $atts);

    if (empty($atts['question'])) {
        return '';
    }

    ob_start();
?>
    <div class="faq-item">
        <button class="faq-question" type="button">
            <span class="question-text"><?php echo esc_html($atts['question']); ?></span>
            <svg class="faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path class="icon-plus" d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                <path class="icon-minus" d="M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
        </button>
        <div class="faq-answer">
            <div class="faq-answer-content">
                <?php echo do_shortcode($content); ?>
            </div>
        </div>
    </div>
<?php
    return ob_get_clean();
}
add_shortcode('faq_item', 'faq_item_shortcode');
