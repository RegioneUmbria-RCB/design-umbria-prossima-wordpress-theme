<?php
/**
 * Metabox Post List – versione corretta + card type per tassonomie
 */

function get_hierarchical_term_options( $terms, &$options, $parent = 0, $indent = '', $prefix = '' ) {
	foreach ( $terms as $term ) {
		if ( (int) $term->parent === (int) $parent ) {
			$options[ $prefix . $term->term_id ] = $indent . $term->name;
			get_hierarchical_term_options( $terms, $options, $term->term_id, $indent . '—— ', $prefix );
		}
	}
}

function add_post_list_metabox_fields( $cmb, $identifier = '', $description_extension = '' ) {

    $group_field_id = $cmb->add_field( array(
        'id'          => 'post_list_group' . $identifier,
        'type'        => 'group',
        'description' => 'Aggiungi una sezione con l\'elenco dei post'.$description_extension,
        'options'     => array(
            'group_title'   => 'Sezione {#}',
            'add_button'    => 'Aggiungi Sezione',
            'remove_button' => 'Rimuovi Sezione',
            'sortable'      => true,
        ),
    ) );

    $cmb->add_group_field( $group_field_id, array(
        'name' => 'Titolo sezione',
        'id'   => 'title',
        'type' => 'text',
    ) );

    $cmb->add_group_field( $group_field_id, array(
        'name'    => 'Posizione titolo',
        'id'      => 'title_position',
        'type'    => 'select',
        'options' => array(
            'start'  => 'Sinistra',
            'center' => 'Centro',
            'end'    => 'Destra',
        ),
        'default' => 'left',
    ) );

    // Nuovo campo: Mostra divisore titolo
    $cmb->add_group_field( $group_field_id, array(
        'name' => 'Mostra divisore titolo',
        'id'   => 'show_title_divider',
        'type' => 'checkbox',
    ) );

    // Nuovo campo: Label link piè di sezione
    $cmb->add_group_field( $group_field_id, array(
        'name' => 'Label piè di sezione',
        'id'   => 'footer_link_label',
        'type' => 'text',
    ) );

    $cmb->add_group_field( $group_field_id, array(
        'name' => 'Mostra come bottone',
        'id'   => 'footer_show_as_button',
        'type' => 'checkbox',
    ) );

    // Nuovo campo: Link piè di sezione
    $cmb->add_group_field( $group_field_id, array(
        'name' => 'Link piè di sezione',
        'id'   => 'footer_link_url',
        'type' => 'text',
    ) );

    $cmb->add_group_field( $group_field_id, array(
        'name'    => 'Layout',
        'id'      => 'layout',
        'type'    => 'select',
        'options' => array(
            'griglia'   => 'Griglia',
            'carosello' => 'Carosello',
        ),
    ) );

    $cmb->add_group_field( $group_field_id, array(
        'name'       => 'Scorrimento',
        'id'         => 'scorrimento',
        'type'       => 'select',
        'options'    => array(
            'dots'   => 'Scorrimento dots',
            'frecce' => 'Scorrimento con frecce',
        ),
        'attributes' => array(
            'data-conditional-id'    => 'layout',
            'data-conditional-value' => 'carosello',
        ),
    ) );

    $cmb->add_group_field( $group_field_id, array(
        'name'       => 'Cosa vuoi selezionare?',
        'id'         => 'selection_mode',
        'type'       => 'select',
        'options'    => array(
            ''           => 'Seleziona...',
            'post'       => 'seleziona post',
            'categories' => 'seleziona categorie',
            'taxonomies' => 'seleziona tassonomie',
        ),
        'attributes' => array(
            'class' => 'cmb2-selection-mode-select',
        ),
    ) );

    $post_types        = get_post_types( array( 'public' => true ), 'objects' );
    $post_type_options = array(
        ''         => 'Seleziona...',
        'any' => 'Qualsiasi post',
    );
    foreach ( $post_types as $post_type ) {
        if ( 'attachment' !== $post_type->name ) {
            $post_type_options[ $post_type->name ] = $post_type->labels->singular_name;
        }
    }
    $cmb->add_group_field( $group_field_id, array(
        'name'       => 'Tipologia di Post',
        'id'         => 'post_type',
        'type'       => 'select',
        'options'    => $post_type_options,
        'attributes' => array(
            'class' => 'cmb2-post-type-select',
        ),
    ) );

    // Card type per modalità "post"
    $cmb->add_group_field( $group_field_id, array(
        'name'           => 'Tipologia Card',
        'id'             => 'card_type',
        'type'           => 'select',
        'options_cb'     => 'add_card_type_options_cb',
        'attributes'     => array(
            'class'            => 'cmb2-card-type-select',
            'data-current-value' => '',
        ),
    ) );

    $cmb->add_group_field( $group_field_id, array(
        'name'       => 'Filtra per',
        'id'         => 'filter_by',
        'type'       => 'select',
        'options'    => array(
            ''         => 'I più recenti',
            //'category' => 'I più recenti per categoria',
            'taxonomy' => 'I più recenti per tassonomia',
            'manual'   => 'Selezione manuale',
        ),
        'attributes' => array(
            'class' => 'cmb2-filter-by-select',
        ),
    ) );

    $cmb->add_group_field( $group_field_id, array(
        'name'       => 'Numero massimo di elementi',
        'id'         => 'max_items',
        'type'       => 'text_small',
        'default'    => '6',
        'attributes' => array(
            'type'  => 'number',
            'min'   => '1',
            'class' => 'cmb2-max-items-field',
        ),
    ) );

    // Select singola categoria (per filter=category)
    $all_categories             = get_terms( array(
        'taxonomy'   => 'category',
        'hide_empty' => false,
        'hierarchical' => true,
    ) );
    $categories_checklist_options = array();
    get_hierarchical_term_options( $all_categories, $categories_checklist_options );
    $cmb->add_group_field( $group_field_id, array(
        'name'       => 'Scegli Categoria',
        'id'         => 'category_select',
        'type'       => 'select',
        'options'    => $categories_checklist_options,
        'attributes' => array(
            'class' => 'cmb2-category-select',
        ),
    ) );

    // Select tassonomia/termine “flat” (rimane com’è)
    $cmb->add_group_field( $group_field_id, array(
        'name'       => 'Scegli Tassonomia/Termine',
        'id'         => 'taxonomy_select',
        'type'       => 'select',
        'options_cb' => function() {
            $taxonomy_options = array();
            $taxonomies       = get_taxonomies( array( 'public' => true ), 'objects' );
            foreach ( $taxonomies as $taxonomy ) {
                if ( in_array( $taxonomy->name, array( 'category', 'post_tag', 'nav_menu', 'link_category', 'post_format' ), true ) ) {
                    continue;
                }
                $taxonomy_options[ $taxonomy->name ] = $taxonomy->labels->singular_name;
                $terms = get_terms( array(
                    'taxonomy'   => $taxonomy->name,
                    'hide_empty' => false,
                ) );
                if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                    foreach ( $terms as $term ) {
                        $indent = '—— ';
                        if ( $term->parent ) {
                            $ancestors = get_ancestors( $term->term_id, $taxonomy->name, 'taxonomy' );
                            $indent    = str_repeat( '—— ', count( $ancestors ) + 1 );
                        }
                        $taxonomy_options[ $term->taxonomy . ':' . $term->term_id ] = $indent . $term->name;
                    }
                }
            }
            return $taxonomy_options;
        },
        'attributes' => array(
            'class' => 'cmb2-taxonomy-select',
        ),
    ) );

    // UI selezione manuale post
    $cmb->add_group_field( $group_field_id, array(
        'name'        => 'Seleziona Post (manuale)',
        'id'          => 'manual_posts',
        'type'        => 'text',
        'description' => 'Cerca e seleziona post. I risultati sono filtrati in base alla "Tipologia di Post".',
        'attributes'  => array(
            'class' => 'cmb2-manual-posts',
            'style' => 'display:none;',
        ),
        'after'       => '<div class="manual-posts-ui">
            <div class="manual-posts-search-wrap">
                <input type="text" class="manual-posts-search" placeholder="Cerca post..." />
                <span class="manual-posts-help">Digita per cercare. Clic per selezionare/deselezionare.</span>
            </div>
            <div class="manual-posts-results" style="display:none;"></div>
            <div class="manual-posts-selected"></div>
        </div>',
    ) );

    // Checklist categorie (multi)
    $all_categories             = get_terms( array(
        'taxonomy'   => 'category',
        'hide_empty' => false,
        'hierarchical' => true,
    ) );
    $categories_checklist_options = array();
    get_hierarchical_term_options( $all_categories, $categories_checklist_options );
    $cmb->add_group_field( $group_field_id, array(
        'name'       => 'Seleziona Categorie (multi)',
        'id'         => 'categories_checklist',
        'type'       => 'multicheck',
        'options'    => $categories_checklist_options,
        'attributes' => array(
            'class'                  => 'cmb2-categories-checklist',
            'data-conditional-id'    => 'selection_mode',
            'data-conditional-value' => 'categories',
        ),
    ) );

    // === NUOVA LOGICA TASSONOMIE ===
    // Select tassonomia (mostrata quando selection_mode=taxonomies)
    $taxonomy_options = array( '' => 'Seleziona una tassonomia...' );
    $taxonomies       = get_taxonomies( array( 'public' => true ), 'objects' );
    foreach ( $taxonomies as $taxonomy ) {
        if ( in_array( $taxonomy->name, array( 'category', 'post_tag', 'nav_menu', 'link_category', 'post_format' ), true ) ) {
            continue;
        }
        $taxonomy_options[ $taxonomy->name ] = $taxonomy->labels->singular_name;
    }
    $cmb->add_group_field( $group_field_id, array(
        'name'       => 'Seleziona Tassonomia',
        'id'         => 'selected_taxonomy',
        'type'       => 'select',
        'options'    => $taxonomy_options,
        'attributes' => array(
            'class'                  => 'cmb2-taxonomy-select-terms',
            'data-conditional-id'    => 'selection_mode',
            'data-conditional-value' => 'taxonomies',
        ),
    ) );

    // NUOVA select: Tipologia Card in base alla tassonomia scelta
    $cmb->add_group_field( $group_field_id, array(
        'name'           => 'Tipologia Card (tassonomia)',
        'id'             => 'card_type_taxonomy',
        'type'           => 'select',
        'options_cb'     => 'add_card_type_options_cb', // placeholder; le opzioni vengono riempite via JS
        'attributes'     => array(
            'class'            => 'cmb2-card-type-taxonomy-select',
            'data-current-value' => '',
            'data-conditional-id'    => 'selection_mode',
            'data-conditional-value' => 'taxonomies',
        ),
    ) );

    // Campo MULTICHECK termini popolato via AJAX
    $cmb->add_group_field( $group_field_id, array(
        'name'        => 'Seleziona Termini',
        'id'          => 'selected_terms',
        'type'        => 'multicheck',
        'options'     => array(), // popolati via JS
        'render_row_cb' => function( $field_args, $field ) {
            $value      = (array) $field->value();
            $value      = array_values( array_map( 'strval', $value ) );
            $saved_json = esc_attr( wp_json_encode( $value ) );
            ?>
            <div class="cmb-row cmb-type-multicheck cmb2-id-selected_terms">
                <div class="cmb-th"><label><?php echo esc_html( $field->args( 'name' ) ); ?></label></div>
                <div class="cmb-td">
                    <input type="hidden" class="cmb2-selected-terms-saved"
                           data-field-name="<?php echo esc_attr( $field->args( '_name' ) ); ?>"
                           data-saved="<?php echo esc_attr( $saved_json ); ?>" />
                    <div class="cmb2-terms-checklist"></div>
                </div>
            </div>
            <?php
        },
        'attributes'  => array(
            'data-conditional-id'    => 'selection_mode',
            'data-conditional-value' => 'taxonomies',
        ),
    ) );
}

