<?php 
if ( $args ) : 
  $post_id   = $args->ID;
  $post_type = get_post_type( $post_id );
  $title     = get_the_title( $post_id );
  $external_link = get_post_meta( $post_id, 'external_link', true );
  $permalink = !empty($external_link)? $external_link : get_permalink( $post_id );
  $image_id  = get_post_meta( $post_id, '_dci_'.$post_type.'_immagine_id', true );
?>
<a href="<?php echo esc_url( $permalink ); ?>" class="card card-img no-after">
  <div class="img-responsive-wrapper">
      <div class="img-responsive">
          <div class="img-wrapper">
            <?php echo wp_get_attachment_image(
                intval( $image_id ),
                'medium',
                false,
                array(
                  'width' => '280',
                  'height'=> '158',
                  'class' => 'attachment-item-thumb size-item-thumb',
                  'alt'   => esc_attr( $title ),
                  'style' => 'object-fit:cover;',
                )
            ); ?>
          </div>
      </div>
  </div>
  <div class="card-overlay">
      <?php echo $title; ?>                                
  </div>
</a>
<?php endif; ?>