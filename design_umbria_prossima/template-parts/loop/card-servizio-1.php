<?php 
if ( $args ) : 
  $post_id   = $args->ID;
  $title     = get_the_title( $post_id );
  $external_link = get_post_meta( $post_id, 'external_link', true );
  $permalink = !empty($external_link)? $external_link : get_permalink( $post_id );
  $excerpt  = get_post_meta( $post_id, '_dci_servizio_descrizione_breve', true );
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
        </div>

        <a class="text-decoration-none" href="<?php echo esc_url( $permalink ); ?>" aria-labelledby="titleLabel-<?php echo esc_attr( $post_id ); ?>">
          <h3 class="card-title mb-2" style="font-family: 'Titillium Web'; font-size: 24px; font-weight: 600; line-height: 32px; text-align: left; color: black;">
            <span><?php echo esc_html( $title ); ?></span>
          </h3>
        </a>

        <h4 class="card-text mb-2 flex-grow-1" style="font-family: 'Titillium Web'; font-size: 16px; font-weight: 400; line-height: 24px; text-align: left; color: #2F475E;" aria-labelledby="descriptionLabel-<?php echo esc_attr( $post_id ); ?>">
          <span><?php echo esc_html( $excerpt ); ?></span>
        </h4>

        <div class="my-3">
          <a href="<?php echo esc_url( $permalink ); ?>" style="text-decoration: none; font-family: 'Titillium Web', sans-serif; font-size: 14px; font-weight: 600; color: black; display: inline-flex; align-items: center;">
            <span>VAI AL SERVIZIO</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>
