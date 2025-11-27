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