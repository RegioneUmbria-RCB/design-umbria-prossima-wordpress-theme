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