function add_card_type_options_cb( $field ) {
	$value   = $field->escaped_value();
	$options = array();
	if ( ! empty( $value ) ) {
		$options[ $value ] = $value;
	}
	return $options;
}

add_action( 'admin_enqueue_scripts', function( $hook ) {
	wp_enqueue_script( 'jquery' );
	$card_types_data      = function_exists( 'get_card_type' ) ? get_card_type() : array();
	$card_taxonomies_data = function_exists( 'get_card_taxonomies' ) ? get_card_taxonomies() : array();
	wp_localize_script( 'jquery', 'CMB2PostListData', array(
		'card_types_data'      => $card_types_data, // per modalità "post"
		'card_taxonomies_data' => $card_taxonomies_data, // NUOVO: per modalità "taxonomies"
		'ajax_url'             => admin_url( 'admin-ajax.php' ),
		'nonce'                => wp_create_nonce( 'cmb2_post_search_nonce' ),
	) );
} );

add_action( 'wp_ajax_cmb2_post_search', 'cmb2_handle_post_search' );
function cmb2_handle_post_search() {
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error( array( 'message' => 'Permesso negato.' ), 403 );
	}
	check_ajax_referer( 'cmb2_post_search_nonce', 'nonce' );

	$q         = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
	$post_type = isset( $_GET['post_type'] ) ? sanitize_key( wp_unslash( $_GET['post_type'] ) ) : '';
	$ids       = isset( $_GET['ids'] ) ? (array) $_GET['ids'] : array();
	$ids       = array_filter( array_map( 'absint', $ids ) );

	if ( ! empty( $ids ) ) {
		$args = array(
			'post__in'            => $ids,
			'posts_per_page'      => count( $ids ),
			'ignore_sticky_posts' => true,
			'orderby'             => 'post__in',
			'no_found_rows'       => true,
			'fields'              => 'ids',
		);
		$args['post_type'] = ! empty( $post_type ) ? $post_type : 'any';
		if ( 'any' === $post_type ) {
			$args['post_type'] = 'any';
		}
		$query = new WP_Query( $args );

		$out = array();
		if ( $query->have_posts() ) {
			foreach ( $query->posts as $pid ) {
				$out[] = array(
					'id'    => $pid,
					'title' => html_entity_decode( get_the_title( $pid ), ENT_QUOTES, get_bloginfo( 'charset' ) ),
					'edit'  => get_edit_post_link( $pid, '' ),
				);
			}
		}
		wp_send_json_success( array( 'results' => $out ) );
	}

	$allowed = get_post_types( array( 'public' => true ), 'names' );
	$allowed = array_diff( $allowed, array( 'attachment' ) );

	if ( 'any' !== $post_type && ( empty( $post_type ) || ! in_array( $post_type, $allowed, true ) ) ) {
		wp_send_json_error( array( 'message' => 'post_type non valido.' ), 400 );
	}

	$args = array(
		'posts_per_page'      => 20,
		's'                   => $q,
		'ignore_sticky_posts' => true,
		'orderby'             => 'date',
		'order'               => 'DESC',
		'suppress_filters'    => false,
		'no_found_rows'       => true,
		'fields'              => 'ids',
	);
	if ( 'any' !== $post_type ) {
		$args['post_type'] = $post_type;
	} else {
		$args['post_type'] = 'any';
	}

	$query = new WP_Query( $args );
	$out   = array();
	if ( $query->have_posts() ) {
		foreach ( $query->posts as $pid ) {
			$out[] = array(
				'id'    => $pid,
				'title' => html_entity_decode( get_the_title( $pid ), ENT_QUOTES, get_bloginfo( 'charset' ) ),
				'edit'  => get_edit_post_link( $pid, '' ),
			);
		}
	}
	wp_send_json_success( array( 'results' => $out ) );
}

