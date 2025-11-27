<?php
  $images = $args['images'];
  if(!empty($images)){
    $randomKey = array_rand($images);
    $randomImage = $images[$randomKey];
  }
?>
<section class="home-hero it-hero-wrapper it-dark it-overlay">
  <!-- - img-->
  <div class="img-responsive-wrapper">
    <div class="img-responsive">
        <?php 
          if(isset($randomImage) && !empty($randomImage) && !empty($randomImage['image_id'])):?>
            <div class="img-wrapper">
              <?php
                echo wp_get_attachment_image(
                    intval( $randomImage['image_id'] ),
                    'large',
                    false,
                    array(
                      'class' => 'attachment-item-thumb size-item-thumb',
                      'alt'   => esc_attr($randomImage['title']) ?? "Immagine sfondo pannello di ricerca",
                    )
                );
              ?>
            </div>
        <?php endif;?>
    </div>
  </div>
  <!-- - texts-->
  <div class="container">
    <form class="row" action="/search/">
        <div class="col-12 position-relative d-grid gap-3">
          <h2 class="text-white">Cosa stai cercando?</h2>
          <div class="it-btn-container gap-3 d-flex flex-wrap">
            <?php
              if(!empty($args["quick_links"])){
                foreach ($args["quick_links"] as $link){
                  if(!empty($link['url']) && !empty($link['label'])){
                    echo '<a class="btn btn-sm bg-white text-black" href="'.$link['url'].'">'.$link['label'].'</a>';
                  }
                }
              }
            ?>
          </div>
          <div class="input-group p-1 bg-white rounded">
            <input type="search" class="form-control" id="search" name="s" placeholder="Cerca nel sito" value="">
            <button type="submit" class="btn btn-sm btn-primary">Cerca</button>
          </div>
        </div>
    </form>
  </div>
  <?php
    if(!empty($randomImage) && $randomImage['title']){
      echo '<a style="font-size:20px;" href="'.($randomImage['link']??'#').'" class="text-white position-absolute bottom-0 end-0 pe-3 pb-2 text-decoration-none fw-bold">'.$randomImage['title'].'</a>';
    }
  ?>
</section>