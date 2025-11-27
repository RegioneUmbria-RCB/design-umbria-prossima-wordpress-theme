<?php
add_action('cmb2_admin_init', 'permissions_metabox');
function permissions_metabox() {
    $cmb = new_cmb2_box(array(
        'id'           => 'permissions_metabox',
        'title'        => 'Gestione permessi globali',
        'object_types' => array('options-page'),
        'option_key'   => 'permissions_metabox',
        'context'      => 'normal',
        'priority'     => 'high',
    ));

    // Repeater (group field)
    $group_field_id = $cmb->add_field(array(
        'id'          => 'permissions_group',
        'type'        => 'group',
        'description' => 'Associazioni tra tipo di post e ruolo utente',
        'options'     => array(
            'group_title'   => 'Associazione {#}',
            'add_button'    => 'Aggiungi Associazione',
            'remove_button' => 'Rimuovi',
            'sortable'      => true,
        ),
    ));

    // Tipo di post (checkbox multiple inline)
    $cmb->add_group_field($group_field_id, array(
        'name'       => 'Tipo di post',
        'id'         => 'post_type',
        'type'       => 'multicheck_inline',
        'options_cb' => 'cmb2_get_post_types_for_permissions',
    ));

    // Ruolo utente (checkbox multiple)
    $cmb->add_group_field($group_field_id, array(
        'name'       => 'Ruolo utente',
        'id'         => 'user_role',
        'type'       => 'multicheck',
        'options_cb' => 'cmb2_get_user_roles_for_permissions',
    ));
}

// Helper: CPT pubblici
function cmb2_get_post_types_for_permissions() {
    $post_types = get_post_types(array('public' => true), 'objects');
    $options = [];
    foreach ($post_types as $pt) {
        $options[$pt->name] = $pt->labels->singular_name;
    }
    return $options;
}

// Helper: ruoli utente di WordPress
function cmb2_get_user_roles_for_permissions() {
    global $wp_roles;
    $options = [];
    foreach ($wp_roles->roles as $role => $details) {
        //if ($role === 'administrator') continue; // esclude admin
        $options[$role] = $details['name'];
    }
    return $options;
}
