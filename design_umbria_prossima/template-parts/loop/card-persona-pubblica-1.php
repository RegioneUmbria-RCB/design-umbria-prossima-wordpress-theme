<?php 
if ( $args ) : 
$post_id   = $args->ID;
$title     = get_the_title( $post_id );
$external_link = get_post_meta( $post_id, 'external_link', true );
$permalink = !empty($external_link)? $external_link : get_permalink( $post_id );
$excerpt  = get_post_meta( $post_id, '_dci_persona_pubblica_descrizione_breve', true );
$image_id  = get_post_meta( $post_id, '_dci_persona_pubblica_immagine_id', true );
$contact_ids = get_post_meta( $post_id, '_dci_persona_pubblica_punti_contatto', true );

$contact = [
    'post_type'      => 'punto_contatto',
    'post__in'       => $contact_ids,
    'orderby'        => 'post__in'
];

$posts = get_posts($contact);
$contact_points_list = array();

foreach ($posts as $post) {
    $contact_point = get_post_meta( $post->ID, '_dci_punto_contatto_voci', true );
    if(!empty($contact_point)){
        array_push($contact_points_list, $contact_point);
    }
}
wp_reset_postdata();
?>

<div class="it-card card border-primary shadow politician-card">
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
    <div class="card-body">
        <h4 class="card-title h6 mb-0">
            <a class="text-primary" href="<?php echo $permalink; ?>"><?php echo $title; ?></a>
        </h4>
        <p class="card-text mb-2 flex-grow-1 mb-3" style="font-family: 'Titillium Web'; font-size: 16px; font-weight: 400; line-height: 24px; text-align: left; color: #2F475E;" aria-labelledby="descriptionLabel">
            <?php echo $excerpt; ?>
        </p>
        <div class="ps-4">
            <h6 class="card-title">Contatti:</h6>
            <div class="card-text">
                <?php foreach ($contact_points_list as $contact_group): ?>
                    <?php foreach ($contact_group as $contact): ?>
                        <?php 
                            $type  = $contact['_dci_punto_contatto_tipo_punto_contatto'];
                            $value = $contact['_dci_punto_contatto_valore'];
                        ?>

                        <?php if ($type === 'telefono'): ?>
                            <div class="d-flex align-items-center mb-2">
                                <svg class="icon me-2" aria-hidden="true">
                                    <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-telephone"></use>
                                </svg>
                                <span><?php echo esc_html($value); ?></span>
                            </div>
                        <?php elseif ($type === 'email'): ?>
                            <div class="d-flex align-items-center mb-2">
                                <svg class="icon me-2" aria-hidden="true">
                                    <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-mail"></use>
                                </svg>
                                <a href="mailto:<?php echo esc_attr($value); ?>" class="text-decoration-none text-primary">
                                    <?php echo esc_html($value); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>