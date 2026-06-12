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
            'class' => 'category-conditional-field category-post-type-select',
        ),
    ));

    $saved_options = get_option( 'category_metabox', array() );
    $saved_post_type = isset( $saved_options[ 'post_type_for_category_' . $category->term_id ] )
        ? (string) $saved_options[ 'post_type_for_category_' . $category->term_id ]
        : '';
    $saved_breadcrumb_term = isset( $saved_options[ 'breadcrumb_term_for_category_' . $category->term_id ] )
        ? (string) $saved_options[ 'breadcrumb_term_for_category_' . $category->term_id ]
        : '';
    $breadcrumb_term_options = dup_get_breadcrumb_term_options_for_post_type( $saved_post_type );

    $cmb->add_field(array(
        'name' => 'Tipologia per breadcrumb',
        'desc' => 'Opzionale. Scegli un Tipo di Documento oppure un Argomento: il breadcrumb di questa categoria si applica solo ai singoli contenuti con quel valore. Per le pagine archivio tassonomia usa la scheda Taxonomy Page. Lascia vuoto per tutti i contenuti del post type.',
        'id'   => 'breadcrumb_term_for_category_'.$category->term_id,
        'type' => 'select',
        'options' => $breadcrumb_term_options,
        'default' => $saved_breadcrumb_term,
        'attributes' => array(
            'data-category' => $category->term_id,
            'class' => 'category-conditional-field category-breadcrumb-term-select',
            'data-saved-value' => $saved_breadcrumb_term,
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
        'name'    => 'Livelli sottocategorie da mostrare',
        'desc'    => 'Si applica a header e griglia sottocategorie. «Tutti i livelli» mantiene il comportamento attuale. Se la categoria non ha figli, vengono mostrate le categorie sorelle (stesso livello).',
        'id'      => 'subcategories_depth_'.$category->term_id,
        'type'    => 'select',
        'options' => dup_get_category_subcategories_depth_options(),
        'default' => '',
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

add_action( 'admin_notices', 'dup_breadcrumb_category_conflict_notices' );
function dup_breadcrumb_category_conflict_notices() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'toplevel_page_impostazioni-template' !== $screen->id ) {
		return;
	}

	$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : '';
	if ( 'category' !== $tab ) {
		return;
	}

	$conflicts = array_merge(
		dup_detect_breadcrumb_category_conflicts(),
		dup_detect_breadcrumb_taxonomy_conflicts()
	);
	if ( empty( $conflicts ) ) {
		return;
	}

	echo '<div class="notice notice-warning"><p><strong>' . esc_html__( 'Attenzione — conflitti breadcrumb', 'design_umbria_prossima' ) . '</strong></p><ul style="list-style:disc;margin-left:1.5em;">';
	foreach ( $conflicts as $message ) {
		echo '<li>' . esc_html( $message ) . '</li>';
	}
	echo '</ul></div>';
}

add_action('admin_footer', 'category_metabox_scripts');
function category_metabox_scripts() {
  $screen = function_exists('get_current_screen') ? get_current_screen() : null;
  if (!$screen || 'toplevel_page_impostazioni-template' !== $screen->id) {
    return;
  }

  $tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : '';
  if ( 'category' !== $tab ) {
    return;
  }

  $terms_map = dup_get_breadcrumb_terms_map_for_admin();
  ?>
  <script>
    window.dupCategoryBreadcrumbTerms = <?php echo wp_json_encode( $terms_map ); ?>;
  </script>
  <script>
    jQuery(document).ready(function($) {
      const postTypeSelect = $('#category_post_type');
      const defaultLabel = <?php echo wp_json_encode( __( 'Tutti i contenuti del post type', 'design_umbria_prossima' ) ); ?>;

      function rebuildBreadcrumbTermSelect($termSelect, postType, savedValue) {
        const $row = $termSelect.closest('.cmb-row');
        const groups = (window.dupCategoryBreadcrumbTerms && window.dupCategoryBreadcrumbTerms[postType]) || [];
        let hasOptions = false;

        $termSelect.empty();
        $termSelect.append($('<option>', { value: '', text: defaultLabel }));

        groups.forEach(function(group) {
          if (!group.options || !group.options.length) {
            return;
          }
          hasOptions = true;
          const $optgroup = $('<optgroup>').attr('label', group.label);
          group.options.forEach(function(option) {
            $optgroup.append($('<option>', { value: option.value, text: option.label }));
          });
          $termSelect.append($optgroup);
        });

        if (savedValue && $termSelect.find('option[value="' + savedValue + '"]').length) {
          $termSelect.val(savedValue);
        } else {
          $termSelect.val('');
        }

        if (postType && hasOptions) {
          $row.show();
        } else {
          $termSelect.val('');
          $row.hide();
        }
      }

      function syncBreadcrumbTermForCategory(categoryId) {
        const $postTypeField = $('.category-post-type-select[data-category="' + categoryId + '"]');
        const $termField = $('.category-breadcrumb-term-select[data-category="' + categoryId + '"]');
        if (!$postTypeField.length || !$termField.length) {
          return;
        }

        const postType = $postTypeField.val();
        const savedValue = $termField.data('saved-value') || $termField.val() || '';
        rebuildBreadcrumbTermSelect($termField, postType, savedValue);
      }

      function toggleCategoryFields(selectedValue) {
        $('#cmb2-metabox-category_metabox .cmb-repeatable-group').hide();
        $('#cmb2-metabox-category_metabox #post_list_group_'+selectedValue+'_repeat').show();

        $('#cmb2-metabox-category_metabox .category-conditional-field').closest('.cmb-row').hide();
        $('#cmb2-metabox-category_metabox .category-conditional-field[data-category="'+selectedValue+'"]').closest('.cmb-row').show();

        syncBreadcrumbTermForCategory(selectedValue);
      }

      $(document).on('change', '.category-post-type-select', function() {
        const categoryId = $(this).data('category');
        const $termField = $('.category-breadcrumb-term-select[data-category="' + categoryId + '"]');
        $termField.data('saved-value', '');
        rebuildBreadcrumbTermSelect($termField, $(this).val(), '');
      });

      postTypeSelect.on('change', function() {
        toggleCategoryFields($(this).val());
      });

      toggleCategoryFields(postTypeSelect.val());
    });
  </script>
  <?php
}
