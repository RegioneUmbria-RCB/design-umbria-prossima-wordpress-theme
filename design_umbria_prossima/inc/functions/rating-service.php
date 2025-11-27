<?php

add_action('init', function() {
    register_post_type('feedback', [
        'label' => 'Feedback utenti',
        'public' => false,
        'show_ui' => true,
        'menu_icon' => 'dashicons-star-filled',
        'supports' => ['title'],
    ]);
});


add_filter('manage_feedback_posts_columns', function($columns) {
    $columns['rating']   = 'Rating';
    $columns['option']   = 'Opzione';
    $columns['details']  = 'Dettagli';
    $columns['page_url'] = 'URL pagina';
    return $columns;
});

add_action('manage_feedback_posts_custom_column', function($column, $post_id) {
    switch ($column) {
        case 'rating':
            echo get_post_meta($post_id, 'feedback_rating', true);
            break;
        case 'option':
            echo get_post_meta($post_id, 'feedback_option', true);
            break;
        case 'details':
            echo get_post_meta($post_id, 'feedback_details', true);
            break;
        case 'page_url':
            $url = get_post_meta($post_id, 'feedback_page_url', true);
            echo $url ? '<a href="'.esc_url($url).'" target="_blank">Link</a>' : '';
            break;
    }
}, 10, 2);

add_action('cmb2_admin_init', function() {
    $prefix = 'feedback_';

    $cmb = new_cmb2_box([
        'id'           => $prefix . 'metabox',
        'title'        => 'Dati Feedback',
        'object_types' => ['feedback'],
        'context'      => 'normal',
        'priority'     => 'high',
        'show_names'   => true,
    ]);

    $cmb->add_field([
        'name' => 'Rating',
        'id'   => $prefix . 'rating',
        'type' => 'text_small',
        'attributes' => [
            'type' => 'number',
            'min'  => 0,
            'max'  => 5,
        ],
    ]);

    $cmb->add_field([
        'name' => 'Opzione',
        'id'   => $prefix . 'option',
        'type' => 'text',
    ]);

    $cmb->add_field([
        'name' => 'Dettagli',
        'id'   => $prefix . 'details',
        'type' => 'textarea_small',
    ]);

    $cmb->add_field([
        'name' => 'URL pagina',
        'id'   => $prefix . 'page_url',
        'type' => 'text_url',
    ]);
});

add_action('wp_ajax_nopriv_save_feedback', 'save_feedback');
add_action('wp_ajax_save_feedback', 'save_feedback');

function save_feedback() {
    $rating  = intval($_POST['rating']);
    $option  = sanitize_text_field($_POST['option']);
    $details = sanitize_text_field($_POST['details']);
    $url     = esc_url_raw($_POST['page_url']);

    $post_id = wp_insert_post([
        'post_type'   => 'feedback',
        'post_title'  => 'Feedback del ' . current_time('d/m/Y H:i'),
        'post_status' => 'publish',
    ]);

    if ($post_id) {
        update_post_meta($post_id, 'feedback_rating', $rating);
        update_post_meta($post_id, 'feedback_option', $option);
        update_post_meta($post_id, 'feedback_details', $details);
        update_post_meta($post_id, 'feedback_page_url', $url);
    }

    wp_send_json_success("Feedback salvato");
}

add_shortcode('show_feedbacks', function() {
    $feedbacks = get_posts([
        'post_type' => 'feedback',
        'numberposts' => -1,
        'post_status' => 'publish',
    ]);

    if(!$feedbacks) return '<p>Nessun feedback ancora salvato.</p>';

    $html = '<table class="feedback-table" style="width:100%;border-collapse:collapse;">';
    $html .= '<thead><tr>';
    $html .= '<th style="border:1px solid #ccc;padding:5px;">Rating</th>';
    $html .= '<th style="border:1px solid #ccc;padding:5px;">Opzione</th>';
    $html .= '<th style="border:1px solid #ccc;padding:5px;">Dettagli</th>';
    $html .= '<th style="border:1px solid #ccc;padding:5px;">Pagina</th>';
    $html .= '</tr></thead><tbody>';

    foreach($feedbacks as $fb) {
        $rating  = get_post_meta($fb->ID, 'feedback_rating', true);
        $option  = get_post_meta($fb->ID, 'feedback_option', true);
        $details = get_post_meta($fb->ID, 'feedback_details', true);
        $url     = get_post_meta($fb->ID, 'feedback_page_url', true);

        $html .= '<tr>';
        $html .= '<td style="border:1px solid #ccc;padding:5px;">'.esc_html($rating).'</td>';
        $html .= '<td style="border:1px solid #ccc;padding:5px;">'.esc_html($option).'</td>';
        $html .= '<td style="border:1px solid #ccc;padding:5px;">'.esc_html($details).'</td>';
        $html .= '<td style="border:1px solid #ccc;padding:5px;"><a href="'.esc_url($url).'" target="_blank">Link</a></td>';
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';

    return $html;
});
?>
