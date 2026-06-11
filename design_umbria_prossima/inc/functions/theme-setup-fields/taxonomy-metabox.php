<?php
require_once get_template_directory() . '/inc/functions/theme-setup-fields/components/post-list.php';

// Creazione metabox per le tassonomie
add_action('cmb2_admin_init', 'taxonomy_metabox');
function taxonomy_metabox() {
    $cmb = new_cmb2_box(array(
        'id'           => 'taxonomy_metabox',
        'title'        => 'Impostazioni Sezioni Tassonomie',
        'object_types' => array('options-page'),
        'option_key'   => 'taxonomy_metabox',
        'context'      => 'normal',
        'priority'     => 'high',
    ));

    $taxonomies       = get_taxonomies(array('public' => true), 'objects');
    $taxonomy_options = array();

    // Creazione select principale
    foreach ($taxonomies as $taxonomy) {
        if (in_array($taxonomy->name, array('category', 'post_tag', 'nav_menu', 'link_category', 'post_format'), true)) {
            continue;
        }

        $terms = get_terms(array(
            'taxonomy'   => $taxonomy->name,
            'hide_empty' => false,
        ));

        if (!empty($terms) && !is_wp_error($terms)) {
            $term_labels = array();
            get_hierarchical_term_options( $terms, $term_labels, 0, '', $taxonomy->name . '_' );
            foreach ( $term_labels as $option_key => $label ) {
                $taxonomy_options[ $option_key ] = $taxonomy->labels->singular_name . ' — ' . $label;
            }
        }
    }

    $cmb->add_field(array(
        'name'       => 'Seleziona Tassonomia',
        'id'         => 'taxonomy_post_type',
        'type'       => 'select',
        'options'    => $taxonomy_options,
        'attributes' => array(
            'class' => 'cmb2-post-type-select',
        ),
    ));

    $wp_categories = get_categories( array( 'hide_empty' => false ) );
    $breadcrumb_category_options = array( '' => __( 'Nessun prefisso categoria', 'design_umbria_prossima' ) );
    get_hierarchical_term_options( $wp_categories, $breadcrumb_category_options );

    // Creazione campi per ogni termine
    foreach ($taxonomies as $taxonomy) {
        if (in_array($taxonomy->name, array('category', 'post_tag', 'nav_menu', 'link_category', 'post_format'), true)) {
            continue;
        }

        $terms = get_terms(array(
            'taxonomy'   => $taxonomy->name,
            'hide_empty' => false,
        ));

        if (!empty($terms) && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                $field_prefix = $term->taxonomy . '_' . $term->term_id;

                $cmb->add_field(array(
                    'name' => 'Abilita breadcrumb',
                    'desc' => __( 'Se attivo: in archivio mostra Home, eventuale prefisso categoria, genitori del termine e il termine; sui contenuti taggati con questo termine usa lo stesso percorso prima del titolo.', 'design_umbria_prossima' ),
                    'id'   => 'breadcrumb_enable_' . $field_prefix,
                    'type' => 'checkbox',
                    'attributes' => array(
                        'data-taxonomy' => $field_prefix,
                        'class'         => 'taxonomy-conditional-field',
                    ),
                ));

                $cmb->add_field(array(
                    'name'       => 'Prefisso categoria (opzionale)',
                    'desc'       => __( 'Categorie WordPress da mostrare prima della gerarchia del termine.', 'design_umbria_prossima' ),
                    'id'         => 'breadcrumb_category_' . $field_prefix,
                    'type'       => 'select',
                    'options'    => $breadcrumb_category_options,
                    'attributes' => array(
                        'data-taxonomy' => $field_prefix,
                        'class'         => 'taxonomy-conditional-field taxonomy-breadcrumb-prefix-field',
                    ),
                ));

                $cmb->add_field(array(
                    'name'       => 'Attiva Alert Tassonomia',
                    'id'         => 'alert_enable_' . $field_prefix,
                    'type'       => 'checkbox',
                    'attributes' => array(
                        'data-taxonomy' => $field_prefix,
                        'class'         => 'taxonomy-conditional-field',
                    ),
                ));

                $cmb->add_field(array(
                    'name'       => 'Tipo di Alert',
                    'id'         => 'alert_type_' . $field_prefix,
                    'type'       => 'select',
                    'options'    => array(
                        'success' => 'Conferma',
                        'danger'  => 'Pericolo',
                        'warning' => 'Avviso',
                        'info'    => 'Informazione',
                    ),
                    'attributes' => array(
                        'data-taxonomy' => $field_prefix,
                        'class'         => 'taxonomy-conditional-field',
                    ),
                ));

                $cmb->add_field(array(
                    'name'       => 'Messaggio Alert',
                    'id'         => 'alert_message_' . $field_prefix,
                    'type'       => 'textarea',
                    'attributes' => array(
                        'data-taxonomy' => $field_prefix,
                        'class'         => 'taxonomy-conditional-field',
                    ),
                    'options'       => array(
                        'textarea_rows' => 5,
                    ),
                    'media_buttons' => true,
                ));

                add_post_list_metabox_fields($cmb, '_' . $field_prefix);
            }
        }
    }
}

add_action( 'admin_notices', 'dup_breadcrumb_taxonomy_conflict_notices' );
function dup_breadcrumb_taxonomy_conflict_notices() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'toplevel_page_impostazioni-template' !== $screen->id ) {
		return;
	}

	$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : '';
	if ( 'taxonomy' !== $tab ) {
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

// JS per mostrare/nascondere campi per termine
add_action('admin_footer', 'taxonomy_metabox_scripts');
function taxonomy_metabox_scripts() {
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || 'toplevel_page_impostazioni-template' !== $screen->id) {
        return;
    }

    $tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : '';
    if ( 'taxonomy' !== $tab ) {
        return;
    }
    ?>
    <script>
    jQuery(document).ready(function($) {
        const postTypeSelect = $('#taxonomy_post_type');

        function showSelectedGroup(selectedValue) {
            // Nascondo tutte le group repeatable
            $('#cmb2-metabox-taxonomy_metabox .cmb-repeatable-group').hide();

            // Mostro solo il gruppo corretto (se esiste)
            $('#cmb2-metabox-taxonomy_metabox #post_list_group_' + selectedValue + '_repeat').show();

            // Nascondo tutte le checkbox/fields tassonomia-specifiche
            $('#cmb2-metabox-taxonomy_metabox .taxonomy-conditional-field').closest('.cmb-row').hide();

            // Mostro solo quelle della tassonomia selezionata
            $('#cmb2-metabox-taxonomy_metabox .taxonomy-conditional-field[data-taxonomy="'+selectedValue+'"]').closest('.cmb-row').show();
        }

        postTypeSelect.on('change', function() {
            showSelectedGroup($(this).val());
        });

        // Stato iniziale
        showSelectedGroup(postTypeSelect.val());
    });
    </script>
    <?php
}
