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

  