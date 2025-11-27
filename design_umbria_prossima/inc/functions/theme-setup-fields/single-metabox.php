<?php
require_once get_template_directory() . '/inc/functions/theme-setup-fields/components/post-list.php';
add_action('cmb2_admin_init', 'single_metabox');
function single_metabox() {
  $cmb = new_cmb2_box(array(
    'id'           => 'single_metabox',
    'title'        => 'Impostazioni Sezioni Single Page',
    'object_types' => array('options-page'),
    'option_key'   => 'single_metabox',
    'context'      => 'normal',
    'priority'     => 'high',
  ));

  $post_types = get_post_types( array( 'public' => true ), 'objects' );
  $post_type_options = array();
  foreach ( $post_types as $post_type ) {
      if ( 'attachment' !== $post_type->name ) {
          $post_type_options[ $post_type->name ] = $post_type->labels->singular_name;
      }
  }

  $cmb->add_field(array(
        'name'       => 'Tipologia di Post',
        'id'         => 'single_post_type',
        'type'       => 'select',
        'options'    => $post_type_options,
        'attributes' => array(
            'class' => 'cmb2-post-type-select',
        ),
  ) );

  foreach ( $post_types as $post_type ) {
      if ( 'attachment' !== $post_type->name ) {
          add_post_list_metabox_fields($cmb, '_'.$post_type->name, " da mostrare in fondo alla pagina");
      }
  }
}

add_action('admin_footer', 'single_metabox_scripts');
function single_metabox_scripts() {
  ?>
  <script>
    jQuery(document).ready(function($) {
      const postTypeSelect = $('#single_post_type');

      postTypeSelect.on('change', function() {
        const selectedValue = $(this).val();
        

        $('#cmb2-metabox-single_metabox .cmb-repeatable-group').hide();
        $('#cmb2-metabox-single_metabox #post_list_group_'+selectedValue+'_repeat').show();

      });

      const initialValue = postTypeSelect.val();
      $('#cmb2-metabox-single_metabox .cmb-repeatable-group').hide();
      $('#cmb2-metabox-single_metabox #post_list_group_'+initialValue+'_repeat').show();
    });
  </script>
  <?php
}