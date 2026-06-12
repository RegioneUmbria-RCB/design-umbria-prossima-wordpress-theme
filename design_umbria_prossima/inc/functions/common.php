<?php
//ENTITY CARDS

function get_card_type(){
 return array(
    'any' => array(
        'card-full-image-1' => 'Card Immagine Background',
        'card-generic-post-1'  => 'Card Post Generico',
    ),
    'notizia'   => array(
        'card-notizia-1'  => 'Card Notizia Standard',
        'card-generic-post-1'  => 'Card Post Generico',
        'card-full-image-1' => 'Card Immagine Background',
    ),
    'servizio'   => array(
        'card-servizio-1'  => 'Card Servizio Standard',
        'card-generic-post-1'  => 'Card Post Generico',
        'card-full-image-1' => 'Card Immagine Background',
    ),
    'documento_pubblico'   => array(
        'card-documento-pubblico-1'  => 'Card Documento Pubblico Standard',
        'card-generic-post-1'  => 'Card Post Generico',
        'card-full-image-1' => 'Card Immagine Background',
    ),
    'sito_tematico'   => array(
        'card-sito-tematico-1'  => 'Card Sito Tematico Standard',
        'card-generic-post-1'  => 'Card Post Generico',
        'card-full-image-1' => 'Card Immagine Background',
    ),
    'persona_pubblica'   => array(
        'card-persona-pubblica-1'  => 'Card Persona Pubblica Standard',
        'card-generic-post-1'  => 'Card Post Generico',
        'card-full-image-1' => 'Card Immagine Background',
    ),
    'comuni'   => array(
        'card-comune-1'  => 'Card Comune Standard',
        'card-generic-post-1'  => 'Card Post Generico',
        'card-full-image-1' => 'Card Immagine Background',
    ),
    'unita_organizzativa' => array(
        'card-unita-organizzativa-1'  => 'Card Unità Organizzativa Standard',
        'card-generic-post-1'  => 'Card Post Generico',
        'card-full-image-1' => 'Card Immagine Background',
    ),
    'punto_contatto' => array(
        'card-punto-contatto-1' => 'Card Punto di Contatto Standard',
        'card-generic-post-1'   => 'Card Post Generico',
    ),
    'enti_e_fondazioni' => array(
        'card-ente-fondazione-1'  => 'Card Enti e Fondazioni',
        'card-generic-post-1'  => 'Card Post Generico',
        'card-full-image-1' => 'Card Immagine Background',
    )
  );
}

function get_card_taxonomies(){
 return array(
    'focus'  => array(
        'card-focus-1'   => 'Card Focus Standard',
    ),
    'argomenti' => array(
        'card-argomento-1' => 'Card Argomento Standard',
    ),
  );
}

//CUSTOM ENTITY

function get_custom_post_type(){
 return array(
    'enti_e_fondazioni' => 'Enti e Fondazioni',
    'comuni' => 'Comuni',
  );
}

function get_custom_toxonomy(){
 return array(
    'Focus',
  );
}

/**
 * Normalizza un valore CMB2 file_list (o URL singolo) in array di URL.
 *
 * @param mixed $raw Valore meta CMB2.
 * @return string[]
 */
function dup_normalize_cmb2_file_list( $raw ) {
	if ( is_array( $raw ) && count( $raw ) ) {
		return array_values( array_filter( $raw ) );
	}
	if ( is_string( $raw ) && $raw ) {
		return array( $raw );
	}
	return array();
}

/**
 * Verifica se un paragrafo aggiuntivo del documento pubblico ha contenuto da mostrare.
 *
 * @param array $paragrafo Dati del gruppo CMB2 paragrafi_aggiuntivi.
 */
function dup_documento_pubblico_paragrafo_has_content( $paragrafo ) {
	if ( ! is_array( $paragrafo ) ) {
		return false;
	}
	if ( ! empty( $paragrafo['titolo'] ) || ! empty( $paragrafo['testo'] ) || ! empty( $paragrafo['immagine'] ) ) {
		return true;
	}
	return ! empty( dup_normalize_cmb2_file_list( $paragrafo['files_di_dettaglio'] ?? null ) );
}

