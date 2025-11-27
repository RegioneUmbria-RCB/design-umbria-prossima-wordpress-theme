<?php
/**
 * Impostazioni tema con CMB2
 */

add_action('cmb2_admin_init', 'theme_metabox');
function theme_metabox() {
    $cmb = new_cmb2_box( array(
        'id'            => 'theme_metabox',
        'title'         => __('Theme Settings', 'tuo-text-domain'),
        'object_types'  => array('options-page'),
        'option_key'    => 'theme_metabox',
        'context'       => 'normal',
        'priority'      => 'high',
    ) );

    // Logo
    $cmb->add_field( array(
        'name' => 'Logo del sito',
        'id'   => 'logo',
        'type' => 'file',
        'query_args'   => array(
            'type' => array('image/jpeg','image/png','image/gif','image/svg+xml'),
        ),
        'preview_size' => 'medium',
    ) );

    $cmb->add_field( array(
        'name' => __('Nome sito', 'tuo-text-domain'),
        'id'   => 'site_name',
        'type' => 'text',
    ) );

    $cmb->add_field( array(
        'name' => __('Sottotitolo', 'tuo-text-domain'),
        'id'   => 'subtitle',
        'type' => 'text',
    ) );

    // Palette Colori
    $cmb->add_field( array(
        'name'    => 'Colore del tema',
        'id'      => 'color',
        'type'    => 'select',
        'options' => array(
            'blue'         => 'Blu',
            'red'          => 'Rosso',
            'orange'       => 'Arancione',
            'yellow'       => 'Giallo',
            'green'        => 'Verde',
            'violet'       => 'Viola',
            'brown'        => 'Marrone',
            'dark-gray'    => 'Grigio scuro',
            'turquoise'    => 'Turchese',
        ),
        'default' => 'green',
    ) );

    // Canali Social
    $cmb->add_field( array(
        'name' => 'Facebook',
        'id'   => 'social_facebook',
        'type' => 'text_url',
    ) );

    $cmb->add_field( array(
        'name' => 'Twitter/X',
        'id'   => 'social_twitter',
        'type' => 'text_url',
    ) );

    $cmb->add_field( array(
        'name' => 'Instagram',
        'id'   => 'social_instagram',
        'type' => 'text_url',
    ) );

    $cmb->add_field( array(
        'name' => 'YouTube',
        'id'   => 'social_youtube',
        'type' => 'text_url',
    ) );

    $cmb->add_field( array(
        'name' => 'LinkedIn',
        'id'   => 'social_linkedin',
        'type' => 'text_url',
    ) );

    $cmb->add_field( array(
        'name' => __('Attiva Alert Globale', 'tuo-text-domain'),
        'id'   => 'alert_enable',
        'type' => 'checkbox',
    ) );

    $cmb->add_field( array(
        'name'    => __('Tipo di Alert', 'tuo-text-domain'),
        'id'      => 'alert_type',
        'type'    => 'select',
        'options' => array(
            'success' => 'Conferma',
            'danger'  => 'Pericolo',
            'warning' => 'Avviso',
            'info'    => 'Informazione',
        ),
    ) );

    $cmb->add_field( array(
        'name' => __('Messaggio Alert', 'tuo-text-domain'),
        'id'   => 'alert_message',
        'type' => 'textarea',
        'options' => array(
            'media_buttons' => true,
            'textarea_rows' => 5,
        ),
    ) );
}
