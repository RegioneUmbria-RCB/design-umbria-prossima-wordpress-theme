<?php
  require_once get_template_directory() . '/inc/functions/common.php';
  require_once get_template_directory() . '/inc/functions/enqueue.php';
  require_once get_template_directory() . '/inc/origin-tema-comuni/cmb2.php';
  require_once get_template_directory() . '/inc/origin-tema-comuni/origin-core-functions.php';

  foreach (glob(get_template_directory() . '/inc/origin-tema-comuni/tipologie/*.php') as $file) {
    require_once $file;
  }
  foreach (glob(get_template_directory() . '/inc/origin-tema-comuni/tassonomie/*.php') as $file) {
    require_once $file;
  }

  require_once get_template_directory() . '/inc/functions/custom-post-type.php';
  require_once get_template_directory() . '/inc/functions/custom-taxonomy.php';

  require_once get_template_directory() . '/inc/functions/image-focal-point.php';
  require_once get_template_directory() . '/inc/functions/rating-service.php';

  function design_umbria_register_custom_image_sizes() {
    add_image_size('du-small', 150, 150, true);
    add_image_size('du-thumbnail', 300, 300, true);
    add_image_size('du-medium', 600, 600, false);
    add_image_size('du-large', 1200, 1200, false);
  }
  add_action('after_setup_theme', 'design_umbria_register_custom_image_sizes', 10);

  function design_umbria_normalize_image_sizes() {
    global $_wp_additional_image_sizes;
    if (!is_array($_wp_additional_image_sizes)) return;
    foreach ($_wp_additional_image_sizes as $name => &$size) {
      if (!is_array($size)) $size = array();
      if (!isset($size['width'])) $size['width'] = 0;
      if (!isset($size['height'])) $size['height'] = 0;
      if (!isset($size['crop'])) $size['crop'] = false;
    }
  }

  function design_umbria_normalize_image_metadata($data, $id) {
    if (!is_array($data) || empty($data['sizes'])) return $data;
    foreach (array_keys($data['sizes']) as $name) {
      if (!is_array($data['sizes'][$name])) continue;
      if (!isset($data['sizes'][$name]['width'])) $data['sizes'][$name]['width'] = 0;
      if (!isset($data['sizes'][$name]['height'])) $data['sizes'][$name]['height'] = 0;
    }
    return $data;
  }

  add_action('after_setup_theme', 'design_umbria_normalize_image_sizes', 9999);
  add_action('init', 'design_umbria_normalize_image_sizes', 9999);
  add_filter('wp_get_attachment_metadata', 'design_umbria_normalize_image_metadata', 10, 2);

  if (is_admin()) {
    add_action( 'admin_menu', 'rimuovi_menu_dci_options', 999 );

    function rimuovi_menu_dci_options() {
        remove_menu_page( 'dci_options' );
    }
    
    require_once get_template_directory() . '/inc/functions/custom-permissions.php';
    require_once get_template_directory() . '/inc/functions/update-origin-theme-dataset.php';
    require_once get_template_directory() . '/inc/functions/activation.php';
    require_once get_template_directory() . '/inc/functions/theme-setup.php';
    require_once get_template_directory() . '/inc/functions/image-focal-point.php';
    require_once get_template_directory() . '/inc/functions/image-create.php';

    foreach (glob(get_template_directory() . '/inc/functions/theme-setup-fields/*.php') as $file) {
      require_once $file;
    }
  }
  else{
    require_once get_template_directory() . '/inc/functions/image-render.php';
  }

  