/**
 * Profondità di una categoria rispetto a un antenato (0 = stesso termine).
 *
 * @return int Profondità oppure -1 se non è discendente.
 */
function dup_get_category_depth_below( $term_id, $root_id ) {
	$term_id = (int) $term_id;
	$root_id = (int) $root_id;

	if ( $term_id === $root_id ) {
		return 0;
	}

	$cat = get_category( $term_id );
	if ( ! $cat ) {
		return -1;
	}

	$depth     = 0;
	$parent_id = (int) $cat->parent;

	while ( $parent_id ) {
		$depth++;
		if ( $parent_id === $root_id ) {
			return $depth;
		}
		$parent = get_category( $parent_id );
		if ( ! $parent ) {
			return -1;
		}
		$parent_id = (int) $parent->parent;
	}

	return -1;
}

/**
 * Filtra le categorie mantenendo solo i discendenti entro N livelli dal termine radice.
 *
 * @param WP_Term[] $categories
 * @return WP_Term[]
 */
function dup_filter_categories_by_max_depth( array $categories, $root_id, $max_depth ) {
	$max_depth = (int) $max_depth;
	if ( $max_depth < 1 ) {
		return $categories;
	}

	return array_values(
		array_filter(
			$categories,
			static function ( $cat ) use ( $root_id, $max_depth ) {
				$depth = dup_get_category_depth_below( $cat->term_id, $root_id );
				return $depth > 0 && $depth <= $max_depth;
			}
		)
	);
}

/**
 * Opzioni select per il limite di livelli sottocategorie in Category Page.
 *
 * @return array<string, string>
 */
function dup_get_category_subcategories_depth_options() {
	return array(
		''  => __( 'Tutti i livelli', 'design_umbria_prossima' ),
		'1' => __( '1 livello (solo figli diretti)', 'design_umbria_prossima' ),
		'2' => __( '2 livelli', 'design_umbria_prossima' ),
		'3' => __( '3 livelli', 'design_umbria_prossima' ),
		'4' => __( '4 livelli', 'design_umbria_prossima' ),
		'5' => __( '5 livelli', 'design_umbria_prossima' ),
	);
}

/**
 * URL action dei form di ricerca WordPress (rispetta sottocartella, es. /webpg3/search/).
 */
function get_search_form_action_url() {
	return user_trailingslashit( home_url( '/search/' ) );
}

/**
 * Form di ricerca generati da get_search_form().
 */
add_filter( 'get_search_form', 'dup_filter_search_form_action_url', 20 );
function dup_filter_search_form_action_url( $form ) {
	$url = esc_url( get_search_form_action_url() );
	if ( preg_match( '/\saction=(["\'])([^"\']*)\1/i', $form ) ) {
		return preg_replace( '/\saction=(["\'])([^"\']*)\1/i', ' action="' . $url . '"', $form, 1 );
	}
	return preg_replace( '/<form\b/i', '<form action="' . $url . '"', $form, 1 );
}

/**
 * Corregge action="/search/" hardcoded in tutti i template del tema (hero, modale, ecc.).
 */
add_action( 'template_redirect', 'dup_start_search_form_action_buffer', 0 );
function dup_start_search_form_action_buffer() {
	if ( is_admin() ) {
		return;
	}
	ob_start( 'dup_replace_hardcoded_search_form_actions' );
}

function dup_replace_hardcoded_search_form_actions( $html ) {
	if ( ! is_string( $html ) || '' === $html ) {
		return $html;
	}
	$url = esc_url( get_search_form_action_url() );
	$html = str_replace( 'action="/search/"', 'action="' . $url . '"', $html );
	$html = str_replace( "action='/search/'", "action='" . $url . "'", $html );
	return $html;
}

/**
 * Tassonomie ammesse come filtro breadcrumb, in ordine di priorità.
 *
 * @return string[]
 */
