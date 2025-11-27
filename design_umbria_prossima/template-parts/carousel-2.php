<?php 
if($args && isset($args['card_type']) && isset($args['items']) && !empty($args['card_type']) && !empty($args['items'])){
  $card_type = $args['card_type'];
  $items = $args['items'];
  $is_post = $args['is_post'];
?>
<div class="px-3 px-md-0">
  <div class="it-carousel-wrapper splide it-carousel-landscape-abstract-three-cols-arrow-visible" 
       data-bs-carousel-splide 
       data-splide='{"height":"auto"}'>
    <div class="splide__track pt-0">
      <ul class="splide__list">
        <?php  
          if($is_post){
            while ( $items->have_posts() ){
              $items->the_post();
              ?>
                <li class="splide__slide lined_slide d-flex">
                  <?php get_template_part('template-parts/loop/'.$card_type, null, get_post());?>
                </li>
              <?php
            }
          }
          else{
            foreach ($items as $item) {
              ?>
                <li class="splide__slide lined_slide d-flex">
                  <?php get_template_part('template-parts/loop/'.$card_type, null, $item);?>
                </li>
              <?php
            } 
          }
        ?>
      </ul>
    </div>
  </div>
</div>
<?php } ?>