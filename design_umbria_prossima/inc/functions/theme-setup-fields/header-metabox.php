<?php
add_action('cmb2_admin_init', 'header_metabox');
function header_metabox() {
    $cmb = new_cmb2_box( array(
        'id'            => 'header_metabox',
        'title'         => __('Header Settings', 'tuo-text-domain'),
        'object_types' => array('options-page'),
        'option_key'   => 'header_metabox',
        'context'       => 'normal',
        'priority'      => 'high',
    ) );

     // Mostra canali social (checkbox)
    $cmb->add_field( array(
        'name'    => __('Mostra canali social', 'tuo-text-domain'),
        'desc'    => __('Attiva per mostrare i canali social nell\'header.', 'tuo-text-domain'),
        'id'      => 'show_social',
        'type'    => 'checkbox',
    ) );
    $cmb->add_field( array(
        'name'    => __('Mostra ente di appartenenza', 'tuo-text-domain'),
        'desc'    => __('Attiva per mostrare l\'ente di appartenenza nell\'header.', 'tuo-text-domain'),
        'id'      => 'show_ente',
        'type'    => 'checkbox',
    ) );
     $cmb->add_field( array(
        'name' => __('Nome Ente', 'tuo-text-domain'),
        'id'   => 'ente',
        'type' => 'text',
    ) );

    // Link Ente
    $cmb->add_field( array(
        'name' => __('Link Ente', 'tuo-text-domain'),
        'id'   => 'link_ente',
        'type' => 'text_url',
    ) );

    // Menu principale (repeatable group con link, label e icona)
    $group_id = $cmb->add_field( array(
        'id'          => 'primary_menu',
        'type'        => 'group',
        'description' => __('MENU PRINCIPALE', 'tuo-text-domain'),
        'options'     => array(
            'group_title'   => __('Voce {#}', 'tuo-text-domain'),
            'add_button'    => __('Aggiungi voce', 'tuo-text-domain'),
            'remove_button' => __('Rimuovi voce', 'tuo-text-domain'),
            'sortable'      => true,
        ),
    ) );

    // Campi del gruppo Menu principale
    $cmb->add_group_field( $group_id, array(
        'name' => __('Label', 'tuo-text-domain'),
        'id'   => 'label',
        'type' => 'text',
    ) );

    $cmb->add_group_field( $group_id, array(
        'name' => __('Link', 'tuo-text-domain'),
        'id'   => 'link',
        'type' => 'text_url',
    ) );

    $cmb->add_group_field( $group_id, array(
        'name'         => __('Icona', 'tuo-text-domain'),
        'id'           => 'icon',
        'type'         => 'file',
        'options'      => array(
            'url' => false,
        ),
        'text'         => array(
            'add_upload_file_text' => __('Carica / Seleziona Icona', 'tuo-text-domain')
        ),
        'query_args'   => array(
            'type' => array(
                'image/gif',
                'image/jpeg',
                'image/png',
            ),
        ),
        'preview_size' => 'thumbnail',
    ) );

    // Menu secondario (stesso gruppo del menu principale, con icona)
    $group_id2 = $cmb->add_field( array(
        'id'          => 'secondary_menu',
        'type'        => 'group',
        'description' => __('MENU SECONDARIO', 'tuo-text-domain'),
        'options'     => array(
            'group_title'   => __('Voce {#}', 'tuo-text-domain'),
            'add_button'    => __('Aggiungi voce', 'tuo-text-domain'),
            'remove_button' => __('Rimuovi voce', 'tuo-text-domain'),
            'sortable'      => true,
        ),
    ) );

    $cmb->add_group_field( $group_id2, array(
        'name' => __('Label', 'tuo-text-domain'),
        'id'   => 'label',
        'type' => 'text',
    ) );

    $cmb->add_group_field( $group_id2, array(
        'name' => __('Link', 'tuo-text-domain'),
        'id'   => 'link',
        'type' => 'text_url',
    ) );

    $cmb->add_group_field( $group_id2, array(
        'name'         => __('Icona', 'tuo-text-domain'),
        'id'           => 'icon',
        'type'         => 'file',
        'options'      => array(
            'url' => false,
        ),
        'text'         => array(
            'add_upload_file_text' => __('Carica / Seleziona Icona', 'tuo-text-domain')
        ),
        'query_args'   => array(
            'type' => array(
                'image/gif',
                'image/jpeg',
                'image/png',
            ),
        ),
        'preview_size' => 'thumbnail',
    ) );
}