function dup_get_breadcrumb_taxonomy_slugs_for_post_type( $post_type ) {
	$whitelists = array(
		'documento_pubblico' => array( 'tipi_documento', 'argomenti' ),
		'notizia'            => array( 'tipi_notizia', 'argomenti' ),
		'servizio'           => array( 'categorie_servizio', 'argomenti' ),
		'evento'             => array( 'tipi_evento', 'argomenti' ),
		'luogo'              => array( 'tipi_luogo', 'argomenti' ),
		'unita_organizzativa' => array( 'tipi_unita_organizzativa', 'argomenti' ),
		'incarico'           => array( 'tipi_incarico', 'argomenti' ),
		'punto_contatto'     => array( 'tipi_punto_contatto', 'argomenti' ),
		'dataset'            => array( 'temi_dataset', 'argomenti' ),
	);

	if ( isset( $whitelists[ $post_type ] ) ) {
		return $whitelists[ $post_type ];
	}

	$slugs = array();
	if ( is_object_in_taxonomy( $post_type, 'argomenti' ) ) {
		$slugs[] = 'argomenti';
	}

	foreach ( get_object_taxonomies( $post_type ) as $taxonomy_slug ) {
		if ( 0 === strpos( $taxonomy_slug, 'tipi_' ) || 0 === strpos( $taxonomy_slug, 'categorie_' ) || 0 === strpos( $taxonomy_slug, 'temi_' ) ) {
			$slugs[] = $taxonomy_slug;
		}
	}

	return array_values( array_unique( $slugs ) );
}

/**
 * Tassonomie utilizzabili come filtro breadcrumb per un post type.
 *
 * @return WP_Taxonomy[]
 */
function dup_get_breadcrumb_taxonomies_for_post_type( $post_type ) {
	$result = array();

	foreach ( dup_get_breadcrumb_taxonomy_slugs_for_post_type( $post_type ) as $taxonomy_slug ) {
		$taxonomy = get_taxonomy( $taxonomy_slug );
		if ( ! $taxonomy || ! is_object_in_taxonomy( $post_type, $taxonomy_slug ) ) {
			continue;
		}

		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomy_slug,
				'hide_empty' => false,
			)
		);
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			$result[ $taxonomy_slug ] = $taxonomy;
		}
	}

	return $result;
}

/**
 * Opzioni select tipologia breadcrumb (taxonomy:term_id => etichetta).
 *
 * @return array<string, string>
 */
function dup_get_breadcrumb_term_options_for_post_type( $post_type ) {
	$options = array(
		'' => __( 'Tutti i contenuti del post type', 'design_umbria_prossima' ),
	);

	if ( ! $post_type ) {
		return $options;
	}

	foreach ( dup_get_breadcrumb_taxonomies_for_post_type( $post_type ) as $taxonomy ) {
		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomy->name,
				'hide_empty' => false,
			)
		);
		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			continue;
		}

		dup_append_hierarchical_breadcrumb_term_options(
			$terms,
			$taxonomy,
			$options,
			0,
			''
		);
	}

	return $options;
}

/**
 * @param WP_Term[] $terms
 * @param WP_Taxonomy $taxonomy
 * @param array<string, string> $options
 */
function dup_append_hierarchical_breadcrumb_term_options( $terms, $taxonomy, &$options, $parent = 0, $indent = '' ) {
	foreach ( $terms as $term ) {
		if ( (int) $term->parent !== (int) $parent ) {
			continue;
		}
		$key             = $taxonomy->name . ':' . $term->term_id;
		$options[ $key ] = $taxonomy->labels->singular_name . ' — ' . $indent . $term->name;
		dup_append_hierarchical_breadcrumb_term_options( $terms, $taxonomy, $options, $term->term_id, $indent . '—— ' );
	}
}

/**
 * Mappa post type → gruppi tipologia/argomento per l'admin Category Page.
 *
 * @return array<string, array<int, array{taxonomy: string, label: string, options: array<int, array{value: string, label: string}>}>>
 */
function dup_get_breadcrumb_terms_map_for_admin() {
	$map        = array();
	$post_types = get_post_types( array( 'public' => true ), 'names' );

	foreach ( $post_types as $post_type ) {
		if ( 'attachment' === $post_type ) {
			continue;
		}

		$map[ $post_type ] = array();

		foreach ( dup_get_breadcrumb_taxonomies_for_post_type( $post_type ) as $taxonomy ) {
			$terms = get_terms(
				array(
					'taxonomy'   => $taxonomy->name,
					'hide_empty' => false,
				)
			);
			if ( empty( $terms ) || is_wp_error( $terms ) ) {
				continue;
			}

			$flat_options = array();
			dup_append_hierarchical_breadcrumb_term_options_flat(
				$terms,
				$taxonomy,
				$flat_options,
				0,
				''
			);

			if ( empty( $flat_options ) ) {
				continue;
			}

			$map[ $post_type ][] = array(
				'taxonomy' => $taxonomy->name,
				'label'    => $taxonomy->labels->singular_name,
				'options'  => $flat_options,
			);
		}
	}

	return $map;
}

