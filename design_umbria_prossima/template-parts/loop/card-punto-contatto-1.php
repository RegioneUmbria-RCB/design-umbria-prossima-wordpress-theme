<?php
if ( $args ) :
$data = get_post_meta($args->ID);
$contatti = unserialize($data["_dci_punto_contatto_voci"][0]);
?>

<div class="card card-teaser card-teaser-info shadow mt-3 rounded">
    <svg class="icon" aria-hidden="true">
        <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-telephone"></use>
    </svg>
    <div class="card-body">
        <h5 class="card-title text-secondary fw-bold m-0" style="font-family: 'Titillium Web', sans-serif;">
            <?php echo $args->post_title; ?>
        </h5>
        <div class="card-text">
            <?php foreach ($contatti as $c): ?>
                <?php if ($c["_dci_punto_contatto_tipo_punto_contatto"] === "telefono"): ?>
                    <p><?= esc_html($c["_dci_punto_contatto_valore"]); ?></p>
                <?php elseif ($c["_dci_punto_contatto_tipo_punto_contatto"] === "email"): ?>
                    <p>
                        <a target="_blank"
                            class=" text-primary"
                            aria-label="invia un'email a <?= esc_attr($c["_dci_punto_contatto_valore"]); ?>"
                            title="invia un'email a <?= esc_attr($c["_dci_punto_contatto_valore"]); ?>"
                            href="mailto:<?= esc_attr($c["_dci_punto_contatto_valore"]); ?>">
                                <?= esc_html($c["_dci_punto_contatto_valore"]); ?>
                        </a>
                    </p>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif;?>