<?php

if (!empty($args) && is_array($args)) {
    $layout_sezione   = $args['layout'] ?? null;
    $titolo_sezione   = $args['title'] ?? '';
    $tipo_selezione   = $args['selection_mode'] ?? null;
    $titolo_position  = $args['title_position'] ?? 'center';
    $titolo_divider   = $args['show_title_divider'] ?? false;
    $tipo_scorrimento = $args['scorrimento'] ?? 'dots';
    $footer_label     = $args['footer_link_label'] ?? '';
    $footer_link      = $args['footer_link_url'] ?? '';
    $footer_link_button   = $args['footer_show_as_button'] ?? false;
    

    if (!empty($tipo_selezione)) {
        switch ($tipo_selezione) {
            case 'post':
                $post_type = $args['post_type'] ?? null;

                if (!empty($post_type)) {
                    $tipo_filtro      = $args['filter_by'] ?? null;
                    $tipo_card        = $args['card_type'] ?? (function () use ($post_type) {
                        $map = function_exists('get_card_type') ? get_card_type() : [];
                        return isset($map[$post_type]) ? array_key_first($map[$post_type]) : 'card-generic-post-1';
                    })();
                    $limite_elementi  = intval($args['max_items'] ?? 6);

                    $query_args = [
                        'post_type'           => $post_type,
                        'order'               => 'DESC',
                        'orderby'             => 'date',
                        'ignore_sticky_posts' => true,
                        'posts_per_page'      => $limite_elementi,
                    ];

                    if (!empty($tipo_filtro)) {
                        switch ($tipo_filtro) {
                            case 'category':
                                $category = $args['category_select'] ?? null;
                                if (!empty($category)) {
                                    $query_args['cat'] = intval($category);
                                }
                                break;

                            case 'taxonomy':
                                $taxonomy = $args['taxonomy_select'] ?? '';
                                if (!empty($taxonomy)) {
                                    [$taxonomy_name, $term_id] = array_pad(explode(':', $taxonomy, 2), 2, null);
                                    if (!empty($taxonomy_name)) {
                                        $tax_item = [
                                            'taxonomy' => $taxonomy_name,
                                            'operator' => 'IN',
                                        ];
                                        if (!empty($term_id)) {
                                            $tax_item['field'] = 'term_id';
                                            $tax_item['terms'] = [intval($term_id)];
                                        }
                                        $query_args['tax_query'] = [$tax_item];
                                    }
                                }
                                break;

                            case 'manual':
                                if (!empty($args['manual_posts'])) {
                                    $post_ids_str = explode(',', $args['manual_posts']);
                                    $post_ids_int = array_map('absint', $post_ids_str);
                                    $post_ids     = array_values(array_filter($post_ids_int));

                                    if (!empty($post_ids)) {
                                        $query_args['post__in']       = $post_ids;
                                        $query_args['orderby']        = 'post__in';
                                        $query_args['posts_per_page'] = count($post_ids);
                                    }
                                }
                                break;
                        }
                    }

                    $query_results = new WP_Query($query_args);

                    if ($query_results->have_posts()) {
                        ?>
                        <div class="py-5">
                            <?php if (!empty($titolo_sezione)) : ?>
                                <h3 class="mb-4 text-<?php echo esc_attr($titolo_position); ?>">
                                    <?php echo esc_html($titolo_sezione); ?>
                                </h3>
                                <?php if ($titolo_divider) : ?>
                                    <hr style="margin-top:-1rem;" />
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php
                            $template_part_name = ($layout_sezione === 'griglia') ? 'grid' : 'carousel-1';
                            if ($layout_sezione === 'carosello' && $tipo_scorrimento !== 'dots') {
                                $template_part_name = 'carousel-2';
                            }
                            get_template_part(
                                'template-parts/' . $template_part_name,
                                null,
                                [
                                    'card_type' => $tipo_card,
                                    'items'     => $query_results,
                                    'is_post'   => true
                                ]
                            );
                            if (!empty($footer_label) && !empty($footer_link)) :
                            ?>
                                <?php if($footer_link_button):?>
                                    <div class="d-block text-end pt-4">
                                        <a href="<?php echo esc_url($footer_link); ?>" class="h6 btn bg-primary text-white" data-focus-mouse="false">
                                            <?php echo esc_html($footer_label); ?>
                                        </a>
                                    </div>
                                <?php else:?>
                                    <div class="d-block text-end pt-4">
                                        <a href="<?php echo esc_url($footer_link); ?>" class="h6 text-decoration-none text-primary" data-focus-mouse="false">
                                            <?php echo esc_html($footer_label); ?> ➔
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <?php
                        wp_reset_postdata();
                    }
                }
                break;

            case 'categories':
                $cat_ids = $args['categories_checklist'] ?? null;
                $tipo_card = 'card-category-1';

                if (!empty($cat_ids)) {
                    $query_results = get_terms([
                        'taxonomy'   => 'category',
                        'include'    => $cat_ids,
                        'hide_empty' => false,
                    ]);

                    if (!empty($query_results)) {
                        ?>
                        <div class="py-5">
                            <?php if (!empty($titolo_sezione)) : ?>
                                <h3 class="mb-4 text-<?php echo esc_attr($titolo_position); ?>">
                                    <?php echo esc_html($titolo_sezione); ?>
                                </h3>
                                <?php if ($titolo_divider) : ?>
                                    <hr style="margin-top:-1rem;" />
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php
                            $template_part_name = ($layout_sezione === 'griglia') ? 'grid' : 'carousel-1';
                            if ($layout_sezione === 'carosello' && $tipo_scorrimento !== 'dots') {
                                $template_part_name = 'carousel-2';
                            }
                            get_template_part(
                                'template-parts/' . $template_part_name,
                                null,
                                [
                                    'card_type' => $tipo_card,
                                    'items'     => $query_results,
                                    'is_post'   => false
                                ]
                            );
                            if (!empty($footer_label) && !empty($footer_link)) :
                            ?>
                                <?php if($footer_link_button):?>
                                    <div class="d-block text-end pt-4">
                                        <a href="<?php echo esc_url($footer_link); ?>" class="h6 btn bg-primary text-white" data-focus-mouse="false">
                                            <?php echo esc_html($footer_label); ?>
                                        </a>
                                    </div>
                                <?php else:?>
                                    <div class="d-block text-end pt-4">
                                        <a href="<?php echo esc_url($footer_link); ?>" class="h6 text-decoration-none text-primary" data-focus-mouse="false">
                                            <?php echo esc_html($footer_label); ?> ➔
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <?php
                    }
                }
                break;

            case 'taxonomies':
                $taxonomy       = $args['selected_taxonomy'] ?? null;
                $selected_terms = $args['selected_terms'] ?? [];

                if (!empty($taxonomy) && !empty($selected_terms)) {
                    $tipo_card = $args['card_type_taxonomy'] ?? (function () use ($taxonomy) {
                        $map = function_exists('get_card_taxonomies') ? get_card_taxonomies() : [];
                        return isset($map[$taxonomy]) ? array_key_first($map[$taxonomy]) : 'default';
                    })();

                    $ids = [];
                    foreach ($selected_terms as $term) {
                        [$tax, $id] = explode(':', $term);
                        if ($tax === $taxonomy) {
                            $ids[] = $id;
                        }
                    }

                    $query_results = get_terms([
                        'taxonomy'   => $taxonomy,
                        'include'    => $ids,
                        'hide_empty' => false,
                    ]);

                    if (!empty($query_results)) {
                        ?>
                        <div class="py-5">
                            <?php if (!empty($titolo_sezione)) : ?>
                                <h3 class="mb-4 text-<?php echo esc_attr($titolo_position); ?>">
                                    <?php echo esc_html($titolo_sezione); ?>
                                </h3>
                                <?php if ($titolo_divider) : ?>
                                    <hr style="margin-top:-1rem;" />
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php
                            $template_part_name = ($layout_sezione === 'griglia') ? 'grid' : 'carousel-1';
                            if ($layout_sezione === 'carosello' && $tipo_scorrimento !== 'dots') {
                                $template_part_name = 'carousel-2';
                            }
                            get_template_part(
                                'template-parts/' . $template_part_name,
                                null,
                                [
                                    'card_type' => $tipo_card,
                                    'items'     => $query_results,
                                    'is_post'   => false
                                ]
                            );
                            if (!empty($footer_label) && !empty($footer_link)) :
                            ?>
                                <?php if($footer_link_button):?>
                                    <div class="d-block text-end pt-4">
                                        <a href="<?php echo esc_url($footer_link); ?>" class="h6 btn bg-primary text-white" data-focus-mouse="false">
                                            <?php echo esc_html($footer_label); ?>
                                        </a>
                                    </div>
                                <?php else:?>
                                    <div class="d-block text-end pt-4">
                                        <a href="<?php echo esc_url($footer_link); ?>" class="h6 text-decoration-none text-primary" data-focus-mouse="false">
                                            <?php echo esc_html($footer_label); ?> ➔
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <?php
                    }
                }
                break;
        }
    }
}