add_action( 'wp_ajax_cmb2_get_taxonomy_terms', 'cmb2_get_taxonomy_terms_callback' );
function cmb2_get_taxonomy_terms_callback() {
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error( array( 'message' => 'Permesso negato.' ), 403 );
	}
	check_ajax_referer( 'cmb2_post_search_nonce', 'nonce' );

	$taxonomy_name = isset( $_POST['taxonomy'] ) ? sanitize_key( wp_unslash( $_POST['taxonomy'] ) ) : '';

	if ( empty( $taxonomy_name ) ) {
		wp_send_json_error( array( 'message' => 'Tassonomia non valida.' ), 400 );
	}

	$terms = get_terms( array(
		'taxonomy'   => $taxonomy_name,
		'hide_empty' => false,
		'hierarchical' => true,
	) );

	if ( is_wp_error( $terms ) ) {
		wp_send_json_error( array( 'message' => 'Errore nel recupero dei termini.' ), 500 );
	}

	$options = array();
	get_hierarchical_term_options( $terms, $options, 0, '', $taxonomy_name . ':' );
	wp_send_json_success( array( 'terms' => $options ) );
}

/**
 * Mantiene l'ordine delle righe dei gruppi post_list_group in salvataggio (options page).
 */
add_action( 'cmb2_options-page_process_fields_taxonomy_metabox', 'dup_cmb2_normalize_post_list_groups_in_post', 5, 1 );
add_action( 'cmb2_options-page_process_fields_category_metabox', 'dup_cmb2_normalize_post_list_groups_in_post', 5, 1 );
add_action( 'cmb2_options-page_process_fields_homepage_metabox', 'dup_cmb2_normalize_post_list_groups_in_post', 5, 1 );
add_action( 'cmb2_options-page_process_fields_single_metabox', 'dup_cmb2_normalize_post_list_groups_in_post', 5, 1 );

function dup_cmb2_deep_null_to_empty( $value ) {
	if ( is_array( $value ) ) {
		foreach ( $value as $key => $item ) {
			$value[ $key ] = dup_cmb2_deep_null_to_empty( $item );
		}
		return $value;
	}

	return null === $value ? '' : $value;
}

function dup_cmb2_is_empty_field_value( $value ) {
	if ( is_array( $value ) ) {
		foreach ( $value as $item ) {
			if ( ! dup_cmb2_is_empty_field_value( $item ) ) {
				return false;
			}
		}
		return true;
	}

	return $value === '' || $value === null || $value === false;
}

/**
 * Unisce POST e riga salvata (stesso titolo): il POST vince sui campi inviati,
 * il resto viene dal DB (es. categories_checklist assente se la riga era nascosta).
 */
