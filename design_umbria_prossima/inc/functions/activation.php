<?php
add_action('after_switch_theme', 'importa_strutture_dati_tema');
add_action('after_switch_theme', 'createCapabilities');
add_action('after_switch_theme', 'mytheme_set_custom_permalink_structure');
add_action('cmb2_init', 'dci_register_main_options_metabox' );

function mytheme_set_custom_permalink_structure() {
    global $wp_rewrite;

    $wp_rewrite->set_permalink_structure('/%category%/%postname%/');
    $wp_rewrite->flush_rules(); // Ricostruisce le regole dei permalink
}

function comuni_crea_categorie_da_pagine() {
    if (!defined('COMUNI_PAGINE')) {
        return;
    }

    foreach (COMUNI_PAGINE as $pagina) {
        comuni_crea_categoria($pagina);
    }
}

function comuni_crea_categoria($pagina, $parent_id = 0) {
    $term = term_exists($pagina['slug'], 'category');

    if (!$term) {
        $term = wp_insert_term(
            $pagina['title'],
            'category',
            [
                'slug' => $pagina['slug'],
                'description' => $pagina['description'],
                'parent' => $parent_id
            ]
        );
    }
    if (is_wp_error($term)) {
        return;
    }

    $term_id = is_array($term) ? $term['term_id'] : $term;
    if (!empty($pagina['children'])) {
        foreach ($pagina['children'] as $child) {
            comuni_crea_categoria($child, $term_id);
        }
    }
}
add_action('after_switch_theme', 'comuni_crea_categorie_da_pagine');