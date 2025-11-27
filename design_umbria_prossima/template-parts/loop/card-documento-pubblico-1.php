<?php 
if ( $args ) : 
  $post_id   = $args->ID;
  $title     = get_the_title( $post_id );
  $external_link = get_post_meta( $post_id, 'external_link', true );
  $permalink = !empty($external_link)? $external_link : get_permalink( $post_id );
  $terms = get_the_terms($post_id, 'tipi_documento');
  $excerpt = get_post_meta( $post_id, '_dci_documento_pubblico_descrizione_breve', true );
?>
<div class="col-12">
    <div class="card-wrapper rounded px-3 bg-white shadow position-relative">
        <div class="card no-after rounded d-flex flex-column border-0 bg-white h-100">
            <div class="row flex-grow-1">
                <div class="col-md-12 pb-2 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-3 pt-4">
                        <?php if ( ! empty( $terms ) && is_array( $terms ) ) {
                            $term = $terms[0];
                            $term_link = get_term_link( $term );
                            if ( ! is_wp_error( $term_link ) ) : ?>
                            <div class="chip chip-simple bg-primary text-white text-truncate text-decoration-none">
                                <span class="chip-label text-white"><?php echo esc_html( $term->name ); ?></span>
                            </div>
                            <?php endif;
                        } ?>
                        <svg class="icon icon-sm" aria-hidden="true">
                            <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-file"></use>
                        </svg>
                    </div>
                    <a class="text-decoration-none" href="<?php echo $permalink; ?>" aria-label="Leggi il documento: <?php echo $title;?>">
                        <h5 class="card-title" style="font-family: 'Titillium Web', sans-serif; font-size: 24px; font-weight: 600; line-height: 32px; text-align: left; color: black;">
                        <?php echo $title; ?>
                        </h5>
                    </a>
                    
                    <div class="card-text-wrapper flex-grow-1">
                        <p class="card-text text-secondary pt-2" style="font-family: 'Titillium Web', serif; font-size: 16px; font-weight: 400; line-height: 24px; text-align: left; color: #2F475E;">
                            <?php echo $excerpt; ?>
                        </p>
                    </div>
                    <div class="mt-3 mb-lg-3 leggi-tutto">
                        <a href="<?php echo $permalink; ?>" style="text-decoration: none; font-family: 'Titillium Web', sans-serif; font-size: 14px; font-weight: 600; color: black; display: inline-flex; align-items: center;">
                            <span>LEGGI TUTTO</span>
                            <svg class="icon icon-xs ms-1" style="width: 16px; height: 16px; vertical-align: middle;" aria-hidden="true">
                                <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-arrow-right"></use>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>