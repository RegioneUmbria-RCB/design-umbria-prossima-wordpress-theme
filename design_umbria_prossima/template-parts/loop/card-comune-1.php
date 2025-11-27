<?php 
if ( $args ) : 
  $post_id   = $args->ID;
  $title     = get_the_title( $post_id );
  $external_link = get_post_meta( $post_id, 'external_link', true );
  $permalink = !empty($external_link)? $external_link : get_permalink( $post_id );
  $image_id = get_post_thumbnail_id($post_id);
?>
<div class="card-wrapper rounded px-3 bg-white shadow flex-fill d-flex flex-column" style="position: relative;">
  <div class="card no-after rounded d-flex flex-column border-0 bg-white h-100">
      <div class="d-flex justify-content-between align-items-start mb-3 pt-4">
        <h5 class="card-title text-primary" style="font-family: 'Titillium Web'; font-size: 20px; font-weight: 600; line-height: 28px;">
            <?php echo $title;?>
        </h5>
        <?php echo wp_get_attachment_image(
            intval( $image_id ),
            'medium',
            false,
            array(
              'class' => 'img-fluid',
              'alt'   => esc_attr( $title ),
              'style' => 'max-height: 40px; object-fit:contain;',
            )
        ); ?>
      </div>
      <hr class="mt-3 mb-3">
      <div class="text-end mt-auto mb-3">
          <a href="<?php echo $permalink;?>" class="btn btn-link p-0 text-primary" target="_blank" style="font-family: 'Titillium Web'; font-size: 16px; font-weight: 600; line-height: 24px;">
              Vai al sito
              <svg class="icon icon-primary align-items-center">
                <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-external-link"></use>
              </svg>
          </a>
      </div>
  </div>
</div>
<?php endif;?>