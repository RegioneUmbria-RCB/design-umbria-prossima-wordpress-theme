<?php 
function custom_register_post_types() {
    $post_types = get_custom_post_type();

    foreach ($post_types as $slug => $label) {
        register_post_type($slug, [
            'labels' => [
                'name' => $label,
                'singular_name' => $label,
                'add_new_item' => 'Aggiungi ' . strtolower($label),
                'edit_item' => 'Modifica ' . strtolower($label),
                'new_item' => 'Nuovo ' . strtolower($label),
                'view_item' => 'Visualizza ' . strtolower($label),
                'search_items' => 'Cerca ' . strtolower($label),
                'not_found' => 'Nessun elemento trovato',
                'not_found_in_trash' => 'Nessun elemento nel cestino',
            ],
            'public' => true,
            'has_archive' => false,
            'rewrite' => ['slug' => $slug],
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
            'menu_position' => 20,
            'menu_icon' => 'dashicons-admin-post',
            'show_in_rest' => true,
        ]);
    }
}
add_action('init', 'custom_register_post_types');

add_filter('register_post_type_args', function($args, $post_type) {
    if (!in_array($post_type, ['post', 'page']) && isset($args['capability_type'])) {
        unset($args['capability_type']);
    }
    return $args;
}, 10, 2);


add_action('after_setup_theme', function () {
    $post_types = get_custom_post_type();
    add_theme_support('post-thumbnails', array_keys($post_types));
});

function add_shared_global_fields() {
    $post_types = get_post_types( array(
        'public' => true,
    ), 'names' );

    //ESCLUSIONE
    $post_types = array_diff( $post_types, array( 'sito_tematico' ) );

    $cmb = new_cmb2_box( array(
        'id'            => 'link_esterno_metabox',
        'title'         => __( 'Link Esterno', 'textdomain' ),
        'object_types'  => $post_types,
        'context'       => 'normal',
        'priority'      => 'high',
        'show_names'    => true,
    ) );

    $cmb->add_field( array(
        'name'       => __( 'Link Esterno', 'textdomain' ),
        'desc'       => __( 'Inserisci un link esterno da mostrare al click al posto del contenuto', 'textdomain' ),
        'id'         => 'external_link',
        'type'       => 'text_url',
        'protocols'  => array( 'http', 'https' ),
    ) );
}

add_action( 'cmb2_admin_init', 'add_shared_global_fields' );
