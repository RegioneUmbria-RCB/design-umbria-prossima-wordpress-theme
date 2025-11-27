<?php
global $luogo_id, $with_border;
$luogo = get_post( $luogo_id );

$prefix = '_dci_luogo_';
$image_id = dci_get_meta('immagine_id', $prefix, $luogo->ID);
$indirizzo = dci_get_meta("indirizzo", $prefix, $luogo->ID);  

if($with_border) {
?>
<div class="card card-teaser border rounded shadow p-3 flex-nowrap">
    <div class="card-body">
       <h4 class="u-main-black mb-1 title-small-semi-bold-medium">
            <a class="text-decoration-none text-primary" href="<?php echo get_permalink($ufficio->ID); ?>">
            <?php echo $luogo->post_title; ?>
            </a>
        </h4>
        
        <div class="card-text">
            <?php
                echo '<p>'.$indirizzo.'</p>';
            ?>
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
<div class="card card-teaser shadow-sm p-3 card-teaser-info rounded flex-nowrap">
    <div class="card-body">
        <p class="card-title text-paragraph-regular-medium-semi">
            <a class="text-decoration-none text-primary" href="<?php echo get_permalink($luogo->ID); ?>" data-element="service-area">
            <?php echo $luogo->post_title; ?>
            </a>
        </p>
        <div class="card-text">
            <?php
                echo '<p class="u-main-black">'.$indirizzo.'</p>';
            ?>
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
$with_border = false;
?>