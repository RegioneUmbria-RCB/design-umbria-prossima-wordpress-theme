<?php 
if ( $args ) : 
    $term_id   = $args->term_id;
    $name      = $args->name;
    $external_link = get_term_meta( $term_id, 'external_link', true );
    $permalink = !empty($external_link)? $external_link : get_term_link( $term_id, $args->taxonomy );
    $icon_id = get_term_meta( $term_id, 'icon_id', true );
?>
    <div class="col-12 shadow p-3 mb-3 bg-white rounded position-relative">
        <a href="<?php echo $permalink; ?>" 
           class="text-decoration-none text-dark d-flex align-items-center" 
           data-focus-mouse="false">
            <?php echo wp_get_attachment_image(
                intval( $icon_id ),
                'small',
                false,
                array(
                    'class'  => 'mr-3 show-on-large',
                    'alt'    => 'Icona argomento',
                    'style'  => 'width: 40px; height: 40px; position: absolute; z-index: 1;',
                )
            ); ?>

            <div class="card-body">
                <div class="h5 card-title font-titillium text-center" 
                     style="font-weight: 600; font-size: 20px; line-height: 24px; position: relative; z-index: 0;">
                    <?php echo $name; ?>
                </div>
            </div>
        </a>
    </div>
<?php endif; ?>
