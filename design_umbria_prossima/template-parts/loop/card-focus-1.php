
<?php 
if ( $args ) : 
    $term_id   = $args->term_id;
    $name     = $args->name;
    $external_link = get_term_meta( $term_id, 'external_link', true );
    $permalink = !empty($external_link)? $external_link : get_term_link( $term_id, $args->taxonomy );
    $excerpt   = $args->description;
    $image_id = get_term_meta($term_id, 'dci_term_immagine_id', true);
?>

<div style="min-height:350px;" class="card-wrapper rounded px-3 bg-primary shadow" style="position: relative;">
    <div class="card no-after rounded d-flex flex-column border-0 bg-primary h-100">
        <div class="d-flex flex-column align-items-start">
            <div class="avatar mt-3">
                <?php echo wp_get_attachment_image(
                    intval( $image_id ),
                    'medium',
                    false,
                    array(
                        'width'  => '280',
                        'height' => '280',
                        'class'  => 'img-fluid',
                        'alt'    => esc_attr( $name ),
                        'style'  => 'object-fit:cover;',
                    )
                ); ?>
            </div>
            <div class="text-left">
                <a class="text-decoration-none text-white" 
                   href="<?php echo esc_url( $permalink ); ?>" 
                   aria-label="Leggi di più: <?php echo esc_attr( $name ); ?>">
                    <h5 class="focus-cards-title mb-2">
                        <?php echo esc_html( $name ); ?>
                    </h5>
                </a>
                <div class="card-text-wrapper">
                    <p style="font-family: 'Lora', serif; font-size:16px; -webkit-line-clamp:6; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-box-orient: vertical;" class="text-white">
                        <?php echo esc_html( $excerpt ); ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-auto text-left pb-3 leggi-tutto">
            <a class="text-white d-flex align-items-center fw-bold text-decoration-none"
               style="font-size:14px;"
               href="<?php echo esc_url( $permalink ); ?>" 
               aria-label="Leggi tutto">
                LEGGI TUTTO
                <svg fill="#ffffff" class="icon ms-1" 
                     style="width: 24px; height: 24px; vertical-align: middle;" 
                     aria-hidden="true">
                    <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-arrow-right"></use>
                </svg>
            </a>
        </div>
    </div>
</div>
<?php endif; ?>
