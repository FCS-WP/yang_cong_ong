<?php

// Handle contact form submission
function handle_contact_form_submission()
{
    if (!isset($_POST['contact_form_nonce']) || !wp_verify_nonce($_POST['contact_form_nonce'], 'contact_form_action')) {
        wp_send_json_error(['message' => 'Security check failed']);
    }

    $name = sanitize_text_field($_POST['contact_name']);
    $email = sanitize_email($_POST['contact_email']);
    $number = sanitize_text_field($_POST['contact_number']);
    $message = sanitize_textarea_field($_POST['contact_message']);

    // Validate
    if (empty($name) || empty($email) || empty($number) || empty($message)) {
        wp_send_json_error(['message' => 'All fields are required']);
    }

    if (!is_email($email)) {
        wp_send_json_error(['message' => 'Invalid email address']);
    }

    // Send email
    $to = get_option('admin_email');
    $headers = array('Content-Type: text/html; charset=UTF-8', 'From: ' . $name . ' <' . $email . '>');
    $email_number = 'Contact Form: ' . $number;
    $email_message = '<p><strong>Name:</strong> ' . $name . '</p>';
    $email_message .= '<p><strong>Email:</strong> ' . $email . '</p>';
    $email_message .= '<p><strong>number:</strong> ' . $number . '</p>';
    $email_message .= '<p><strong>Message:</strong></p><p>' . nl2br($message) . '</p>';

    $sent = wp_mail($to, $email_number, $email_message, $headers);
    if ($sent) {
        wp_send_json_success(['message' => 'Thank you! Your message has been sent successfully.']);
    } else {
        wp_send_json_error(['message' => 'Sorry, there was an error sending your message. Please try again.']);
    }
}

add_action('wp_ajax_contact_form_submit', 'handle_contact_form_submission');
add_action('wp_ajax_nopriv_contact_form_submit', 'handle_contact_form_submission');
