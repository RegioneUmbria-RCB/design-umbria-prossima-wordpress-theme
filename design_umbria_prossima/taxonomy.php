<?php
$term = get_queried_object();

if ( isset($term->taxonomy) && $term->taxonomy === 'category' ) {
    include get_template_directory() . '/category.php';
    exit;
}

$metabox = get_option('taxonomy_metabox');
$term_id  = $term->term_id;
$term_taxonomy = $term->taxonomy;
$image_id = get_term_meta($term_id, 'dci_term_immagine_id', true);
$icon_id  = get_term_meta($term_id, 'icon_id', true);
$post_list_group = $metabox["post_list_group_{$term_taxonomy}_{$term_id}"] ?? [];
if ( is_array( $post_list_group ) ) {
    $post_list_group = array_values( $post_list_group );
}
$alert_active = $metabox["alert_enable_{$term_taxonomy}_{$term_id}"] ?? null;
$alert_type = $metabox["alert_type_{$term_taxonomy}_{$term_id}"] ?? null;
$alert_message = $metabox["alert_message_{$term_taxonomy}_{$term_id}"] ?? "";

get_header();
?>
<main>
  <div class="it-hero-wrapper it-wrapped-container" id="main-container">

    <?php if ($image_id) : ?>
      <div class="img-responsive-wrapper">
        <div class="img-responsive">
          <div class="img-wrapper">
            <?php
              echo wp_get_attachment_image(
                intval($image_id),
                'large',
                false,
                array(
                  'class' => 'attachment-item-thumb size-item-thumb',
                  'style' => 'object-fit:cover;',
                  'alt'   => esc_attr($term->name),
                )
              );
            ?>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <div class="container">
      <div class="row">
        <div class="col-12 px-0 px-lg-2">
          <div class="it-hero-card it-hero-bottom-overlapping rounded hero-p drop-shadow <?php echo $image_id ? '' : 'mt-0'; ?>">
            <div class="col-12 col-lg-10 mx-auto">
              <div class="row justify-content-center">
                <?php if($alert_active == 'on'):?>
                  <div class="pb-4 container">
                    <?php get_template_part('template-parts/alert-component', null, array('active'=>$alert_active, 'type'=>$alert_type, 'message'=>$alert_message)); ?>
                  </div>
                <?php endif;?>
                <nav class="breadcrumb-container" aria-label="breadcrumb">
                  <ol class="breadcrumb p-0" data-element="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
                    <?php $position = 1; ?>
                    <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="breadcrumb-item trail-begin">
                      <a class="text-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" itemprop="item">
                        <span itemprop="name">Home</span>
                      </a>
                      <meta itemprop="position" content="<?php echo (int) $position++; ?>">
                    </li>

                    <?php
                    if ( dup_is_taxonomy_breadcrumb_enabled( $term_taxonomy, $term_id ) ) {
                        dup_breadcrumb_echo_taxonomy_term_trail( $term_taxonomy, $term_id, $position, true );
                    } else {
                        $taxonomy_obj = get_taxonomy( $term_taxonomy );
                        $fallback_cat = $taxonomy_obj ? get_term_by( 'slug', $taxonomy_obj->name, 'category' ) : null;
                        if ( $fallback_cat && ! is_wp_error( $fallback_cat ) ) :
                            ?>
                      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="breadcrumb-item">
                        <span class="separator">/</span>
                        <a class="text-primary" href="<?php echo esc_url( home_url( '/' . $term_taxonomy ) ); ?>" itemprop="item">
                          <span itemprop="name"><?php echo esc_html( $fallback_cat->name ); ?></span>
                        </a>
                        <meta itemprop="position" content="<?php echo (int) $position++; ?>">
                      </li>
                            <?php
                        endif;
                        ?>
                      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="breadcrumb-item active">
                        <span class="separator">/</span>
                        <span itemprop="item">
                          <span itemprop="name"><?php echo esc_html( $term->name ); ?></span>
                        </span>
                        <meta itemprop="position" content="<?php echo (int) $position++; ?>">
                      </li>
                        <?php
                    }
                    ?>
                  </ol>
                </nav>
              </div>

              <div class="row sport-wrapper justify-content-between mt-lg-2">
                <div class="col-12">
                  <div class="d-flex align-items-center mb-3 mb-lg-4">
                    <?php
                    if (!empty($icon_id)) {
                      echo wp_get_attachment_image(
                        intval($icon_id),
                        'small',
                        false,
                        array(
                          'class' => 'me-3 d-inline-block show-on-large',
                          'alt'   => esc_attr__('Icona argomento', 'textdomain'),
                          'style' => 'min-width: 40px; min-height: 40px;',
                        )
                      );
                    }
                    ?>
                    <h1 class="title-xxlarge"><?php echo esc_html($term->name); ?></h1>
                  </div>

                  <?php if (!empty($term->description)) : ?>
                    <p class="u-main-black text-paragraph-regular-medium" style="text-align: justify;">
                      <?php echo esc_html($term->description); ?>
                    </p>
                  <?php endif; ?>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="mt-md-100">
    <?php 
      if(!empty($post_list_group)){
        foreach ($post_list_group as $args):?>
          <div class="container">
            <?php get_template_part('template-parts/post-list-component', null, $args); ?>
          </div>
      <?php endforeach; 
      }
    ?>
  </div>
</main>
<?php
get_footer();
