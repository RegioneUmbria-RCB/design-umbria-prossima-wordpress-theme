<?php
function theme_enqueue() {
    wp_enqueue_script(
        'bootstrap-italia-js',
        get_template_directory_uri() . '/inc/js/bootstrap-italia.js',
        array(),
        null,
        true
    );
    wp_enqueue_script(
        'comuni-js',
        get_template_directory_uri() . '/inc/js/comuni.js',
        array(),
        null,
        true
    );
    wp_enqueue_script(
        'leaflet-js',
        get_template_directory_uri() . '/inc/origin-tema-comuni/lib/leaflet/leaflet.js',
        array(),
        null,
        true
    );
    wp_enqueue_script(
        'geocoder-js',
        get_template_directory_uri() . '/inc/js/geocoder.js',
        array(),
        null,
        true
    );


    wp_enqueue_style(
        'bootstrap-italia-css',
        get_template_directory_uri() . '/inc/origin-tema-comuni/bootstrap-italia/css/bootstrap-italia.min.css'
    );
    wp_enqueue_style(
        'bootstrap-italia-comuni-css',
        get_template_directory_uri() . '/inc/origin-tema-comuni/css/bootstrap-italia-comuni.min.css'
    );
    wp_enqueue_style(
        'theme-css',
        get_template_directory_uri() . '/style.css'
    );
    wp_enqueue_style(
        'leaflet-css',
        get_template_directory_uri() . '/inc/origin-tema-comuni/lib/leaflet/leaflet.css'
    );
  }

  add_action('wp_enqueue_scripts', 'theme_enqueue');


  function enqueue_scripts_admin($hook) {
    wp_enqueue_script(
        'cmb2-dynamic-posts',
        get_template_directory_uri() . '/inc/js/cmb2-dynamic-posts.js',
        [],
        null,
        true
    );

    wp_localize_script('cmb2-dynamic-posts', 'cmb2_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('cmb2_get_posts_nonce'),
    ]);

    wp_enqueue_style(
        'custom-css',
        get_template_directory_uri() . '/style-admin.css'
    );
  }
  
  add_action('admin_enqueue_scripts', 'enqueue_scripts_admin');