/**
 * @param WP_Term[] $terms
 * @param WP_Taxonomy $taxonomy
 * @param array<int, array{value: string, label: string}> $options
 */
function dup_append_hierarchical_breadcrumb_term_options_flat( $terms, $taxonomy, &$options, $parent = 0, $indent = '' ) {
	foreach ( $terms as $term ) {
		if ( (int) $term->parent !== (int) $parent ) {
			continue;
		}

		$options[] = array(
			'value' => $taxonomy->name . ':' . $term->term_id,
			'label' => $indent . $term->name,
		);

		dup_append_hierarchical_breadcrumb_term_options_flat(
			$terms,
			$taxonomy,
			$options,
			$term->term_id,
			$indent . '—— '
		);
	}
}

/**
 * Verifica se un contenuto ha il termine configurato per il breadcrumb.
 */
function dup_post_matches_breadcrumb_term( $post_id, $taxonomy, $term_id ) {
	if ( has_term( $term_id, $taxonomy, $post_id ) ) {
		return true;
	}

	$post_type = get_post_type( $post_id );
	$prefix    = '_dci_' . $post_type . '_';

	if ( 'argomenti' === $taxonomy ) {
		$stored = get_post_meta( $post_id, $prefix . 'argomenti', true );
		if ( is_array( $stored ) ) {
			foreach ( $stored as $key => $value ) {
				if ( is_int( $key ) && (int) $value === (int) $term_id ) {
					return true;
				}
				if ( (int) $key === (int) $term_id && ( 'on' === $value || true === $value || 1 === $value || '1' === $value ) ) {
					return true;
				}
				if ( (string) $value === (string) $term_id ) {
					return true;
				}
			}
		}
	}

	if ( 'tipi_documento' === $taxonomy ) {
		$stored = get_post_meta( $post_id, $prefix . 'tipo_documento', true );
		if ( (string) $stored === (string) $term_id ) {
			return true;
		}
		$term = get_term( $term_id, $taxonomy );
		if ( $term && ! is_wp_error( $term ) && (string) $stored === $term->slug ) {
			return true;
		}
	}

	return false;
}

/**
 * Regole breadcrumb da category_metabox.
 *
 * @return array<int, array{category_id: int, category_name: string, post_type: string, subtype: string, rule_key: string}>
 */
function dup_get_breadcrumb_category_rules() {
	$options = get_option( 'category_metabox', array() );
	if ( ! is_array( $options ) ) {
		return array();
	}

	$rules = array();

	foreach ( $options as $key => $value ) {
		if ( 0 !== strpos( $key, 'post_type_for_category_' ) ) {
			continue;
		}

		$post_type = (string) $value;
		if ( '' === $post_type ) {
			continue;
		}

		$category_id = (int) str_replace( 'post_type_for_category_', '', $key );
		$category    = get_category( $category_id );
		if ( ! $category || is_wp_error( $category ) ) {
			continue;
		}

		$subtype_key = 'breadcrumb_term_for_category_' . $category_id;
		$subtype     = isset( $options[ $subtype_key ] ) ? (string) $options[ $subtype_key ] : '';
		$rule_key    = '' !== $subtype ? $subtype : '__generic__:' . $post_type;

		$rules[] = array(
			'category_id'   => $category_id,
			'category_name' => $category->name,
			'post_type'     => $post_type,
			'subtype'       => $subtype,
			'rule_key'      => $rule_key,
		);
	}

	return $rules;
}

/**
 * Conflitti di configurazione breadcrumb (stessa tipologia o stesso post type generico su più categorie).
 *
 * @return string[]
 */
