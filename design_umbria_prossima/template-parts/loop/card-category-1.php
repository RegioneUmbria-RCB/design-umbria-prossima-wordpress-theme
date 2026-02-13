<?php 
if ( $args ) : 
  $term_id   = $args->term_id;
  $name      = $args->name;
  $external_link = get_term_meta( $term_id, 'external_link', true );
  $permalink = !empty($external_link)? $external_link : get_category_link($term_id);
  $description = ( is_object( $args ) && ! empty( $args->term_id ) && function_exists( 'get_category_page_description' ) )
    ? get_category_page_description( $args )
    : ( is_object( $args ) ? ( $args->description ?? '' ) : ( $args['description'] ?? '' ) );
?>
<div class="card shadow-sm rounded border h-100">
  <div class="card-body">
      <a class="text-decoration-none text-primary" href="<?php echo $permalink; ?>" data-element="management-category-link">
          <h3 class="h5 fw-bold"><?php echo $name; ?></h3>
      </a>
      <p class="text-paragraph mb-0"><?php echo $description; ?></p>
  </div>
</div>
<?php endif; ?>