function dup_cmb2_merge_post_list_row( array $row, array $old_rows ) {
	$row   = dup_cmb2_deep_null_to_empty( $row );
	$title = isset( $row['title'] ) ? (string) $row['title'] : '';

	foreach ( $old_rows as $old_row ) {
		if ( ! is_array( $old_row ) ) {
			continue;
		}

		$old_title = isset( $old_row['title'] ) ? (string) $old_row['title'] : '';
		if ( '' === $title || $old_title !== $title ) {
			continue;
		}

		$merged = dup_cmb2_deep_null_to_empty( $old_row );
		foreach ( $row as $key => $val ) {
			if ( ! dup_cmb2_is_empty_field_value( $val ) ) {
				$merged[ $key ] = $val;
			}
		}

		return $merged;
	}

	return $row;
}

function dup_cmb2_get_cmb_option_key( $cmb ) {
	$option_key = $cmb->object_id();

	if ( is_string( $option_key ) && '' !== $option_key ) {
		return $option_key;
	}

	$keys = $cmb->prop( 'option_key' );
	if ( is_string( $keys ) && '' !== $keys ) {
		return $keys;
	}
	if ( is_array( $keys ) && ! empty( $keys ) ) {
		$first = reset( $keys );
		return is_string( $first ) ? $first : (string) $cmb->cmb_id;
	}

	return (string) $cmb->cmb_id;
}

function dup_cmb2_normalize_post_list_groups_in_post( $cmb ) {
	if ( empty( $cmb->data_to_save ) || ! is_array( $cmb->data_to_save ) ) {
		return;
	}

	$cmb->data_to_save = dup_cmb2_deep_null_to_empty( $cmb->data_to_save );

	$option_key = dup_cmb2_get_cmb_option_key( $cmb );
	$existing   = function_exists( 'cmb2_options' )
		? cmb2_options( $option_key )->get_options()
		: get_option( $option_key, array() );
	if ( ! is_array( $existing ) ) {
		$existing = array();
	}

	foreach ( $cmb->data_to_save as $field_id => $value ) {
		if ( 0 !== strpos( (string) $field_id, 'post_list_group' ) || ! is_array( $value ) ) {
			continue;
		}

		$value    = array_values( $value );
		$old_rows = isset( $existing[ $field_id ] ) && is_array( $existing[ $field_id ] )
			? array_values( $existing[ $field_id ] )
			: array();

		foreach ( $value as $index => $row ) {
			if ( is_array( $row ) ) {
				$value[ $index ] = dup_cmb2_merge_post_list_row( $row, $old_rows );
			}
		}

		$cmb->data_to_save[ $field_id ] = $value;
	}
}

