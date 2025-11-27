<?php
get_header();
$metabox = get_option('homepage_metabox');
$search_quick_links = $metabox['quick_links_group'] ?? [];
$search_images = $metabox['homepage_images_group'] ?? [];
$alert_active      = $metabox['alert_enable'] ?? null;
$alert_type        = $metabox['alert_type'] ?? null;
$alert_message     = $metabox['alert_message'] ?? "";

if(!empty($metabox['search_active']) && $metabox['search_active'] == "on"){
  get_template_part('template-parts/hero', null, array('quick_links'=>$search_quick_links, 'images'=>$search_images));
}

if($alert_active == 'on'){
  echo '<div class="py-4 container">';
    get_template_part('template-parts/alert-component', null, ['active'=>$alert_active, 'type'=>$alert_type, 'message'=>$alert_message]);
  echo '</div>';
}

if(!empty($metabox["post_list_group"])){
  echo '<div class="repeater-home">';
  foreach ($metabox["post_list_group"] as $args){
?>
      <div>
        <div class="container">
          <?php get_template_part('template-parts/post-list-component', null, $args); ?>
        </div>
      </div>
<?php 
  }
  echo '</div>';
}
get_footer();