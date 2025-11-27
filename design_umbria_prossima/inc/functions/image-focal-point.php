<?php

add_action('admin_head', 'focal_point_styles');

function focal_point_styles() {
  echo '<style>
    .focal-point-modal {
  display: none;
  position: fixed;
  z-index: 1000000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
}

.focal-point-modal-content {
  background-color: #fff;
  margin: 5% auto;
  padding: 20px;
  width: fit-content;
  position: relative;
}
#focal-point-image{
  max-height:60vh
}
.close-modal {
  position: absolute;
  top: 10px;
  right: 15px;
  font-size: 24px;
  cursor: pointer;
}

.image-container {
  position: relative;
}

#focal-point-marker {
  position: absolute;
  width: 30px;
  height: 30px;
  transform:translate(-50%,-50%);
  background-color: red;
  border-radius: 50%;
  pointer-events: none;
}
  </style>';
}
function add_focal_point_button($form_fields, $post) {
  $image = wp_get_attachment_image_src($post->ID, 'full');
  $axes = get_post_meta($post->ID, 'focal_point', true);
 
  $form_fields['focal_point'] = array(
      'label' => 'Punto Focale',
      'input' => 'html',
      'html'  => '<button onclick="openFocalPointModal('.$post->ID.', \''.esc_url($image[0]).'\', '.htmlspecialchars(json_encode($axes), ENT_QUOTES, 'UTF-8').');" type="button" class="button button-primary" id="open-focal-point-modal">Seleziona Punto Focale</button>'
  );
  return $form_fields;
}
add_filter('attachment_fields_to_edit', 'add_focal_point_button', 10, 2);

function enqueue_focal_point_script($hook) {
    if ($hook === 'upload.php' || $hook === 'post.php') {
        wp_enqueue_script(
            'focal-point',
            get_template_directory_uri() . '/inc/admin-js/focal-point.js',
            array('jquery'),
            null,
            true
        );

        wp_localize_script(
            'focal-point',
            'focalPointVars',
            array(
                'ajaxurl' => admin_url('admin-ajax.php')
            )
        );
    }
}
add_action('admin_enqueue_scripts', 'enqueue_focal_point_script');

function save_focal_point() {
  if (isset($_POST['x']) && isset($_POST['y']) && isset($_POST['post_id'])) {
      $x = intval($_POST['x']);
      $y = intval($_POST['y']);
      $post_id = intval($_POST['post_id']);

      $success = update_post_meta($post_id, 'focal_point', array('x' => $x, 'y' => $y));
      
      if ($success) {
        wp_send_json_success(array('message' => 'Punto focale salvato.', 'post_id' => $post_id));
      } else {
          wp_send_json_error(array('message' => 'Update fallito.'));
      }
  } else {
      wp_send_json_error(array('message' => 'Dati mancanti.'));
  }
}
add_action('wp_ajax_save_focal_point', 'save_focal_point');
?>