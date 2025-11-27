<?php 
if($args && isset($args['card_type']) && isset($args['items']) && !empty($args['card_type']) && !empty($args['items'])){
  $card_type = $args['card_type'];
  $items = $args['items'];
  $is_post = $args['is_post'];

?>
  <div class="row gy-3">
  <?php  
    if($is_post){
      while ( $items->have_posts() ){
        $items->the_post();
        ?>
          <div class="col-12 col-md-6 col-lg-4 d-flex">
            <?php get_template_part('template-parts/loop/'.$card_type, null, get_post());?>
          </div>
        <?php
      }
    }
    else{
      foreach ($items as $item) {
        ?>
          <div class="col-12 col-md-6 col-lg-4 d-flex">
            <?php get_template_part('template-parts/loop/'.$card_type, null, $item);?>
          </div>
        <?php
      } 
    }
  ?>
  </div>
<?php } ?>