add_action( 'admin_head', 'cmb2_print_conditional_script' );
function cmb2_print_conditional_script() {
	if ( ! is_admin() ) {
		return;
	}

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'toplevel_page_impostazioni-template' !== $screen->id ) {
		return;
	}
	?>
    <style>
        .manual-posts-ui {
            margin: 8px 0 4px;
        }

        .manual-posts-search-wrap {
            display: flex;
            gap: 8px;
            align-items: center;
            margin-bottom: 8px;
        }

        .manual-posts-search {
            width: 320px;
            max-width: 100%;
        }

        .manual-posts-help {
            font-size: 11px;
            color: #666;
        }

        .manual-posts-results {
            border: 1px solid #e2e2e2;
            background: #fff;
            max-height: 220px;
            overflow: auto;
            padding: 6px;
            margin-bottom: 8px;
            display: none;
        }

        .manual-posts-results .result-item {
            padding: 6px 8px;
            cursor: pointer;
            border-radius: 3px;
        }

        .manual-posts-results .result-item:hover {
            background: #f3f4f5;
        }

        .manual-posts-results .result-item.is-selected {
            background: #e6f7ff;
        }

        .manual-posts-selected {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .manual-posts-selected .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 8px;
            border-radius: 999px;
            background: #f0f0f0;
            border: 1px solid #ddd;
            font-size: 12px;
        }

        .manual-posts-selected .badge .remove {
            cursor: pointer;
            font-weight: bold;
        }

        .cmb2-id-manual-posts .cmb-th {
            width: 20%;
        }

        .cmb2-id-categories_checklist .cmb-td,
        .cmb2-id-taxonomies_checklist .cmb-td,
        .cmb2-id-selected_terms .cmb-td {
            max-height: 240px;
            overflow: auto;
            padding-right: 6px;
            border: 1px solid #e2e2e2;
            background: #fff;
        }

        .cmb2-id-categories_checklist .cmb-td label,
        .cmb2-id-taxonomies_checklist .cmb-td label,
        .cmb2-id-selected_terms .cmb-td label {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 8px;
            cursor: pointer;
            margin: 0;
        }

        .cmb2-id-selected_terms .cmb-td label input[type="checkbox"] {
            cursor: pointer;
            flex-shrink: 0;
            appearance: auto;
            -webkit-appearance: checkbox;
            accent-color: #2271b1;
        }

        /* Nasconde le checklist per impostazione predefinita */
        .cmb2-id-categories_checklist,
        .cmb2-id-taxonomies_checklist,
        .cmb2-id-selected_taxonomy,
        .cmb2-id-selected_terms,
        .cmb2-id-card_type_taxonomy {
            display: none;
        }
    </style>
     <script type="text/javascript">
        jQuery(function ($) {
            function debounce(fn, wait) {
                var t;
                return function () {
                    var ctx = this, args = arguments;
                    clearTimeout(t);
                    t = setTimeout(function () {
                        fn.apply(ctx, args);
                    }, wait);
                };
            }

            /**
             * Allinea [post_list_group_…][n] all'ordine DOM (salvataggio + frecce).
             */
            function getGroupFieldId($table) {
                var groupId = $table.attr('data-groupid');
                if (groupId) {
                    return String(groupId);
                }
                var id = $table.attr('id') || '';
                if (id.indexOf('_repeat') !== -1) {
                    return id.replace(/_repeat$/, '');
                }
                var selector = $table.find('.cmb-add-group-row').first().data('selector');
                return selector ? String(selector).replace(/_repeat$/, '') : '';
            }

            function replaceGroupIndexInName(name, groupFieldId, fromIndex, toIndex) {
                var escapedGroup = String(groupFieldId).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                var escapedFrom = String(fromIndex).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                var pattern = new RegExp(
                    '(\\[' + escapedGroup + '\\])(\\[' + escapedFrom + '\\])(?=\\[|$)',
                    'g'
                );
                return name.replace(pattern, '$1[' + toIndex + ']');
            }

            function getSelectedTermsFieldBase($row) {
                var $hidden = $row.find('.cmb2-selected-terms-saved').first();
                return $hidden.attr('data-field-name') || '';
            }

            function getSelectedTermsFromRow($row) {
                var ids = [];
                $row.find('.cmb2-terms-checklist input[type="checkbox"]:checked').each(function () {
                    ids.push(String($(this).val()));
                });
                return ids;
            }

            function syncSelectedTermsDataSaved($row) {
                var ids = getSelectedTermsFromRow($row);
                var $hidden = $row.find('.cmb2-selected-terms-saved').first();
                if ($hidden.length) {
                    $hidden.attr('data-saved', JSON.stringify(ids));
                }
                return ids;
            }

            function syncTermsCheckboxNames($row) {
                var base = getSelectedTermsFieldBase($row);
                if (!base) {
                    return;
                }
                $row.find('.cmb2-terms-checklist input[type="checkbox"]').attr('name', base + '[]');
            }

            function renameGroupFieldsInRow($row, groupFieldId, fromIndex, toIndex) {
                if (!groupFieldId || String(fromIndex) === String(toIndex)) {
                    return;
                }

                var marker = '[' + groupFieldId + '][';

                $row.find('[name]').each(function () {
                    var name = this.getAttribute('name');
                    if (!name || name.indexOf(marker) === -1) {
                        return;
                    }
                    this.setAttribute('name', replaceGroupIndexInName(name, groupFieldId, fromIndex, toIndex));
                });

                $row.find('.cmb2-selected-terms-saved').each(function () {
                    var fieldName = this.getAttribute('data-field-name');
                    if (fieldName && fieldName.indexOf(marker) !== -1) {
                        this.setAttribute(
                            'data-field-name',
                            replaceGroupIndexInName(fieldName, groupFieldId, fromIndex, toIndex)
                        );
                    }
                });

                syncTermsCheckboxNames($row);
            }

            function reindexCmbRepeatableGroup($table) {
                if (!$table || !$table.length) {
                    return;
                }
                var groupFieldId = getGroupFieldId($table);
                if (!groupFieldId) {
                    return;
                }
                var groupTitle = $table.find('.cmb-add-group-row').data('grouptitle');
                var $rows = $table.children('.cmb-repeatable-grouping');
                if (!$rows.length) {
                    $rows = $table.find('> .cmb-repeatable-grouping');
                }

                // Due passaggi con indici temporanei per evitare collisioni [0]↔[1].
                $rows.each(function (i) {
                    var $row = $(this);
                    var prev = $row.attr('data-iterator');
                    if (prev === undefined || prev === null || prev === '') {
                        prev = String(i);
                    } else {
                        prev = String(prev);
                    }
                    renameGroupFieldsInRow($row, groupFieldId, prev, 'tmp' + i);
                    $row.attr('data-iterator', 'tmp' + i).data('iterator', 'tmp' + i);
                });

                $rows.each(function (i) {
                    var $row = $(this);
                    renameGroupFieldsInRow($row, groupFieldId, 'tmp' + i, i);

                    var rowId = $row.attr('id');
                    if (rowId) {
                        $row.attr('id', rowId.replace(/-(\d+)$/, '-' + i));
                    }

                    $row.attr('data-iterator', i).data('iterator', i);

                    if (groupTitle && window.CMB2 && typeof window.CMB2.resetGroupTitles === 'function') {
                        window.CMB2.resetGroupTitles($row, i, groupTitle);
                    }

                    syncTermsCheckboxNames($row);
                });
            }

            /**
             * Taxonomy/Category/Single: un solo gruppo visibile alla volta — :visible non basta.
             */
            function reindexActivePostListGroups(form) {
                var $form = $(form);
                var reindexed = 0;

                var $tax = $form.find('#taxonomy_post_type');
                if ($tax.length && $tax.val()) {
                    var $g = $form.find('#post_list_group_' + $tax.val() + '_repeat');
                    if ($g.length) {
                        reindexCmbRepeatableGroup($g);
                        reindexed++;
                    }
                }

                var $cat = $form.find('#category_post_type');
                if ($cat.length && $cat.val()) {
                    var $gCat = $form.find('#post_list_group_' + $cat.val() + '_repeat');
                    if ($gCat.length) {
                        reindexCmbRepeatableGroup($gCat);
                        reindexed++;
                    }
                }

                var $single = $form.find('#single_post_type');
                if ($single.length && $single.val()) {
                    var $gSingle = $form.find('#post_list_group_' + $single.val() + '_repeat');
                    if ($gSingle.length) {
                        reindexCmbRepeatableGroup($gSingle);
                        reindexed++;
                    }
                }

                if (!reindexed) {
                    $form.find('.cmb-repeatable-group.sortable:visible').each(function () {
                        reindexCmbRepeatableGroup($(this));
                        reindexed++;
                    });
                }

                return reindexed;
            }

            /**
             * Solo i post_list_group nascosti: con molti termini superano max_input_vars.
             * Non disabilitare alert/checkbox condizionali: assenti dal POST, CMB2 passa null a wp_kses_post.
             */
            function disableHiddenMetaboxInputs($form) {
                $form.find('.cmb-repeatable-group[id^="post_list_group_"]').each(function () {
                    var $group = $(this);
                    var off = !$group.is(':visible');
                    $group.find('input, select, textarea').prop('disabled', off);
                });
            }

            function runReindexBeforeSave(formEl) {
                var $form = $(formEl);
                if ($form.data('dupReindexed')) {
                    return $form.data('dupReindexed');
                }

                $form.find('.cmb-repeatable-grouping').each(function () {
                    syncSelectedTermsDataSaved($(this));
                });

                var reindexed = reindexActivePostListGroups(formEl);

                $form.find('.cmb-repeatable-grouping').each(function () {
                    syncTermsCheckboxNames($(this));
                });

                handleConditionalFields({ reloadTerms: false });
                disableHiddenMetaboxInputs($form);
                $form.data('dupReindexed', reindexed);
                return reindexed;
            }

            document.addEventListener('submit', function (e) {
                var form = e.target;
                if (!form || !form.classList || !form.classList.contains('cmb-form')) {
                    return;
                }
                runReindexBeforeSave(form);
            }, true);

            $(document).on('click', '.cmb-form input[name="submit-cmb"], .cmb-form button[name="submit-cmb"]', function () {
                var $form = $(this).closest('form.cmb-form');
                if ($form.length) {
                    runReindexBeforeSave($form[0]);
                }
            });

            function shiftPostListGroupRow(btn, moveUp) {
                var $btn = $(btn);
                var $from = $btn.closest('.cmb-repeatable-grouping');
                var $table = $btn.closest('.cmb-repeatable-group.sortable');

                if (!$table.length) {
                    return;
                }

                var $goto = $from[moveUp ? 'prev' : 'next']('.cmb-repeatable-grouping');
                if (!$goto.length) {
                    return;
                }

                if (moveUp) {
                    $from.insertBefore($goto);
                } else {
                    $from.insertAfter($goto);
                }

                reindexCmbRepeatableGroup($table);
                handleConditionalFields();
            }

            var dupShiftLock = false;

            function onShiftRowPointer(e) {
                var btn = e.target.closest('.cmb-shift-rows, .move-up, .move-down');
                if (!btn || !btn.closest('.cmb-repeatable-group.sortable')) {
                    return;
                }
                if (dupShiftLock) {
                    return;
                }
                dupShiftLock = true;
                setTimeout(function () {
                    dupShiftLock = false;
                }, 400);

                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                shiftPostListGroupRow(btn, btn.classList.contains('move-up'));
            }

            // Solo click: mousedown+click spostava due volte e annullava il riordino.
            document.addEventListener('click', onShiftRowPointer, true);

            function disableCmb2NativeRowShift() {
                $('.cmb-repeatable-group.sortable .cmb-shift-rows').off('click');
            }

            $(disableCmb2NativeRowShift);
            $(document).on('cmb2_init', function () {
                setTimeout(disableCmb2NativeRowShift, 0);
            });

            function normalizeIds(arr) {
                return Array.from(new Set(arr.filter(Boolean).map(function (x) {
                    return String(x).trim();
                })));
            }

            function parseIds(value) {
                if (!value) return [];
                return normalizeIds(value.split(','));
            }

            function serializeIds(arr) {
                return normalizeIds(arr).join(',');
            }

            function renderSelected($ui, ids) {
                var $selected = $ui.find('.manual-posts-selected');
                $selected.empty();
                if (!ids.length) {
                    return;
                }
                ids.forEach(function (id) {
                    var title = $ui.data('title-' + id) || ('ID #' + id);
                    var edit = $ui.data('edit-' + id) || '';
                    var $badge = $('<span class="badge" data-id="' + id + '"></span>');
                    $badge.append('<span class="t">' + $('<div>').text(title).html() + '</span>');
                    if (edit) {
                        $badge.append(' <a href="' + edit + '" target="_blank" rel="noopener noreferrer">modifica</a>');
                    }
                    $badge.append(' <span class="remove" title="Rimuovi" aria-label="Rimuovi">×</span>');
                    $selected.append($badge);
                });
            }

            function toggleId($input, $ui, id, title, edit) {
                var ids = parseIds($input.val());
                var exists = ids.indexOf(String(id)) !== -1;
                if (exists) {
                    ids = ids.filter(function (x) {
                        return String(x) !== String(id);
                    });
                } else {
                    ids.push(String(id));
                }
                if (title) {
                    $ui.data('title-' + id, title);
                }
                if (edit) {
                    $ui.data('edit-' + id, edit);
                }
                $input.val(serializeIds(ids)).trigger('change');
                renderSelected($ui, ids);
            }

            function renderResults($ui, results, selectedIds, query) {
                var $list = $ui.find('.manual-posts-results');
                $list.empty();
                if (query && results.length) {
                    $list.show();
                } else {
                    $list.hide();
                }
                results.forEach(function (item) {
                    var $it = $('<div class="result-item" tabindex="0"></div>');
                    $it.attr('data-id', item.id);
                    $it.text(item.title);
                    if (selectedIds.indexOf(String(item.id)) !== -1) {
                        $it.addClass('is-selected');
                    }
                    $ui.data('title-' + item.id, item.title);
                    if (item.edit) {
                        $ui.data('edit-' + item.id, item.edit);
                    }
                    $list.append($it);
                });
            }

            function doSearch($group) {
                var $postTypeSelect = $group.find('.cmb2-post-type-select');
                var postTypeValue = $postTypeSelect.val() || '';
                var $inputHidden = $group.find('.cmb2-manual-posts');
                var $ui = $group.find('.manual-posts-ui');
                var $search = $ui.find('.manual-posts-search');
                var query = ($search.val() || '').trim();
                var selectedIds = parseIds($inputHidden.val());
                if (!query) {
                    renderResults($ui, [], selectedIds, query);
                    return;
                }
                $.ajax({
                    url: (window.CMB2PostListData && CMB2PostListData.ajax_url) || ajaxurl,
                    method: 'GET',
                    dataType: 'json',
                    data: {
                        action: 'cmb2_post_search',
                        nonce: (window.CMB2PostListData && CMB2PostListData.nonce) || '',
                        post_type: postTypeValue,
                        q: query
                    }
                }).done(function (resp) {
                    if (resp && resp.success && resp.data && resp.data.results) {
                        renderResults($ui, resp.data.results, selectedIds, query);
                    } else {
                        renderResults($ui, [], selectedIds, query);
                    }
                }).fail(function () {
                    renderResults($ui, [], selectedIds, query);
                });
            }

            function fetchSelectedDetails($group, ids) {
                var $ui = $group.find('.manual-posts-ui');
                var $postTypeSelect = $group.find('.cmb2-post-type-select');
                var postTypeValue = $postTypeSelect.val() || '';
                if (!ids.length) {
                    return $.Deferred().resolve().promise();
                }
                var missing = ids.filter(function (id) {
                    return !$ui.data('title-' + id);
                });
                if (!missing.length) {
                    return $.Deferred().resolve().promise();
                }
                return $.ajax({
                    url: (window.CMB2PostListData && CMB2PostListData.ajax_url) || ajaxurl,
                    method: 'GET',
                    dataType: 'json',
                    traditional: true,
                    data: {
                        action: 'cmb2_post_search',
                        nonce: (window.CMB2PostListData && CMB2PostListData.nonce) || '',
                        post_type: postTypeValue,
                        'ids[]': missing
                    }
                }).done(function (resp) {
                    if (resp && resp.success && resp.data && resp.data.results) {
                        resp.data.results.forEach(function (item) {
                            $ui.data('title-' + item.id, item.title);
                            if (item.edit) {
                                $ui.data('edit-' + item.id, item.edit);
                            }
                        });
                    }
                });
            }

            // ----- FIX PRINCIPALE: carica termini con baseName corretto e selezioni salvate -----
            function loadTaxonomyTerms($group, taxonomyName, selectedTerms) {
                selectedTerms = (selectedTerms || []).map(String);
                var $termsChecklist = $group.find('.cmb2-terms-checklist');
                var $termsRow = $termsChecklist.closest('.cmb-row');
                var $hiddenBase = $group.find('.cmb2-selected-terms-saved').first();
                var baseName = getSelectedTermsFieldBase($group);

                if (!baseName) {
                    return;
                }

                $termsRow.show();
                $termsChecklist.addClass('loading').html('<div style="padding: 10px;">Caricamento termini...</div>');
                $.ajax({
                    url: (window.CMB2PostListData && CMB2PostListData.ajax_url) || ajaxurl,
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'cmb2_get_taxonomy_terms',
                        taxonomy: taxonomyName,
                        nonce: (window.CMB2PostListData && CMB2PostListData.nonce) || ''
                    }
                }).done(function (resp) {
                    $termsChecklist.removeClass('loading').empty();
                    if (resp && resp.success && resp.data && resp.data.terms) {
                        var optionsHtml = '';
                        var terms = resp.data.terms;
                        Object.keys(terms).forEach(function (termKey) {
                            var termName = terms[termKey];
                            var isChecked = selectedTerms.indexOf(String(termKey)) !== -1 ? ' checked' : '';
                            optionsHtml += '<label><input type="checkbox" name="' + baseName + '[]"' +
                                ' value="' + termKey + '"' + isChecked + '> ' + termName + '</label>';
                        });
                        $termsChecklist.html(optionsHtml);
                        $termsChecklist.attr('data-loaded-taxonomy', taxonomyName);
                        syncTermsCheckboxNames($group);
                        $hiddenBase.attr('data-saved', JSON.stringify(selectedTerms));
                    } else {
                        $termsChecklist.html('<div style="padding: 10px;">Nessun termine trovato.</div>');
                    }
                }).fail(function () {
                    $termsChecklist.removeClass('loading').html('<div style="padding: 10px;">Errore nel caricamento dei termini.</div>');
                });
            }

            // Popola la select "Tipologia Card (tassonomia)" in base alla tassonomia scelta
            function populateTaxonomyCardTypes($group, taxonomyName) {
                var $row = $group.find('.cmb2-card-type-taxonomy-select').closest('.cmb-row');
                var $sel = $group.find('.cmb2-card-type-taxonomy-select');
                var saved = $sel.attr('data-current-value') || $sel.val() || '';
                var data = (window.CMB2PostListData && window.CMB2PostListData.card_taxonomies_data) || {};
                var map = data[taxonomyName] || {};
                var html = '<option value="">Seleziona...</option>';
                Object.keys(map).forEach(function (key) {
                    html += '<option value="' + key + '">' + map[key] + '</option>';
                });
                $sel.html(html);
                if (saved && map[saved]) {
                    $sel.val(saved);
                } else {
                    $sel.val('');
                }
                if (Object.keys(map).length) {
                    $row.show();
                } else {
                    $row.hide();
                }
            }
            
            // Popola la select Card Type (per selezione 'post')
            function populateCardTypeOptions($group) {
                var $select = $group.find('.cmb2-card-type-select');
                var saved = $select.attr('data-current-value') || $select.val() || '';
                var data = (window.CMB2PostListData && window.CMB2PostListData.card_types_data) || {};
                var postType = $group.find('.cmb2-post-type-select').val() || '';

                var map = data[postType] || {};
                var html = '<option value="">Seleziona...</option>';
                Object.keys(map).forEach(function (key) {
                    html += '<option value="' + key + '">' + map[key] + '</option>';
                });
                $select.html(html);
                if (saved && map[saved]) {
                    $select.val(saved);
                } else {
                    $select.val('');
                }
            }


            function handleConditionalFields(options) {
                options = options || {};
                var reloadTerms = options.reloadTerms !== false;

                $('.cmb-repeatable-grouping').each(function () {
                    var $group = $(this);
                    var $selectionModeSelect = $group.find('.cmb2-selection-mode-select');
                    var selectionModeValue = ($selectionModeSelect.val() || '').trim();

                    var $filterBySelect = $group.find('.cmb2-filter-by-select');
                    var $filterByValue = $filterBySelect.val() || '';

                    // ==========================
                    // Gestione della visibilità
                    // ==========================
                    var $postTypeRow = $group.find('.cmb2-post-type-select').closest('.cmb-row');
                    var $filterByRow = $group.find('.cmb2-filter-by-select').closest('.cmb-row');
                    var $cardTypeRow = $group.find('.cmb2-card-type-select').closest('.cmb-row');
                    var $categorySelectRow = $group.find('.cmb2-category-select').closest('.cmb-row');
                    var $taxonomySelectRow = $group.find('.cmb2-taxonomy-select').closest('.cmb-row');
                    var $manualRow = $group.find('.cmb2-manual-posts').closest('.cmb-row');
                    var $maxItemsRow = $group.find('.cmb2-max-items-field').closest('.cmb-row');
                    var $scorrimentoRow = $group.find('#scorrimento').closest('.cmb-row');
                    var $cardTypeTaxRow = $group.find('.cmb2-card-type-taxonomy-select').closest('.cmb-row');
                    var $categoriesChecklistRow = $group.find('.cmb2-categories-checklist').closest('.cmb-row');
                    var $selectedTaxonomyRow = $group.find('.cmb2-taxonomy-select-terms').closest('.cmb-row');
                    var $selectedTermsRow = $group.find('.cmb2-terms-checklist').closest('.cmb-row');

                    // Nasconde tutto per poi mostrare solo i necessari (FEDELE ALL'ORIGINALE)
                    $postTypeRow.hide();
                    $filterByRow.hide();
                    $cardTypeRow.hide();
                    $categorySelectRow.hide();
                    $taxonomySelectRow.hide();
                    $manualRow.hide();
                    $maxItemsRow.hide();
                    $categoriesChecklistRow.hide();
                    $selectedTaxonomyRow.hide();
                    $selectedTermsRow.hide();
                    $cardTypeTaxRow.hide();


                    if (selectionModeValue === 'post') {
                        $postTypeRow.show();
                        $filterByRow.show();
                        $cardTypeRow.show(); 
                        $maxItemsRow.show();
                        
                        populateCardTypeOptions($group);

                        if ($filterByValue === 'category') {
                             $categorySelectRow.show();
                        } else if ($filterByValue === 'taxonomy') {
                            $taxonomySelectRow.show();
                        } else if ($filterByValue === 'manual') {
                            $manualRow.show();
                            
                            // *** FIX PER LA VISUALIZZAZIONE DEI POST SALVATI ***
                            var $inputHidden = $group.find('.cmb2-manual-posts');
                            var $ui = $group.find('.manual-posts-ui');
                            var ids = parseIds($inputHidden.val());

                            // Recupera i dettagli (titoli) dei post salvati e li renderizza
                            fetchSelectedDetails($group, ids).done(function() {
                                renderSelected($ui, ids);
                            });
                            // Fine fix
                        }
                    } else if (selectionModeValue === 'categories') {
                        // FIX: Solo la checklist deve essere visibile
                        $categoriesChecklistRow.show();
                    } else if (selectionModeValue === 'taxonomies') {
                        $selectedTaxonomyRow.show();
                        $cardTypeTaxRow.show();
                        
                        var taxonomyName = $group.find('.cmb2-taxonomy-select-terms').val();
                        if (taxonomyName) {
                            $selectedTermsRow.show();

                            var $checklist = $group.find('.cmb2-terms-checklist');
                            var $hiddenBase = $group.find('.cmb2-selected-terms-saved').first();
                            var needsReload = reloadTerms && (
                                !$checklist.attr('data-loaded-taxonomy') ||
                                $checklist.attr('data-loaded-taxonomy') !== taxonomyName ||
                                !$checklist.find('label').length
                            );

                            if (needsReload) {
                                var savedTerms = getSelectedTermsFromRow($group);
                                if (!savedTerms.length) {
                                    try {
                                        savedTerms = JSON.parse($hiddenBase.attr('data-saved') || '[]');
                                    } catch (e) {
                                        savedTerms = [];
                                    }
                                }
                                loadTaxonomyTerms($group, taxonomyName, savedTerms);
                            }

                            populateTaxonomyCardTypes($group, taxonomyName);
                        }
                    }

                    // Logica aggiuntiva per Carosello/Scorrimento
                    var $layoutSelect = $group.find('#layout');
                    if ($layoutSelect.val() === 'carosello') {
                        $scorrimentoRow.show();
                    } else {
                        $scorrimentoRow.hide();
                    }
                });
            }

            // =========================================================================
            // GESTORI EVENTI PRINCIPALI
            // =========================================================================

            // Gestori per garantire che la logica venga eseguita su start e interazioni
            $(document).on('cmb2_init', handleConditionalFields);
            $(document).on('cmb2_add_group_row', handleConditionalFields);
            $(document).on('change', '.cmb2-selection-mode-select, .cmb2-filter-by-select, .cmb2-post-type-select, #layout', handleConditionalFields);


            // Gestione della selezione manuale
            $('.cmb2-post-type-select').on('change', function () {
                var $group = $(this).closest('.cmb-repeatable-grouping');
                // Pulisce la selezione manuale quando si cambia il post type
                $group.find('.cmb2-manual-posts').val('').trigger('change');
                $group.find('.manual-posts-selected').empty();
                // Rilancia la logica per aggiornare le card type
                handleConditionalFields();
            });

            // Gestione dei badge
            $(document).on('click', '.manual-posts-selected .badge .remove', function () {
                var $badge = $(this).closest('.badge');
                var id = $badge.data('id');
                var $ui = $badge.closest('.manual-posts-ui');
                var $inputHidden = $ui.closest('.cmb-row').find('.cmb2-manual-posts');
                toggleId($inputHidden, $ui, id);
            });

            // Gestione del click sui risultati della ricerca
            $(document).on('click', '.manual-posts-results .result-item', function () {
                var $item = $(this);
                var $ui = $item.closest('.manual-posts-ui');
                var $inputHidden = $ui.closest('.cmb-row').find('.cmb2-manual-posts');
                var id = $item.data('id');
                var title = $item.text();
                var edit = $ui.data('edit-' + id) || '';
                toggleId($inputHidden, $ui, id, title, edit);
                $item.toggleClass('is-selected');
            });

            // Gestione della ricerca con debounce
            $(document).on('keyup', '.manual-posts-search', debounce(function () {
                var $group = $(this).closest('.cmb-repeatable-grouping');
                doSearch($group);
            }, 300));

            $(document).on('change', '.cmb2-terms-checklist input[type="checkbox"]', function () {
                syncSelectedTermsDataSaved($(this).closest('.cmb-repeatable-grouping'));
                // La spunta in wp-admin si disegna spesso solo dopo blur (come un click fuori)
                window.requestAnimationFrame(function () {
                    var el = document.activeElement;
                    if (el && typeof el.blur === 'function') {
                        el.blur();
                    }
                });
            });

            // Gestione cambio tassonomia per i termini
            $(document).on('change', '.cmb2-taxonomy-select-terms', function() {
                var $group = $(this).closest('.cmb-repeatable-grouping');
                var taxonomyName = $(this).val();
                var $hiddenBase = $group.find('.cmb2-selected-terms-saved').first();
                var $termsChecklist = $group.find('.cmb2-terms-checklist');

                $termsChecklist.empty().removeAttr('data-loaded-taxonomy');
                $hiddenBase.attr('data-saved', '[]');

                if (taxonomyName) {
                    loadTaxonomyTerms($group, taxonomyName, []);
                    populateTaxonomyCardTypes($group, taxonomyName);
                } else {
                    $group.find('.cmb2-card-type-taxonomy-select').closest('.cmb-row').hide();
                }
            });
            

            // Avvia la gestione delle condizioni al caricamento
            handleConditionalFields();
        });
    </script>
	<?php
}