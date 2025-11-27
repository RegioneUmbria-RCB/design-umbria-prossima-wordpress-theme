<?php
get_header();

$paged = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

$post_types_selected = isset($_GET['post_type']) && is_array($_GET['post_type']) 
                       ? array_map('sanitize_text_field', $_GET['post_type']) 
                       : ['any'];

$argomenti_selected = isset($_GET['argomenti']) && is_array($_GET['argomenti']) 
                      ? array_map('sanitize_text_field', $_GET['argomenti']) 
                      : [];

$args = [
    'post_type'      => $post_types_selected,
    'posts_per_page' => 24,
    'paged'          => $paged,
];

if (!empty($_GET['s'])) {
    $args['s'] = sanitize_text_field($_GET['s']);
}

if (!empty($argomenti_selected)) {
    $args['tax_query'] = [
        [
            'taxonomy' => 'argomenti',
            'field'    => 'slug',
            'terms'    => $argomenti_selected,
        ]
    ];
}

$items = new WP_Query($args);
?>

<div class="container py-5">
    <div class="row">

        <aside class="col-12 col-md-3 sidebar d-none d-lg-block">

            <form role="search" method="get" class="search-form" id="filter-form" action="">
                <div id="accordionFilters">

                    <?php 
                    $open_post_types = (!empty($post_types_selected) && $post_types_selected[0] !== 'any') ? 'show' : '';
                    ?>

                    <div class="border-bottom accordion-item">
                        <h2 class="accordion-header" id="heading-post-type">
                          <button class="accordion-button py-3 text-primary <?php echo $open_post_types ? '' : 'collapsed'; ?>" 
                                  type="button"
                                  data-bs-toggle="collapse"
                                  data-bs-target="#collapse-post-type"
                                  aria-expanded="<?php echo $open_post_types ? 'true' : 'false'; ?>"
                                  aria-controls="collapse-post-type">

                            Tipi di post

                            <span class="accordion-icon ms-auto">
                              <svg class="icon icon-sm icon-primary" aria-hidden="true">
                                <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-expand"></use>
                              </svg>
                            </span>
                          </button>
                        </h2>

                        <div id="collapse-post-type" 
                             class="accordion-collapse collapse <?php echo $open_post_types; ?>" 
                             aria-labelledby="heading-post-type">
                            <div class="accordion-body px-0 pt-0 pb-3">

                                <?php
                                $all_post_types = get_post_types(['public' => true], 'objects');
                                $post_types = [];

                                foreach ($all_post_types as $pt_slug => $pt_obj) {
                                    if ($pt_slug === 'attachment') continue;
                                    $post_types[$pt_slug] = $pt_obj->labels->singular_name;
                                }

                                foreach ($post_types as $pt_slug => $pt_label) :
                                    $checked = in_array($pt_slug, $post_types_selected) ? 'checked' : '';
                                ?>

                                <div class="form-check my-2">
                                    <input type="checkbox"
                                           class="form-check-input"
                                           name="post_type[]"
                                           value="<?php echo esc_attr($pt_slug); ?>"
                                           id="check-<?php echo esc_attr($pt_slug); ?>"
                                           <?php echo $checked; ?>>
                                    <label class="form-check-label" for="check-<?php echo esc_attr($pt_slug); ?>">
                                        <?php echo esc_html($pt_label); ?>
                                    </label>
                                </div>

                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <?php 
                    $open_args = !empty($argomenti_selected) ? 'show' : '';
                    ?>

                    <div class="border-bottom accordion-item">
                        <h2 class="accordion-header" id="heading-argomenti">
                          <button class="accordion-button py-3 text-primary <?php echo $open_args ? '' : 'collapsed'; ?>"
                                  type="button"
                                  data-bs-toggle="collapse"
                                  data-bs-target="#collapse-argomenti"
                                  aria-expanded="<?php echo $open_args ? 'true' : 'false'; ?>"
                                  aria-controls="collapse-argomenti">

                            Argomenti

                            <span class="accordion-icon ms-auto">
                              <svg class="icon icon-sm icon-primary" aria-hidden="true">
                                <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-expand"></use>
                              </svg>
                            </span>
                          </button>
                        </h2>

                        <div id="collapse-argomenti" 
                             class="accordion-collapse collapse <?php echo $open_args; ?>" 
                             aria-labelledby="heading-argomenti">

                            <div class="accordion-body px-0 pt-0 pb-3">

                                <?php
                                $terms = get_terms([
                                    'taxonomy' => 'argomenti',
                                    'hide_empty' => false
                                ]);

                                if (!empty($terms) && !is_wp_error($terms)) :
                                    foreach ($terms as $term) :
                                        $checked = in_array($term->slug, $argomenti_selected) ? 'checked' : '';
                                ?>

                                <div class="form-check my-2">
                                    <input type="checkbox"
                                           class="form-check-input"
                                           name="argomenti[]"
                                           value="<?php echo esc_attr($term->slug); ?>"
                                           id="check-arg-<?php echo esc_attr($term->slug); ?>"
                                           <?php echo $checked; ?>>
                                    <label class="form-check-label" for="check-arg-<?php echo esc_attr($term->slug); ?>">
                                        <?php echo esc_html($term->name); ?>
                                    </label>
                                </div>

                                <?php 
                                    endforeach;
                                endif;
                                ?>

                            </div>
                        </div>
                    </div>

                </div>

                <div class="my-4 justify-content-end gap-3 d-flex">
                    <button class="btn btn-primary py-2 px-2 px-lg-3" type="submit">Applica Filtri</button>
                    <button class="btn btn-secondary py-2 px-2 px-lg-3"
                            onclick="window.location.href = window.location.origin + window.location.pathname + '?s='; return false;">
                        Reset
                    </button>
                </div>

            </form>
        </aside>

        <div class="col-12 col-lg-9">

            <form role="search" id="search-form" method="get" class="search-form d-flex align-items-end mb-3" action="">
                <div class="cmp-input-search mr-3 flex-grow-1">
                    <div class="form-group autocomplete-wrapper mb-0">
                        <label for="autocomplete-two" class="visually-hidden active">Cerca</label>
                        <input value="<?php echo isset($_GET['s']) ? esc_attr($_GET['s']) : ''; ?>"
                               type="search"
                               class="autocomplete form-control"
                               placeholder="Cerca per parola chiave"
                               id="autocomplete-two"
                               name="s"
                               data-bs-autocomplete="[]">
                        <ul class="autocomplete-list"></ul>
                        <span class="autocomplete-icon" aria-hidden="true">
                          <svg class="icon icon-sm icon-primary">
                            <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-search"></use>
                          </svg>
                        </span>
                    </div>
                </div>

                <div class="form-group mb-0">
                    <button class="btn btn-primary py-2" type="submit">Invio</button>
                </div>
            </form>

            <div class="d-flex justify-content-between align-items-center w-100">
                <span class="text-primary title-xsmall-semi-bold ms-1">
                    <?php 
                    $count = $items->found_posts; 
                    echo $count . ' ' . _n('risultato trovato', 'risultati trovati', $count); 
                    ?>
                </span>

                <button class="btn p-0 pe-2 d-lg-none" type="button"
                        data-bs-toggle="offcanvas" data-bs-target="#offcanvasMobileFilters">
                    <span class="rounded-icon">
                        <svg class="icon icon-primary icon-xs mb-1">
                            <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-funnel"></use>
                        </svg>
                    </span>
                    <span class="text-primary title-xsmall-semi-bold ms-1">Filtra</span>
                </button>
            </div>

            <?php if($items->have_posts()): ?>
            <div class="container mt-3 px-0">
                <div class="row row-cols-1 g-4">

                    <?php while ($items->have_posts()): $items->the_post(); ?>
                    <div class="col d-flex">
                        <?php get_template_part('template-parts/loop/card-generic-post-1', null, get_post()); ?>
                    </div>
                    <?php endwhile; ?>

                </div>
            </div>

            <?php else: ?>
            <div class="container py-5 text-center">
                <p>Nessun risultato disponibile.</p>
            </div>
            <?php endif; wp_reset_postdata(); ?>

            <nav aria-label="Pagination" class="mt-5">
                <ul class="pagination justify-content-center">

                <?php
                $args_for_pagination = $_GET;
                unset($args_for_pagination['page']);

                $pagination_links = paginate_links([
                    'total'     => $items->max_num_pages,
                    'current'   => $paged,
                    'prev_text' => '&laquo;',
                    'next_text' => '&raquo;',
                    'format'    => '?page=%#%',
                    'add_args'  => $args_for_pagination,
                    'type'      => 'array',
                ]);

                if (is_array($pagination_links)) {
                    foreach ($pagination_links as $link) {
                        $class = 'page-item';
                        if (strpos($link, 'current') !== false) $class .= ' active';
                        $link = str_replace('page-numbers', 'page-link', $link);
                        echo '<li class="' . $class . '">' . $link . '</li>';
                    }
                }
                ?>

                </ul>
            </nav>

        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    function appendParams(formSearch, formTerm){
      let filteredParams = new URLSearchParams();

      if(formSearch){
        filteredParams.append("s", formSearch.s.value ?? "");
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
});
</script>

<?php get_footer(); ?>
