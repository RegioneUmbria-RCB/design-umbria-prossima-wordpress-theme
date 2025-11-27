<?php
/**
 * Template part per breadcrumb dinamico
 * con associazione post type → categoria da wp_option('category_metabox')
 */

global $post;
$position = 1;

/**
 * Restituisce l'ID della prima categoria associata al post type corrente
 */
function get_current_post_type_category_id() {
    $options = get_option('category_metabox');
    if (!is_array($options)) {
        return false;
    }

    $post_type = get_post_type();

    foreach ($options as $key => $value) {
        if (strpos($key, 'post_type_for_category_') === 0 && $value === $post_type) {
            // Ritorna subito la prima categoria trovata
            return (int) str_replace('post_type_for_category_', '', $key);
        }
    }

    return false;
}
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

    <?php if (is_category() || is_tag() || is_tax()) : ?>
      <!-- Archivio tassonomia -->
      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="breadcrumb-item">
        <span class="separator">/</span>
        <span itemprop="item"><span itemprop="name"><?php echo single_term_title('', false); ?></span></span>
        <meta itemprop="position" content="<?php echo $position; ?>" />
      </li>

    <?php elseif (is_single()) : ?>
      <!-- Categoria derivata dal mapping in category_metabox -->
      <?php
      $category_id = get_current_post_type_category_id();
      if ($category_id) :
          $category = get_category($category_id);

          if ($category) :
              // Se la categoria ha un genitore, lo mostriamo prima
              if ($category->parent) :
                  $parent = get_category($category->parent);
                  if ($parent) :
      ?>
                      <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="breadcrumb-item">
                        <span class="separator">/</span>
                        <a class="text-primary" href="<?php echo esc_url(get_category_link($parent)); ?>" itemprop="item">
                          <span itemprop="name"><?php echo esc_html($parent->name); ?></span>
                        </a>
                        <meta itemprop="position" content="<?php echo $position; ?>" />
                      </li>
                      <?php $position++; ?>
      <?php
                  endif;
              endif;
      ?>
              <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="breadcrumb-item">
                <span class="separator">/</span>
                <a class="text-primary" href="<?php echo esc_url(get_category_link($category)); ?>" itemprop="item">
                  <span itemprop="name"><?php echo esc_html($category->name); ?></span>
                </a>
                <meta itemprop="position" content="<?php echo $position; ?>" />
              </li>
              <?php $position++; ?>
      <?php
          endif;
      endif;
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
