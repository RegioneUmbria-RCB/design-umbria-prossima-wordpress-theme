<?php 
if($args && isset($args['card_type']) && isset($args['items']) && !empty($args['card_type']) && !empty($args['items'])){
  $card_type = $args['card_type'];
  $items = $args['items'];
  $is_post = $args['is_post'];
?>
<div class="px-3 px-md-4 px-lg-5 it-carousel-wrapper it-carousel-landscape-abstract-three-cols splide" 
     data-bs-carousel-splide 
     data-splide='{"height":"auto", "gap":".5rem", "breakpoints":{"768":{"gap":".5rem"}, "992":{"gap":".5rem"}}}'>
  <div class="splide__track">
    <ul class="splide__list">
      <?php  
        if($is_post){
          while ( $items->have_posts() ){
            $items->the_post();
            ?>
              <li class="splide__slide d-flex">
                <?php get_template_part('template-parts/loop/'.$card_type, null, get_post());?>
              </li>
            <?php
          }
        }
        else{
          foreach ($items as $item) {
            ?>
              <li class="splide__slide d-flex">
                <?php get_template_part('template-parts/loop/'.$card_type, null, $item);?>
              </li>
            <?php
          } 
        }
      ?>
    </ul>
  </div>
</div>
<?php } ?>
