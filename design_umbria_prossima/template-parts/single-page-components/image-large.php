<?php
global $post;
$image_id  = dci_get_meta('immagine_id');
$image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
$image_size = get_query_var('custom_image_size', 'large');

if ($image_id) {
    $img = get_post($image_id);
    ?>
    <div class="container-fluid my-3">
        <div class="row">
            <figure class="figure px-0 img-full">
                
                <?php echo wp_get_attachment_image(
                    intval($image_id),
                    $image_size,
                    false,
                    array(
                        'class' => 'figure-img img-fluid',
                        'alt'   => esc_attr($image_alt),
                        'style' => 'object-fit:contain;'
                    )
                ); ?>

                <?php if (!empty($img->post_excerpt)) : ?>
                    <figcaption class="figure-caption text-center pt-3">
                        <?php echo esc_html($img->post_excerpt); ?>
                    </figcaption>
                <?php endif; ?>

            </figure>
        </div>
    </div>
<?php 
}
?>