function dup_detect_breadcrumb_category_conflicts() {
	$rules     = dup_get_breadcrumb_category_rules();
	$grouped   = array();
	$conflicts = array();

	foreach ( $rules as $rule ) {
		$grouped[ $rule['rule_key'] ][] = $rule;
	}

	foreach ( $grouped as $rule_key => $group ) {
		if ( count( $group ) < 2 ) {
			continue;
		}

		$names = wp_list_pluck( $group, 'category_name' );

		if ( 0 === strpos( $rule_key, '__generic__:' ) ) {
			$post_type = substr( $rule_key, strlen( '__generic__:' ) );
			$conflicts[] = sprintf(
				/* translators: 1: post type slug, 2: comma-separated category names */
				__( 'Più categorie usano il post type "%1$s" senza tipologia/argomento: %2$s. Ne verrà usata solo la prima in ordine di configurazione.', 'design_umbria_prossima' ),
				$post_type,
				implode( ', ', $names )
			);
			continue;
		}

		$term_label = $rule_key;
		if ( preg_match( '/^([a-z0-9_-]+):(\d+)$/', $rule_key, $matches ) ) {
			$term = get_term( (int) $matches[2], $matches[1] );
			if ( $term && ! is_wp_error( $term ) ) {
				$tax_obj = get_taxonomy( $matches[1] );
				$tax_label = $tax_obj ? $tax_obj->labels->singular_name : $matches[1];
				$term_label = $tax_label . ' — ' . $term->name;
			}
		}

		$conflicts[] = sprintf(
			/* translators: 1: taxonomy term label, 2: comma-separated category names */
			__( 'La tipologia "%1$s" è mappata su più categorie: %2$s. Ne verrà usata solo la prima in ordine di configurazione.', 'design_umbria_prossima' ),
			$term_label,
			implode( ', ', $names )
		);
	}

	return $conflicts;
}

/**
 * @return array<string, mixed>
 */
function dup_get_taxonomy_metabox_options() {
	$options = get_option( 'taxonomy_metabox', array() );
	return is_array( $options ) ? $options : array();
}

/**
 * Chiave campo taxonomy_metabox per un termine.
 */
function dup_taxonomy_metabox_field_prefix( $taxonomy, $term_id ) {
	return (string) $taxonomy . '_' . (int) $term_id;
}

/**
 * Breadcrumb abilitato per questo termine (Taxonomy Page).
 */
function dup_is_taxonomy_breadcrumb_enabled( $taxonomy, $term_id ) {
	$key = 'breadcrumb_enable_' . dup_taxonomy_metabox_field_prefix( $taxonomy, $term_id );
	$options = dup_get_taxonomy_metabox_options();
	return isset( $options[ $key ] ) && 'on' === $options[ $key ];
}

/**
 * Categoria WP opzionale come prefisso prima della gerarchia del termine.
 *
 * @return int|false
 */
function dup_get_breadcrumb_wp_category_prefix_for_taxonomy_term( $taxonomy, $term_id ) {
	$key    = 'breadcrumb_category_' . dup_taxonomy_metabox_field_prefix( $taxonomy, $term_id );
	$options = dup_get_taxonomy_metabox_options();
	$cat_id  = isset( $options[ $key ] ) ? (int) $options[ $key ] : 0;
	$category = $cat_id ? get_category( $cat_id ) : null;

	return ( $category && ! is_wp_error( $category ) ) ? $cat_id : false;
}

/**
 * Termini assegnati a un post (tassonomia + meta CMB2 per argomenti).
 *
 * @return WP_Term[]
 */
function dup_get_post_terms_for_breadcrumb( $post_id, $taxonomy ) {
	$post_id  = (int) $post_id;
	$taxonomy = (string) $taxonomy;
	$terms    = wp_get_post_terms( $post_id, $taxonomy );

	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		return $terms;
	}

	if ( 'argomenti' !== $taxonomy ) {
		return array();
	}

	$post_type = get_post_type( $post_id );
	$prefix    = '_dci_' . $post_type . '_';
	$stored    = get_post_meta( $post_id, $prefix . 'argomenti', true );
	$term_ids  = array();

	if ( is_array( $stored ) ) {
		foreach ( $stored as $key => $value ) {
			if ( is_int( $key ) && $value ) {
				$term_ids[] = (int) $value;
			} elseif ( ( 'on' === $value || true === $value || 1 === $value || '1' === $value ) && $key ) {
				$term_ids[] = (int) $key;
			} elseif ( $value ) {
				$term_ids[] = (int) $value;
			}
		}
	}

	$terms = array();
	foreach ( array_unique( array_filter( $term_ids ) ) as $term_id ) {
		$term = get_term( $term_id, $taxonomy );
		if ( $term && ! is_wp_error( $term ) ) {
			$terms[] = $term;
		}
	}

	return $terms;
}

