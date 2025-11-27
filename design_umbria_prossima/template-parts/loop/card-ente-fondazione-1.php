<?php 
if ( $args ) : 
  $post_id   = $args->ID;
  $title     = get_the_title( $post_id );
  $external_link = get_post_meta( $post_id, 'external_link', true );
  $permalink = !empty($external_link)? $external_link : '#';
  $excerpt   = get_the_excerpt($post_id);
  $image_id  = get_post_thumbnail_id( $post_id );
?>
<div class="card no-after rounded d-flex flex-column border-0 bg-white h-100 shadow">
    <div class="card-body p-4 d-flex flex-column">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a class="text-decoration-none" href="<?php echo $permalink; ?>" aria-labelledby="titleLabel" target="_blank">
                <h5 class="card-title mb-2" style="font-family: 'Titillium Web'; font-size: 24px; font-weight: 600; line-height: 32px; text-align: left; color: black;">
                    <span id="titleLabel"><?php echo $title; ?></span>
                </h5>
            </a>
            <?php echo wp_get_attachment_image(
                intval( $image_id ),
                'small',
                false,
                array(
                  'width' => '108',
                  'height'=> '60',
                  'class' => 'attachment-item-thumb size-item-thumb',
                  'alt'   => esc_attr( $title ),
                  'style' => 'max-height: 60px;object-fit:contain; width:auto;'
                )
            ); ?>
        </div>
        <p class="card-text flex-grow-1 mb-2" style="font-family: 'Titillium Web'; font-size: 16px; font-weight: 400; line-height: 24px; text-align: left; color: #1E1E1E;">
          <?php echo $excerpt; ?>
        </p>
        <div class="mt-auto">
            <hr class="mt-3 mb-3">
            <div class="text-end align-items-center">
                <a href="<?php echo $permalink; ?>" class="btn btn-link p-0" target="_blank" style="font-family: 'Titillium Web'; font-size: 14px; font-weight: 600; line-height: 16px; text-align: left; color: #1A1A1A;">
                    VAI AL SITO
                    <svg class="icon icon-primary align-items-center"><use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-external-link"></use></svg>
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>