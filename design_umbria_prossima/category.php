<?php
get_header();

$metabox = get_option('category_metabox') ?: [];
$cat_id       = get_queried_object_id();
$current_cat  = get_queried_object();
$subcategories = get_categories([
    'child_of'   => $current_cat->term_id,
    'hide_empty' => false,
]);

if (!$subcategories || count($subcategories) == 0) {
    $subcategories = get_categories([
        'parent'     => $current_cat->parent,
        'hide_empty' => false,
    ]);
}

$subcategories = array_filter($subcategories, fn($cat) => $cat->term_id != $current_cat->term_id);

// $metabox_for_cat = array_filter(
//     $metabox,
//     fn($key) => preg_match('/_' . $cat_id . '$/', $key),
//     ARRAY_FILTER_USE_KEY
// );

$show_subcategories_header = $metabox["show_subcategories_header_{$cat_id}"] ?? null;
$show_subcategories_page   = $metabox["show_subcategories_page_{$cat_id}"] ?? null;
$show_content_list         = $metabox["show_content_list_{$cat_id}"] ?? null;
$show_content_filters      = $metabox["show_content_filters_{$cat_id}"] ?? null;
$show_content_search       = $metabox["show_content_search_{$cat_id}"] ?? null;
$post_list_group           = $metabox["post_list_group_{$cat_id}"] ?? [];
$post_type                 = $metabox["post_type_for_category_{$cat_id}"] ?? [];
$alert_active              = $metabox["alert_enable_{$cat_id}"] ?? null;
$alert_type                = $metabox["alert_type_{$cat_id}"] ?? null;
$alert_message             = $metabox["alert_message_{$cat_id}"] ?? "";

if($show_content_list == 'on'){
    $paged = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

    $args = [
        'post_type'      => $post_type,
        'posts_per_page' => 24,
        'paged'          => $paged,
    ];

    $tax_query = [];
    $taxonomies = get_object_taxonomies($post_type, 'names');

    foreach ($taxonomies as $taxonomy) {
        if (!empty($_GET[$taxonomy]) && is_array($_GET[$taxonomy])) {
            $tax_query[] = [
                'taxonomy' => $taxonomy,
                'field'    => 'term_id',
                'terms'    => array_map('intval', $_GET[$taxonomy]),
                'operator' => 'IN',
            ];
        }
    }

    if (!empty($tax_query)) {
        $args['tax_query'] = $tax_query;
    }

    if (!empty($_GET['search'])) {
        $args['s'] = sanitize_text_field($_GET['search']);
    }

    $items = new WP_Query($args);
}
?>

