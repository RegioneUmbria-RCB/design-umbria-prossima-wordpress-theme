<?php 
if ( $args ) : 
  $post_id   = $args->ID;
  $post_type = get_post_type($post_id);
  $post_type_obj = get_post_type_object( $post_type );
  if ( $post_type_obj ) {
      $singular_name = $post_type_obj->labels->singular_name;
     
  }
  $title     = get_the_title( $post_id );
  $external_link = get_post_meta( $post_id, 'external_link', true );
  $permalink = !empty($external_link)? $external_link : get_permalink( $post_id );
  $excerpt  = get_post_meta( $post_id, '_dci_'.$post_type.'_descrizione_breve', true ) ?? get_the_excerpt();
?>

<div class="col-12">
  <div class="shadow-sm px-4 pt-4 pb-4 rounded border">
    <span class="border-0 p-0 d-flex justify-content-between align-items-center title-xsmall-bold mb-2 category text-uppercase">
      <?php echo $singular_name; ?>
    </span>
   
    <h3 class="mb-8">
        <a class="text-primary text-decoration-none" href="<?php echo esc_url( $permalink ); ?>" data-element="service-link">
          <?php echo esc_html( $title ); ?>
        </a>
    </h3>
    <p class="text-paragraph"><?php echo esc_html( $excerpt ); ?></p>
    
  </div>
</div>
<?php endif; ?>






