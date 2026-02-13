<?php
require_once get_template_directory() . '/inc/functions/theme-setup-fields/components/post-list.php';

add_action('cmb2_admin_init', 'category_metabox');
function category_metabox() {
  $cmb = new_cmb2_box(array(
    'id'           => 'category_metabox',
    'title'        => 'Impostazioni Sezioni Categorie',
    'object_types' => array('options-page'),
    'option_key'   => 'category_metabox',
    'context'      => 'normal',
    'priority'     => 'high',
  ));

  $categories         = get_categories( array( 'hide_empty' => false ) );
  $categories_options = array();
  get_hierarchical_term_options( $categories, $categories_options);

  // Select principale per scegliere la categoria
  $cmb->add_field(array(
        'name'       => 'Categoria',
        'id'         => 'category_post_type',
        'type'       => 'select',
        'options'    => $categories_options,
        'attributes' => array(
            'class' => 'cmb2-post-type-select',
        ),
  ) );

  // Loop su tutte le categorie
  foreach ( $categories as $category ) {

    $post_types = get_post_types( array(
        'public' => true,
    ), 'objects' );

    $post_types_options = array( '' => 'Seleziona post type' );
    foreach ( $post_types as $pt_slug => $pt_obj ) {
        $post_types_options[$pt_slug] = $pt_obj->labels->singular_name;
    }

    $cmb->add_field(array(
        'name' => 'Post Type associato',
        'id'   => 'post_type_for_category_'.$category->term_id,
        'type' => 'select',
        'options' => $post_types_options,
        'attributes' => array(
            'data-category' => $category->term_id,
            'class' => 'category-conditional-field',
        ),
    ));

    $cmb->add_field(array(
        'name' => 'Descrizione',
        'id'   => 'description_'.$category->term_id,
        'type' => 'textarea',
        'desc' => 'Testo mostrato sotto il titolo nella pagina di questa categoria. Se vuoto viene usata la descrizione della categoria.',
        'attributes' => array(
            'data-category' => $category->term_id,
            'class' => 'category-conditional-field',
        ),
        'options' => array(
            'textarea_rows' => 4,
        ),
    ));
    
    // Checkbox per ogni categoria
    $cmb->add_field(array(
        'name' => 'Mostra sottocategorie nell\'header',
        'id'   => 'show_subcategories_header_'.$category->term_id,
        'type' => 'checkbox',
        'attributes' => array(
            'data-category' => $category->term_id,
            'class' => 'category-conditional-field',
        ),
    ));
    $cmb->add_field(array(
        'name' => 'Mostra sottocategorie in pagina',
        'id'   => 'show_subcategories_page_'.$category->term_id,
        'type' => 'checkbox',
        'attributes' => array(
            'data-category' => $category->term_id,
            'class' => 'category-conditional-field',
        ),
    ));
    $cmb->add_field(array(
        'name' => 'Mostra elenco contenuti',
        'id'   => 'show_content_list_'.$category->term_id,
        'type' => 'checkbox',
        'attributes' => array(
            'data-category' => $category->term_id,
            'class' => 'category-conditional-field',
        ),
    ));
    $cmb->add_field(array(
        'name' => 'Mostra filtri contenuti',
        'id'   => 'show_content_filters_'.$category->term_id,
        'type' => 'checkbox',
        'attributes' => array(
            'data-category' => $category->term_id,
            'class' => 'category-conditional-field',
        ),
    ));
    $cmb->add_field(array(
        'name' => 'Mostra ricerca contenuti',
        'id'   => 'show_content_search_'.$category->term_id,
        'type' => 'checkbox',
        'attributes' => array(
            'data-category' => $category->term_id,
            'class' => 'category-conditional-field',
        ),
    ));

    $cmb->add_field( array(
        'name' => 'Attiva Alert Categoria',
        'id'   => 'alert_enable_'.$category->term_id,
        'type' => 'checkbox',
        'attributes' => array(
            'data-category' => $category->term_id,
            'class' => 'category-conditional-field',
        ),
    ) );

    $cmb->add_field( array(
        'name'    => 'Tipo di Alert',
        'id'      => 'alert_type_'.$category->term_id,
        'type'    => 'select',
        'options' => array(
            'success' => 'Conferma',
            'danger'  => 'Pericolo',
            'warning' => 'Avviso',
            'info'    => 'Informazione',
        ),
        'attributes' => array(
            'data-category' => $category->term_id,
            'class' => 'category-conditional-field',
        ),
    ) );

    $cmb->add_field( array(
        'name' =>'Messaggio Alert',
        'id'   => 'alert_message_'.$category->term_id,
        'type' => 'textarea',
        'options' => array(
            'media_buttons' => true,
            'textarea_rows' => 5,
        ),
        'attributes' => array(
            'data-category' => $category->term_id,
            'class' => 'category-conditional-field',
        ),
    ) );

    // Richiamo ai repeater che avevi già
    add_post_list_metabox_fields($cmb, '_'.$category->term_id);
  }
}

add_action('admin_footer', 'category_metabox_scripts');
function category_metabox_scripts() {
  ?>
  <script>
    jQuery(document).ready(function($) {
      const postTypeSelect = $('#category_post_type');

      function toggleCategoryFields(selectedValue) {
        // nascondo tutti i repeater
        $('#cmb2-metabox-category_metabox .cmb-repeatable-group').hide();
        // mostro solo quello della categoria selezionata
        $('#cmb2-metabox-category_metabox #post_list_group_'+selectedValue+'_repeat').show();

        // nascondo tutte le checkbox categoria-specifiche
        $('#cmb2-metabox-category_metabox .category-conditional-field').closest('.cmb-row').hide();
        // mostro solo quelle della categoria scelta
        $('#cmb2-metabox-category_metabox .category-conditional-field[data-category="'+selectedValue+'"]').closest('.cmb-row').show();
      }

      postTypeSelect.on('change', function() {
        toggleCategoryFields($(this).val());
      });

      // init allo start
      toggleCategoryFields(postTypeSelect.val());
    });
  </script>
  <?php
}