<!-- HERO + Breadcrumb -->
<div class="col-12 shadow bg-primary text-white">
    <div class="container bg-primary">
        <div class="row justify-content-center">
            <div class="col-lg-8 px-lg-4 py-lg-2 me-auto">
                <div class="cmp-hero">
                    <section class="it-hero-wrapper align-items-start">
                        <div class="row">
                            <div class="col-12 pt-0 ps-0 pl-20 pb-4 pb-lg-60">
                                <div class="cmp-breadcrumbs mt-0 pt-4" role="navigation">
                                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                                        <ol class="breadcrumb" style="display: flex; align-items: center;">
                                            <li class="breadcrumb-item text-white">
                                                <strong><a class="text-white" href="<?php echo home_url(); ?>">Home</a></strong>
                                            </li>
                                            <span class="text-white mx-1">/</span>
                                            <?php
                                            if (is_category() || is_archive()) {
                                                if ($current_cat->parent != 0) {
                                                    $ancestors = array_reverse(get_ancestors($current_cat->term_id, 'category'));
                                                    foreach ($ancestors as $ancestor_id) {
                                                        $ancestor = get_category($ancestor_id);
                                                        echo '<li class="breadcrumb-item text-white"><a class="text-white" href="' . get_category_link($ancestor->term_id) . '">' . esc_html($ancestor->name) . '</a></li>';
                                                        echo '<span class="text-white mx-1">/</span>';
                                                    }
                                                }
                                                echo '<li class="breadcrumb-item active text-white" aria-current="page">' . esc_html($current_cat->name) . '</li>';
                                            }
                                            ?>
                                        </ol>
                                    </nav>
                                </div>
                                <h1 class="text-white hero-title"><?php single_cat_title(); ?></h1>
                                <p class="hero-text text-white" style="text-align: justify;"><?php
                                    $cat_description = get_category_page_description( $current_cat );
                                    if ( '' !== $cat_description ) {
                                        echo esc_html( $cat_description );
                                    }
                                ?></p>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <!-- Menu sottocategorie -->
            <?php if($show_subcategories_header == "on"): ?>
            <div class="col-lg-3 offset-lg-1 pt-3">
                <aside id="section-menu">
                    <nav class="section-menu mb-4">
                        <div class="link-list-wrapper">
                            <ul class="link-list">
                                <?php
                                if ($subcategories) {            
                                    foreach ($subcategories as $subcategory) {
                                        $external_link = get_term_meta( $subcategory->term_id, 'external_link', true );
                                        $subcat_permalink = !empty($external_link)? $external_link : get_category_link($subcategory->term_id);
                                        echo '<li class="p-0 m-0">';
                                        echo '<a class="text-white pt-0 pb-1 text-decoration-none" href="' . $subcat_permalink . '">' . esc_html($subcategory->name) . '</a>';
                                        echo '</li>';
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                    </nav>
                </aside>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Alert -->
<?php if($alert_active == 'on'):?>
<div class="py-4 container">
    <?php get_template_part('template-parts/alert-component', null, ['active'=>$alert_active, 'type'=>$alert_type, 'message'=>$alert_message]); ?>
</div>
<?php endif;?>

<!-- Grid sottocategorie -->
<?php if($show_subcategories_page == "on"):?>
<div class="py-5 container">
    <?php 
    get_template_part('template-parts/grid', null, [
        'card_type' => 'card-category-1',
        'items'     => $subcategories,
        'is_post'   => false
    ]);
    ?>
</div>
<?php endif; ?>

<!-- CONTENT -->
<?php if($show_content_list == 'on'): ?>
  <div class="container py-5">
      <div class="row">
          <?php if($show_content_filters == 'on'): ?>
          <aside class="col-12 col-md-3 sidebar d-none d-lg-block">
              <form role="search" method="get" class="search-form" id="filter-form" action="">
                  <div id="accordionFilters">
                      <?php
                      $taxonomies = [];

                        $registered_taxonomies = get_object_taxonomies($post_type, 'objects');

                        foreach ($registered_taxonomies as $taxonomy) {
                            $taxonomies[$taxonomy->name] = $taxonomy->label;
                        }
                      foreach ( $taxonomies as $taxonomy => $label ) :
                          $terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => false]);
                          if ( empty($terms) || is_wp_error($terms) ) continue;
                          $accordion_id = 'collapse-' . $taxonomy;
                          $heading_id   = 'heading-' . $taxonomy;
                      ?>
                      <div class="border-bottom accordion-item">
                          <h2 class="accordion-header" id="<?php echo $heading_id; ?>">
                            <button class="accordion-button collapsed py-3 text-primary" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#<?php echo $accordion_id; ?>"
                                    aria-expanded="false"
                                    aria-controls="<?php echo $accordion_id; ?>">
                              <?php echo esc_html( $label ); ?>
                              <span class="accordion-icon ms-auto">
                                <svg class="icon icon-sm icon-primary" aria-hidden="true">
                                  <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-expand"></use>
                                </svg>
                              </span>
                            </button>
                          </h2>
                          <div id="<?php echo $accordion_id; ?>" class="accordion-collapse collapse" aria-labelledby="<?php echo $heading_id; ?>">
                              <div class="accordion-body px-0 pt-0 pb-3">
                                  <ul class="list-unstyled mb-0">
                                      <?php foreach ( $terms as $term ) :
                                          $checked = ( isset($_GET[$taxonomy]) && is_array($_GET[$taxonomy]) && in_array($term->term_id, $_GET[$taxonomy]) ) ? 'checked' : '';
                                      ?>
                                      <li class="mb-2">
                                          <div class="form-check my-0">
                                              <input type="checkbox"
                                                    class="form-check-input"
                                                    name="<?php echo $taxonomy; ?>[]"
                                                    value="<?php echo $term->term_id; ?>"
                                                    id="check-<?php echo $taxonomy; ?>-<?php echo $term->term_id; ?>"
                                                    <?php echo $checked; ?>>
                                              <label class="form-check-label" for="check-<?php echo $taxonomy; ?>-<?php echo $term->term_id; ?>">
                                                  <?php echo esc_html( $term->name ); ?>
                                              </label>
                                          </div>
                                      </li>
                                      <?php endforeach; ?>
                                  </ul>
                              </div>
                          </div>
                      </div>
                      <?php endforeach; ?>
                  </div>
                  <div class="my-4 justify-content-end gap-3 d-flex">
                      <button class="btn btn-primary py-2 px-2 px-lg-3" type="submit">Applica Filtri</button>
                      <button
                        class="btn btn-secondary btn-reset py-2 px-2 px-lg-3"
                        onclick="window.location.href = window.location.origin + window.location.pathname; return false;">
                        Reset
                      </button>
                  </div>
              </form>
          </aside>
          <?php endif; ?>

          <div class="col-12 <?php echo $show_content_filters == "on" ? 'col-lg-9' : '' ?>">
              <?php if($show_content_search == 'on'): ?>
              <form role="search" id="search-form" method="get" class="search-form d-flex align-items-end mb-3" action="">
                  <div class="cmp-input-search mr-3 flex-grow-1">
                      <div class="form-group autocomplete-wrapper mb-0">
                          <label for="autocomplete-two" class="visually-hidden active">Cerca</label>
                          <input value="<?php echo isset($_GET['search']) ? esc_attr($_GET['search']) : ''; ?>" type="search" class="autocomplete form-control" placeholder="Cerca per parola chiave" id="autocomplete-two" name="search" data-bs-autocomplete="[]" aria-labelledby="autocomplete-label"><ul class="autocomplete-list"></ul>
                          <span class="autocomplete-icon" aria-hidden="true">
                            <svg class="icon icon-sm icon-primary" role="img" aria-labelledby="autocomplete-label">
                              <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-search"></use>
                            </svg>
                          </span>
                      </div>
                  </div>
                  <div class="form-group mb-0">
                      <button class="btn btn-primary py-2" type="submit" id="search-button">Invio</button>
                  </div>
              </form>
              <div class="d-flex justify-content-between align-items-center w-100">
                  <span id="main-container" class="text-primary title-xsmall-semi-bold ms-1">
                      <?php 
                        $count = $items->found_posts; 
                        echo $count . ' ' . _n('risultato trovato', 'risultati trovati', $count, 'textdomain'); 
                      ?>
                  </span>
                  <button class="btn p-0 pe-2 d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMobileFilters" aria-controls="offcanvasMobileFilters">
                      <span class="rounded-icon">
                          <svg class="icon icon-primary icon-xs mb-1" aria-hidden="true">
                              <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-funnel"></use>
                          </svg>
                      </span>
                      <span class="text-primary title-xsmall-semi-bold ms-1">
                          Filtra
                      </span>
                  </button>
              </div>
              <?php endif;?>

              <?php if(isset($items) && $items->have_posts()): ?>
              <div class="container mt-3 px-0">
                  <div class="row row-cols-1 row-cols-md-2 <?php echo $show_content_filters == "on" ? '' : 'row-cols-lg-3' ?> g-4">
                      <?php  
                      while ( $items->have_posts() ){
                          $items->the_post();
                          $post_type = get_post_type();
                          $card_type = (function () use ($post_type) {
                              $map = function_exists('get_card_type') ? get_card_type() : [];
                              return isset($map[$post_type]) ? array_key_first($map[$post_type]) : 'card-generic-post-1';
                          })(); 
                          ?>
                          <div class="col d-flex">
                              <?php get_template_part('template-parts/loop/'.$card_type, null, get_post()); ?>
                          </div>
                      <?php
                      }
                      ?>
                  </div>
              </div>
              <?php else: ?>
              <div class="container py-5 text-center">
                  <p>Nessun risultato disponibile.</p>
              </div>
              <?php endif; wp_reset_postdata(); ?>

              <!-- Paginazione migliorata -->
              <nav aria-label="Pagination" class="mt-5">
                  <ul class="pagination justify-content-center">
                      <?php
                      $args_for_pagination = $_GET;
                      unset($args_for_pagination['page']);
                      if(!empty($items)){
                        $pagination_links = paginate_links([
                            'total'     => $items->max_num_pages,
                            'current'   => $paged,
                            'prev_text' => '&laquo;',
                            'next_text' => '&raquo;',
                            'format'    => '?page=%#%',
                            'add_args'  => $args_for_pagination,
                            'type'      => 'array',
                        ]);
                        if ( is_array($pagination_links) ) {
                          foreach ( $pagination_links as $link ) {
                              $class = 'page-item';
                              if (strpos($link, 'current') !== false) $class .= ' active';
                              $link = str_replace('page-numbers', 'page-link', $link);
                              echo '<li class="' . $class . '">' . $link . '</li>';
                          }
                        }
                      }
                      ?>
                  </ul>
              </nav>
          </div>
      </div>
  </div>
