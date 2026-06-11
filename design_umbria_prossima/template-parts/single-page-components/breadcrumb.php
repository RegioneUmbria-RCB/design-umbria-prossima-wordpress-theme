<?php
/**
 * Template part per breadcrumb dinamico
 * con associazione post type → categoria da wp_option('category_metabox')
 */

global $post;
$position = 1;
?>

<nav class="breadcrumb-container mt-4" aria-label="breadcrumb">
  <ol class="breadcrumb p-0" data-element="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
    <meta name="itemListOrder" content="Ascending" />

    <!-- Home -->
    <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="breadcrumb-item trail-begin">
      <a class="text-primary" href="<?php echo esc_url(home_url()); ?>" rel="home" itemprop="item">
        <span itemprop="name">Home</span>
      </a>
      <meta itemprop="position" content="<?php echo $position; ?>" />
    </li>

    <?php $position++; ?>

    <?php if ( is_category() || is_tag() || is_tax() ) : ?>
      <?php
      $queried = get_queried_object();
      if ( $queried instanceof WP_Term && dup_is_taxonomy_breadcrumb_enabled( $queried->taxonomy, $queried->term_id ) ) {
          dup_breadcrumb_echo_taxonomy_term_trail( $queried->taxonomy, $queried->term_id, $position, true );
      } else {
          ?>
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="breadcrumb-item active">
        <span class="separator">/</span>
        <span itemprop="item"><span itemprop="name"><?php echo esc_html( single_term_title( '', false ) ); ?></span></span>
        <meta itemprop="position" content="<?php echo (int) $position; ?>" />
      </li>
          <?php
      }
      ?>

    <?php elseif (is_single()) : ?>
      <?php
      $breadcrumb_tax_term = dup_get_breadcrumb_taxonomy_term_for_post();
      if ( $breadcrumb_tax_term ) {
          dup_breadcrumb_echo_taxonomy_term_trail( $breadcrumb_tax_term->taxonomy, $breadcrumb_tax_term->term_id, $position, false );
      } else {
          $category_id = dup_get_breadcrumb_category_id_for_post();
          if ( $category_id ) {
              dup_breadcrumb_echo_category_trail( $category_id, $position );
          }
      }
      ?>

      <!-- Titolo del post -->
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="breadcrumb-item active">
        <span class="separator">/</span>
        <span itemprop="item"><span itemprop="name"><?php the_title(); ?></span></span>
        <meta itemprop="position" content="<?php echo $position; ?>" />
      </li>

    <?php elseif (is_page() && !is_front_page()) : ?>
      <!-- Gerarchia pagine -->
      <?php if ($post->post_parent) : ?>
        <?php
        $ancestors = array_reverse(get_post_ancestors($post->ID));
        foreach ($ancestors as $ancestor) :
        ?>
          <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="breadcrumb-item">
            <span class="separator">/</span>
            <a class="text-primary" href="<?php echo esc_url(get_permalink($ancestor)); ?>" itemprop="item">
              <span itemprop="name"><?php echo esc_html(get_the_title($ancestor)); ?></span>
            </a>
            <meta itemprop="position" content="<?php echo $position; ?>" />
          </li>
          <?php $position++; ?>
        <?php endforeach; ?>
      <?php endif; ?>

      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="breadcrumb-item active">
        <span class="separator">/</span>
        <span itemprop="item"><span itemprop="name"><?php the_title(); ?></span></span>
        <meta itemprop="position" content="<?php echo $position; ?>" />
      </li>
    <?php endif; ?>
  </ol>
</nav>