/**
 * Termine tassonomia con breadcrumb abilitato, più specifico (più profondo nella gerarchia).
 *
 * @return WP_Term|null
 */
function dup_get_breadcrumb_taxonomy_term_for_post( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	if ( ! $post_id ) {
		return null;
	}

	$best_term = null;
	$best_depth = -1;

	foreach ( get_object_taxonomies( get_post_type( $post_id ), 'objects' ) as $taxonomy ) {
		if ( in_array( $taxonomy->name, array( 'category', 'post_tag', 'nav_menu', 'link_category', 'post_format' ), true ) ) {
			continue;
		}

		foreach ( dup_get_post_terms_for_breadcrumb( $post_id, $taxonomy->name ) as $term ) {
			if ( ! dup_is_taxonomy_breadcrumb_enabled( $term->taxonomy, $term->term_id ) ) {
				continue;
			}

			$depth = count( get_ancestors( $term->term_id, $term->taxonomy, 'taxonomy' ) );
			if ( $depth > $best_depth ) {
				$best_depth = $depth;
				$best_term  = $term;
			}
		}
	}

	return $best_term;
}

/**
 * Avvisi configurazione Taxonomy Page / sovrapposizioni con Category Page.
 *
 * @return string[]
 */
function dup_detect_breadcrumb_taxonomy_conflicts() {
	$conflicts = array();
	$options   = dup_get_taxonomy_metabox_options();

	foreach ( $options as $key => $value ) {
		if ( 0 !== strpos( $key, 'breadcrumb_enable_' ) || 'on' !== $value ) {
			continue;
		}

		$field_prefix = substr( $key, strlen( 'breadcrumb_enable_' ) );
		if ( ! preg_match( '/^([a-z0-9_-]+)_(\d+)$/', $field_prefix, $matches ) ) {
			continue;
		}

		$taxonomy = $matches[1];
		$term_id  = (int) $matches[2];
		$term     = get_term( $term_id, $taxonomy );
		if ( ! $term || is_wp_error( $term ) ) {
			continue;
		}

		foreach ( dup_get_breadcrumb_category_rules() as $rule ) {
			if ( $rule['subtype'] !== $taxonomy . ':' . $term_id ) {
				continue;
			}

			$prefix_id = dup_get_breadcrumb_wp_category_prefix_for_taxonomy_term( $taxonomy, $term_id );
			if ( $prefix_id && (int) $prefix_id !== (int) $rule['category_id'] ) {
				$prefix_cat = get_category( $prefix_id );
				$conflicts[] = sprintf(
					__( 'Il termine "%1$s": Category Page punta a "%2$s", Taxonomy Page usa prefisso "%3$s".', 'design_umbria_prossima' ),
					$term->name,
					$rule['category_name'],
					$prefix_cat && ! is_wp_error( $prefix_cat ) ? $prefix_cat->name : '#' . $prefix_id
				);
			} elseif ( ! $prefix_id ) {
				$conflicts[] = sprintf(
					__( 'Il termine "%1$s" ha breadcrumb attivo in Taxonomy Page e anche una regola in Category Page: per i singoli contenuti vince la gerarchia tassonomia.', 'design_umbria_prossima' ),
					$term->name
				);
			}
			break;
		}
	}

	return $conflicts;
}

/**
 * Categoria breadcrumb per un singolo contenuto (category_metabox).
 *
 * @param int|null $post_id
 * @return int|false
 */