<?php endif;?>

<!-- Gruppi extra -->
<?php 
if(!empty($post_list_group)){
    foreach ($post_list_group as $args):?>
        <div class="container">
            <?php get_template_part('template-parts/post-list-component', null, $args); ?>
        </div>
<?php endforeach; 
}
?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    function appendParams(formSearch, formTerm){
      let filteredParams = new URLSearchParams();

      if(formSearch && formSearch.search.value){
        filteredParams.append("search", formSearch.search.value);
      }
      if(formTerm){
        Array.from(formTerm.elements).forEach(el => {
          if (!el.name) return;
          if ((el.type === 'checkbox' || el.type === 'radio') && !el.checked) return;
          if (!el.value.trim()) return;
          
          if (el.name.endsWith('[]')) {
              filteredParams.append(el.name, el.value);
          }
        });
      }
      
      const newUrl = window.location.pathname + (filteredParams.toString() ? "?" + filteredParams.toString() : "");
      window.location.href = newUrl;
    }

    function handleFormSubmit() {
        const formSearch = document.getElementById("search-form");
        const formTerm = document.getElementById("filter-form");
      
        formSearch.addEventListener("submit", function(e) {
            e.preventDefault();
            appendParams(formSearch, formTerm);
        });
        formTerm.addEventListener("submit", function(e) {
            e.preventDefault();
            appendParams(formSearch, formTerm);
        });
    }

    handleFormSubmit();
});
</script>

<?php get_footer(); ?>
