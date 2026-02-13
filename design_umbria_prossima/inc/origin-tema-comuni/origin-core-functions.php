<?php
  function jsonToArray($filename) {
      $strJsonFileContents = file_get_contents($filename);
      // Convert to array
      return json_decode($strJsonFileContents, true);
  }

  define('COMUNI_PAGINE',jsonToArray(get_template_directory()."/inc/origin-tema-comuni/comuni_pagine.json")['pagine']);
  define('COMUNI_TIPOLOGIE',jsonToArray(get_template_directory()."/inc/origin-tema-comuni/comuni_tipologie.json")['tipologie']);

  /**
   * Restituisce la descrizione da mostrare per una categoria: se in Impostazioni template > Category Page
   * è impostata una descrizione per questa categoria, usa quella; altrimenti la descrizione del term.
   *
   * @param WP_Term $term Termine (categoria).
   * @return string
   */
  function get_category_page_description( $term ) {
    $opts = get_option( 'category_metabox', array() );
    $key  = 'description_' . $term->term_id;
    if ( ! empty( $opts[ $key ] ) ) {
      return $opts[ $key ];
    }
    return $term->description ?? '';
  }

  function dci_get_tipologie_related_to_taxonomy($taxonomy) {
        $result = array();
        foreach (COMUNI_TIPOLOGIE as $tipologia) {
                if (array_key_exists('taxonomy', $tipologia) && is_array($tipologia['taxonomy']) && in_array($taxonomy,$tipologia['taxonomy'])){
                        $result[] = $tipologia['name'];
                }
        }
        return $result;
    }

  function dci_get_sercheable_tipologie() {
    $arrayTipologie = array(
        'unita_organizzativa',
        'evento',
        'luogo',
        'documento_pubblico',
        'notizia',
        'servizio',
        'persona_pubblica',
        'dataset',
        'page',
        'post'
    );
    if ( post_type_exists( 'amm-trasparente' ) ) { // Compatibilità plugin amministrazione-trasparente
        $arrayTipologie[] = 'amm-trasparente';
    }
    return $arrayTipologie;
  }


  function dci_get_tipologie_capabilities(){
    return array_column(COMUNI_TIPOLOGIE, 'capability', 'name');
  }

  function dci_get_tassonomie_names(){
    $tassonomie = array(
        'categorie_servizio',
        'tipi_evento',
        'tipi_notizia',
        'tipi_luogo',
        'argomenti',
        'tipi_unita_organizzativa',
        'licenze',
        'frequenze_aggiornamento',
        'temi_dataset',
        'tipi_punto_contatto',
        'tipi_doc_albo_pretorio',
        'eventi_vita_persone',
        'eventi_vita_impresa',
        'tipi_incarico',
        'stati_pratica',
        'tipi_documento'
    );
    return $tassonomie;
    }

  function createCapabilities() {

    $admins = get_role( 'administrator' );

    $custom_types = dci_get_tipologie_capabilities(); //nomi plurali dei custom post type
    $custom_types [] = 'ratings'; //aggiungo capability post type sistema di valutazione
    $custom_types [] = 'richieste_assistenza'; //aggiungo capability post type richiesta assistenza

    $caps = array("edit_","edit_others_","publish_","read_private_","delete_","delete_private_","delete_published_","delete_others_","edit_private_","edit_published_");
    foreach ($custom_types as $custom_type){
        foreach ($caps as $cap){
            $admins->add_cap( $cap.$custom_type);
        }
    }
    $custom_tax = dci_get_tassonomie_names(); //array contenente i nomi delle tassonomie custom
    $custom_tax [] = 'stars'; //aggiungo tassonomia sistema di valutazione
    $custom_tax [] = 'page_urls'; //aggiungo tassonomia sistema di valutazione

    $caps_terms = array("manage_","edit_","delete_","assign_");
    foreach ($custom_tax as $ctax){
        foreach ($caps_terms as $cap){
            $admins->add_cap( $cap.$ctax);
        }
    }
    // members cap for multisite
    $admins->add_cap( "create_roles");
    $admins->add_cap( "edit_roles");
    $admins->add_cap( "delete_roles");
}

function dci_members_can_user_view_post($user_id, $post_id) {
    if(!function_exists("members_can_user_view_post")) {
        return true;
    }else{
        return members_can_user_view_post($user_id, $post_id);
    }

}

