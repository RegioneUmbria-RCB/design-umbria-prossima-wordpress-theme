<?php
    global $uo_id, $without_border;
    $ufficio = get_post( $uo_id );

    $prefix = '_dci_unita_organizzativa_';
    $image_id = dci_get_meta('immagine_id', $prefix, $uo_id);
    $sede_principale = dci_get_meta('sede_principale', $prefix, $uo_id);
    $prefix = '_dci_luogo_';
    $indirizzo = dci_get_meta('indirizzo', $prefix, $sede_principale);

    $prefix = '_dci_luogo_';
    $contatti = array();
    if(isset($punti_contatto)){
        foreach ($punti_contatto as $pc_id) {
            $contatto = dci_get_full_punto_contatto($pc_id);
            array_push($contatti, $contatto);
        }
    }
    
    if($without_border) {
?>
<div class="card card-teaser border rounded shadow p-4 flex-nowrap">
    <div class="card-body pe-3">
        <h4 class="u-main-black mb-1 title-small-semi-bold-medium">
            <a class="text-decoration-none text-primary" href="<?php echo get_permalink($ufficio->ID); ?>">
            <?php echo $ufficio->post_title; ?>
            </a>
        </h4>
        <div class="card-text">
            <p><?php echo $indirizzo; ?></p>
        </div>
    </div>
    <?php if ($image_id) { ?>
    <div class="avatar size-xl">
        <?php echo wp_get_attachment_image(
            intval( $image_id ),
            'small',
            false,
            array(
              'style' => 'object-fit:cover;',
            )
        ); ?>
    </div>
    <?php } ?>
</div>

<?php } else { ?>

<div class="card card-teaser card-teaser-info rounded shadow-sm p-3 flex-nowrap">
    <div class="card-body pe-3">
        <p class="card-title text-paragraph-regular-medium-semi mb-3">
            <a class="text-decoration-none text-primary" href="<?php echo get_permalink($ufficio->ID); ?>" data-element="service-area">
            <?php echo $ufficio->post_title; ?>
            </a>
        </p>
        <div class="card-text">
            <p class="u-main-black">
				<?php echo $indirizzo;  ?>
			</p>
        </div>
    </div>
    <?php if ($image_id) { ?>
        <div class="avatar size-xl">
          <?php echo wp_get_attachment_image(
            intval( $image_id ),
            'small',
            false,
            array(
              'style' => 'object-fit:cover;',
            )
          ); ?>
        </div>
    <?php } ?>
</div>

<?php } 
$without_border = false;
?>