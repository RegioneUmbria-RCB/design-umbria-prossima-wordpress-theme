<?php 
if ( $args ) : 
  $post_id   = $args->ID;
  $title     = get_the_title( $post_id );
  $external_link = get_post_meta( $post_id, 'external_link', true );
  $permalink = !empty($external_link)? $external_link : get_permalink( $post_id );
  $timestamp = get_post_meta( $post_id, '_dci_notizia_data_pubblicazione', true );
  if ( ! empty( $timestamp ) ) {$formatted_date = date_i18n( 'd M Y', intval( $timestamp ) );}
  $excerpt  = get_post_meta( $post_id, '_dci_notizia_descrizione_breve', true );
  $image_id  = get_post_meta( $post_id, '_dci_notizia_immagine_id', true );
  $terms = get_the_terms($post_id, 'argomenti');
?>

<div class="col-12">
  <div class="card no-after rounded d-flex flex-column border-0 bg-white h-100 shadow">
    <div class="row flex-grow-1 px-3">
      <div class="col-md-12 pb-2 d-flex flex-column">
        <div class="d-flex gap-3 justify-content-between align-items-center mb-3 pt-4">
          <div>
          <?php if ( ! empty( $terms ) && is_array( $terms ) ) {
            $term = $terms[0];
            $term_link = get_term_link( $term );
            if ( ! is_wp_error( $term_link ) ) : ?>
                <a href="<?php echo esc_url( $term_link ); ?>" class="chip chip-simple bg-primary text-white text-truncate text-decoration-none">
                  <span class="chip-label text-white"><?php echo esc_html( $term->name ); ?></span>
                </a>
            <?php endif;
          } ?>                        
          </div>
          <span class="data" style="font-family: 'Roboto Mono'; font-size: 14px; font-weight: 400; line-height: 24px; text-align: left; color: #2F475E;">
            <?php echo esc_html( $formatted_date ); ?>
          </span>
        </div>

        <a class="text-decoration-none" href="<?php echo esc_url( $permalink ); ?>" aria-labelledby="titleLabel-<?php echo esc_attr( $post_id ); ?>">
          <h3 class="card-title mb-2" style="font-family: 'Titillium Web'; font-size: 24px; font-weight: 600; line-height: 32px; text-align: left; color: black;">
            <span><?php echo esc_html( $title ); ?></span>
          </h3>
        </a>

        <h4 class="card-text mb-2 flex-grow-1" style="font-family: 'Titillium Web'; font-size: 16px; font-weight: 400; line-height: 24px; text-align: left; color: #2F475E;" aria-labelledby="descriptionLabel-<?php echo esc_attr( $post_id ); ?>">
          <span><?php echo esc_html( $excerpt ); ?></span>
        </h4>

        <div class="mt-auto">
          <a href="<?php echo esc_url( $permalink ); ?>" style="text-decoration: none; font-family: 'Titillium Web', sans-serif; font-size: 14px; font-weight: 600; color: black; display: inline-flex; align-items: center;">
            <span>LEGGI TUTTO</span>
            <svg class="icon icon-xs ms-1" style="width: 16px; height: 16px; vertical-align: middle;" aria-hidden="true">
                <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-arrow-right"></use>
            </svg>
          </a>
        </div>
      </div>
    </div>
    
    <div class="img-responsive-wrapper mt-2">
      <div class="img-responsive img-responsive-panoramic">
          <figure class="img-wrapper">
            <?php echo wp_get_attachment_image(
                intval( $image_id ),
                'medium',
                false,
                array(
                  'class' => 'attachment-item-thumb size-item-thumb',
                  'alt'   => esc_attr( $title ),
                  'style' => 'object-fit:cover;',
                )
            ); ?>
          </figure>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>