function dci_register_main_options_metabox (){
    foreach (glob(get_template_directory() . "/inc/origin-tema-comuni/options/*.php") as $file) {
        // Ottieni le funzioni definite PRIMA dell'inclusione del file
        $before = get_defined_functions()['user'];

        // Include il file
        require_once $file;

        // Ottieni le funzioni definite DOPO l'inclusione
        $after = get_defined_functions()['user'];

        // Calcola la differenza: le nuove funzioni definite dal file
        $new_functions = array_diff($after, $before);

        // Se ne è stata definita almeno una, chiama la prima
        if (!empty($new_functions)) {
            // Prendi la prima funzione definita
            $function_to_call = reset($new_functions);
            
            if (function_exists($function_to_call)) {
                $function_to_call();
            }
        }
    }
}

function dci_get_tipologie_prefixes(){
    return array_column(COMUNI_TIPOLOGIE, 'prefix', 'name');
}

if(!function_exists("dci_get_meta")){
	function dci_get_meta( $key = '', $prefix = "", $post_id = "") {
        if ( ! dci_members_can_user_view_post(get_current_user_id(), $post_id) ) return false;

		if($post_id == "")
			$post_id = get_the_ID();

		$post_type = get_post_type($post_id);

		if($prefix != "")
			return get_post_meta( $post_id, $prefix . $key, true );

		$prefixes = dci_get_tipologie_prefixes();
		foreach ($prefixes as $name => $prefix){
            if (is_singular($name)  || (isset($post_type) && $post_type == $name)) {
                return get_post_meta( $post_id, $prefix . $key, true );
            }
        }
		return get_post_meta( $post_id, $key, true );
	}
}

if(!function_exists("dci_get_data_pubblicazione_arr")) {
    function dci_get_data_pubblicazione_arr($key = '', $prefix = '', $post_id = null) {
        global $post;
        $arrdata = array();
        if (!$post) $post = get_post($post_id);

        $data_pubblicazione = dci_get_meta($key, $prefix , $post_id);
        if (!$data_pubblicazione) {
            $data_pubblicazione = explode(' ',$post->post_date)[0];
            $arrdata =  array_reverse(explode("-", $data_pubblicazione));
        } else {
            $arrdata =  explode("-", date('d-m-y',$data_pubblicazione));  
        }
        return $arrdata;
    }
}

if(!function_exists("dci_get_wysiwyg_field")) {
    function dci_get_wysiwyg_field($key = '', $prefix = "", $post_id = "") {
        return wpautop(dci_get_meta ($key,$prefix,$post_id));
    }
}

/**
 * Restituisce le dimensioni immagine disponibili per select/radio (slug => etichetta).
 */
if ( ! function_exists( 'dci_get_available_image_sizes_for_select' ) ) {
	function dci_get_available_image_sizes_for_select() {
		$labels = array(
			'thumbnail'     => __( 'Miniatura', 'design_comuni_italia' ),
			'medium'        => __( 'Media', 'design_comuni_italia' ),
			'medium_large'  => __( 'Media grande', 'design_comuni_italia' ),
			'large'         => __( 'Grande', 'design_comuni_italia' ),
			'full'          => __( 'Originale', 'design_comuni_italia' ),
			'du-small'      => __( 'DU Piccola (150×150)', 'design_comuni_italia' ),
			'du-thumbnail'  => __( 'DU Miniatura (300×300)', 'design_comuni_italia' ),
			'du-medium'     => __( 'DU Media (600×600)', 'design_comuni_italia' ),
			'du-large'      => __( 'DU Grande (1200×1200)', 'design_comuni_italia' ),
		);
		$sizes   = get_intermediate_image_sizes();
		$options = array();
		foreach ( $sizes as $size ) {
			$options[ $size ] = isset( $labels[ $size ] ) ? $labels[ $size ] : ucfirst( str_replace( array( '-', '_' ), ' ', $size ) );
		}
		$options['full'] = $labels['full'];
		return $options;
	}
}

function getFileSizeAndFormat($url) {
    $percorso = parse_url($url);
    $percorso = isset($percorso["path"]) ? substr($percorso["path"], 0, -strlen(pathinfo($url, PATHINFO_BASENAME))) : '';
    $response = wp_remote_head($url);

    if (is_wp_error($response)) {
        return 'Errore nel recupero delle informazioni del file';
    }

    $headers = wp_remote_retrieve_headers($response);
    $content_length = isset($headers['content-length']) ? intval($headers['content-length']) : 0;

    $base = log($content_length, 1024);
    $suffixes = array('', 'Kb', 'Mb', 'Gb', 'Tb');
    $size_formatted = round(pow(1024, $base - floor($base)), 2) . ' ' . $suffixes[floor($base)];

    $info_file = pathinfo($url);
    $file_format = strtoupper(isset($info_file['extension']) ? $info_file['extension'] : '');

    return $file_format . ' ' . $size_formatted;
}