function dup_get_breadcrumb_category_id_for_post( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	if ( ! $post_id ) {
		return false;
	}

	$post_type           = get_post_type( $post_id );
	$generic_category_id = false;

	foreach ( dup_get_breadcrumb_category_rules() as $rule ) {
		if ( $rule['post_type'] !== $post_type ) {
			continue;
		}

		if ( '' === $rule['subtype'] ) {
			if ( false === $generic_category_id ) {
				$generic_category_id = $rule['category_id'];
			}
			continue;
		}

		if ( ! preg_match( '/^([a-z0-9_-]+):(\d+)$/', $rule['subtype'], $matches ) ) {
			continue;
		}

		if ( dup_post_matches_breadcrumb_term( $post_id, $matches[1], (int) $matches[2] ) ) {
			return $rule['category_id'];
		}
	}

	return $generic_category_id;
}

/**
 * Stampa breadcrumb tassonomia: prefisso categorie WP (opz.) + gerarchia termini.
 *
 * @param string $taxonomy
 * @param int    $term_id
 * @param int    $position
 * @param bool   $active_last Ultima voce del termine senza link (archivio).
 */
function dup_breadcrumb_echo_taxonomy_term_trail( $taxonomy, $term_id, &$position, $active_last = false ) {
	$taxonomy = (string) $taxonomy;
	$term_id  = (int) $term_id;

	$wp_category_id = dup_get_breadcrumb_wp_category_prefix_for_taxonomy_term( $taxonomy, $term_id );
	if ( $wp_category_id ) {
		dup_breadcrumb_echo_category_trail( $wp_category_id, $position );
	}

	$ancestors = array_reverse( get_ancestors( $term_id, $taxonomy, 'taxonomy' ) );
	$chain     = array_merge( $ancestors, array( $term_id ) );

	foreach ( $chain as $chain_term_id ) {
		$term = get_term( (int) $chain_term_id, $taxonomy );
		if ( ! $term || is_wp_error( $term ) ) {
			continue;
		}

		$is_last = ( (int) $chain_term_id === $term_id );
		$active  = $is_last && $active_last;
		$link    = get_term_link( $term );
		?>
		<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="breadcrumb-item<?php echo $active ? ' active' : ''; ?>">
			<span class="separator">/</span>
			<?php if ( ! $active && ! is_wp_error( $link ) ) : ?>
				<a class="text-primary" href="<?php echo esc_url( $link ); ?>" itemprop="item">
					<span itemprop="name"><?php echo esc_html( $term->name ); ?></span>
				</a>
			<?php else : ?>
				<span itemprop="item"><span itemprop="name"><?php echo esc_html( $term->name ); ?></span></span>
			<?php endif; ?>
			<meta itemprop="position" content="<?php echo (int) $position; ?>" />
		</li>
		<?php
		$position++;
	}
}

/**
 * Stampa le voci breadcrumb per una categoria WP e i suoi genitori.
 *
 * @param int $category_id
 * @param int $position
 * @param bool $link_category Se false, l'ultima categoria è solo testo (non usato di default).
 */
function dup_breadcrumb_echo_category_trail( $category_id, &$position, $link_category = true ) {
	$category_id = (int) $category_id;
	if ( ! $category_id ) {
		return;
	}

	$ancestors = array_reverse( get_ancestors( $category_id, 'category' ) );
	$chain     = array_merge( $ancestors, array( $category_id ) );

	foreach ( $chain as $cat_id ) {
		$cat = get_category( $cat_id );
		if ( ! $cat || is_wp_error( $cat ) ) {
			continue;
		}

		$is_last   = ( (int) $cat_id === $category_id );
		$with_link = $link_category || ! $is_last;
		?>
		<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="breadcrumb-item<?php echo ( $is_last && ! $link_category ) ? ' active' : ''; ?>">
			<span class="separator">/</span>
			<?php if ( $with_link ) : ?>
				<a class="text-primary" href="<?php echo esc_url( get_category_link( $cat ) ); ?>" itemprop="item">
					<span itemprop="name"><?php echo esc_html( $cat->name ); ?></span>
				</a>
			<?php else : ?>
				<span itemprop="item"><span itemprop="name"><?php echo esc_html( $cat->name ); ?></span></span>
			<?php endif; ?>
			<meta itemprop="position" content="<?php echo (int) $position; ?>" />
		</li>
		<?php
		$position++;
	}
}