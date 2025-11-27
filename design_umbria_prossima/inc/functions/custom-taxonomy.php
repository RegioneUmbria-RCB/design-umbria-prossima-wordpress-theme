<?php
add_action('init', function () {
    $taxonomies = get_custom_toxonomy();

    foreach ($taxonomies as $name) {
        $slug = sanitize_title($name);

        $labels = array(
            'name'              => _x($name, 'taxonomy general name', 'design_comuni_italia'),
            'singular_name'     => _x($name, 'taxonomy singular name', 'design_comuni_italia'),
            'search_items'      => __('Cerca ' . $name, 'design_comuni_italia'),
            'all_items'         => __('Tutti i ' . $name, 'design_comuni_italia'),
            'edit_item'         => __('Modifica ' . $name, 'design_comuni_italia'),
            'update_item'       => __('Aggiorna ' . $name, 'design_comuni_italia'),
            'add_new_item'      => __('Aggiungi ' . $name, 'design_comuni_italia'),
            'new_item_name'     => __('Nuovo ' . $name, 'design_comuni_italia'),
            'menu_name'         => __($name, 'design_comuni_italia'),
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'has_archive'       => false,
            'show_in_rest'      => true,
            'capabilities'      => array(
                'manage_terms'  => 'manage_categories',
                'edit_terms'    => 'manage_categories',
                'delete_terms'  => 'manage_categories',
                'assign_terms'  => 'edit_posts'
            ),
        );

        $all_post_types = get_post_types(['public' => true]);
        $all_post_types = array_diff($all_post_types, ['post', 'page', 'attachment']);
        
        register_taxonomy($slug, $all_post_types, $args);
    }
});


add_action('cmb2_admin_init', function() {
    $taxonomies = get_taxonomies(['public' => true], 'names');
    
    foreach ($taxonomies as $taxonomy) {
        $cmb_term = new_cmb2_box([
            'id'               => 'term_extra_metabox_' . $taxonomy,
            'title'            => __('Extra Info', 'textdomain'),
            'object_types'     => ['term'],
            'taxonomies'       => [$taxonomy],
            'new_term_section' => true,
        ]);

        if($taxonomy !== "argomenti"){ 
            $cmb_term->add_field( array(
                'name' => __( 'Immagine', 'design_comuni_italia' ),
                'desc' => __( 'Immagine principale della tassonomia', 'design_comuni_italia' ),
                'id'   => 'dci_term_immagine',
                'type' => 'file',
                'query_args' => array( 'type' => 'image' ), // Only images attachment
            ) );
        }

        $cmb_term->add_field([
            'name'    => __('Icona', 'textdomain'),
            'desc'    => __('Carica un’icona per questa tassonomia', 'textdomain'),
            'id'      => 'icon',
            'type'    => 'file',
            'options' => [
                'url' => false,
            ],
        ]);

        $cmb_term->add_field([
            'name'      => __('Link Esterno', 'textdomain'),
            'desc'      => __('Inserisci un link esterno da mostrare al click al posto del contenuto', 'textdomain'),
            'id'        => 'external_link',
            'type'      => 'text_url',
            'protocols' => ['http', 'https'],
        ]);
    }
});


