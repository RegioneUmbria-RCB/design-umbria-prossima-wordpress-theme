<?php
add_action('cmb2_admin_init', 'homepage_metabox');

function homepage_metabox() {

    $cmb = new_cmb2_box(array(
        'id'           => 'homepage_metabox',
        'title'        => 'Impostazioni Sezioni Homepage',
        'object_types' => array('options-page'),
        'option_key'   => 'homepage_metabox',
        'context'      => 'normal',
        'priority'     => 'high',
    ));

    $cmb->add_field( array(
        'name' => __('Attiva Alert Homepage', 'tuo-text-domain'),
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

    $cmb->add_field(array(
        'name'    => 'Attiva ricerca',
        'id'      => 'search_active',
        'type'    => 'checkbox',
        'desc'    => 'Spunta per attivare la funzionalità di ricerca.',
    ));

    $group_field_id = $cmb->add_field(array(
        'id'          => 'quick_links_group',
        'description' => 'Aggiungi dei bottoni rapidi nella ricerca',
        'type'        => 'group',
        'options'     => array(
            'group_title'   => 'Link rapido ricerca {#}',
            'add_button'    => 'Aggiungi link rapido',
            'remove_button' => 'Rimuovi link rapido',
            'sortable'      => true,
        ),
    ));

    $cmb->add_group_field($group_field_id, array(
        'name' => 'URL',
        'id'   => 'url',
        'type' => 'text_url',
    ));

    $cmb->add_group_field($group_field_id, array(
        'name' => 'Label',
        'id'   => 'label',
        'type' => 'text',
    ));

    $images_group_id = $cmb->add_field(array(
        'id'          => 'homepage_images_group',
        'description' => 'Aggiungi le immagini di sfondo nella ricerca',
        'type'        => 'group',
        'options'     => array(
            'group_title'   => 'Immagine {#}',
            'add_button'    => 'Aggiungi immagine hero',
            'remove_button' => 'Rimuovi immagine',
            'sortable'      => true,
        ),
    ));

    $cmb->add_group_field($images_group_id, array(
        'name' => 'Immagine',
        'id'   => 'image',
        'type' => 'file', // oppure 'file_list' se vuoi più immagini
        'options' => array(
            'url' => false, // nasconde input URL, usa solo upload
        ),
        'text' => array(
            'add_upload_file_text' => 'Carica immagine',
        ),
    ));

    $cmb->add_group_field($images_group_id, array(
        'name' => 'Titolo immagine',
        'id'   => 'title',
        'type' => 'text',
    ));
    $cmb->add_group_field($images_group_id, array(
        'name' => 'Link titolo',
        'id'   => 'link',
        'type' => 'text',
    ));


    require_once get_template_directory() . '/inc/functions/theme-setup-fields/components/post-list.php';
    add_post_list_metabox_fields($cmb);
}
