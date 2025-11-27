<?php

add_filter('get_terms_args', function ($args, $taxonomies) {
    if (!is_admin()) return $args;

    // Verifica che stiamo lavorando con la tassonomia "argomenti"
    if (empty($taxonomies) || !in_array('argomenti', (array)$taxonomies, true)) {
        return $args;
    }

    // Se l'utente è amministratore → mostra tutto
    if (current_user_can('administrator')) {
        return $args;
    }

    $user = wp_get_current_user();
    if (empty($user->roles)) {
        $args['include'] = [0]; // mostra nulla
        return $args;
    }

    $user_role = $user->roles[0];
    $ruoli_argomenti = get_option('ruoli_editoriali_argomenti', []);
    $allowed_terms = isset($ruoli_argomenti[$user_role]) ? $ruoli_argomenti[$user_role] : [];

    // Se non ci sono termini autorizzati → mostra nulla
    if (empty($allowed_terms)) {
        $args['include'] = [0];
        return $args;
    }

    $args['include'] = $allowed_terms;
    return $args;
}, 10, 2);


add_action('admin_menu', 'permissions_filter_admin_menus', 999);
function permissions_filter_admin_menus() {
    if (!is_admin()) {
        return;
    }

    $option = get_option('permissions_metabox');
    $groups = array();
    if (!empty($option) && is_array($option)) {
        $groups = isset($option['permissions_group']) ? $option['permissions_group'] : $option;
    }

    $role_allowed = array();
    if (!empty($groups) && is_array($groups)) {
        foreach ($groups as $group) {
            if (empty($group) || !is_array($group)) continue;
            $pts = !empty($group['post_type']) ? (array) $group['post_type'] : array();
            $roles = !empty($group['user_role']) ? (array) $group['user_role'] : array();

            if (empty($pts) || empty($roles)) continue;

            foreach ($roles as $r) {
                if (!isset($role_allowed[$r])) $role_allowed[$r] = array();
                $role_allowed[$r] = array_values(array_unique(array_merge($role_allowed[$r], $pts)));
            }
        }
    }

    $current_user = wp_get_current_user();
    $user_roles = (array) $current_user->roles;

    $allowed_pt = array();
    foreach ($user_roles as $r) {
        if (!empty($role_allowed[$r]) && is_array($role_allowed[$r])) {
            $allowed_pt = array_merge($allowed_pt, $role_allowed[$r]);
        }
    }
    $allowed_pt = array_values(array_unique($allowed_pt));

    $all_pt = get_post_types(array('public' => true, 'show_ui' => true), 'names');

    foreach ($all_pt as $pt) {
        if (!in_array($pt, $allowed_pt, true)) {
            if ($pt === 'post') {
                remove_menu_page('edit.php');
            } else {
                remove_menu_page('edit.php?post_type=' . $pt);
            }
        }
    }
}


// Blocca accesso diretto alle pagine non permesse
add_action('current_screen', function($screen) {
    // Se non c'è un post_type, non bloccare
    if (empty($screen->post_type)) {
        return;
    }

    $current_user = wp_get_current_user();
    $user_roles = (array) $current_user->roles;

    $option = get_option('permissions_metabox');
    $groups = !empty($option['permissions_group']) ? $option['permissions_group'] : array();

    $role_allowed = array();
    foreach ($groups as $group) {
        if (empty($group) || !is_array($group)) continue;
        $pts = !empty($group['post_type']) ? (array) $group['post_type'] : array();
        $roles = !empty($group['user_role']) ? (array) $group['user_role'] : array();
        foreach ($roles as $r) {
            if (!isset($role_allowed[$r])) $role_allowed[$r] = array();
            $role_allowed[$r] = array_values(array_unique(array_merge($role_allowed[$r], $pts)));
        }
    }

    $allowed_pt = array();
    foreach ($user_roles as $r) {
        if (!empty($role_allowed[$r])) {
            $allowed_pt = array_merge($allowed_pt, $role_allowed[$r]);
        }
    }
    $allowed_pt = array_values(array_unique($allowed_pt));

    // Blocca solo se è un post_type registrato e non permesso
    $all_pt = get_post_types(array('public' => true, 'show_ui' => true), 'names');
    if (in_array($screen->post_type, $all_pt, true) && !in_array($screen->post_type, $allowed_pt, true)) {
        wp_die(__('Non hai i permessi per accedere a questa pagina.'));
    }
});


add_action('pre_get_posts', function ($query) {

    // Solo nella lista admin (non frontend, non REST, non export)
    if (!is_admin() || !$query->is_main_query() || $query->get('post_type') === 'attachment') {
        return;
    }

    // Controlla che siamo nella schermata di elenco post
    $screen = get_current_screen();
    if (!$screen || $screen->base !== 'edit') return;

    // Se l'utente è amministratore → mostra tutto
    if (current_user_can('administrator')) return;

    $user = wp_get_current_user();
    if (empty($user->roles)) return;

    // Prendi il primo ruolo dell'utente (puoi gestire più ruoli se vuoi)
    $user_role = $user->roles[0];

    // Recupera i termini associati al ruolo
    $ruoli_argomenti = get_option('ruoli_editoriali_argomenti', []);
    $allowed_terms = isset($ruoli_argomenti[$user_role]) ? $ruoli_argomenti[$user_role] : [];

    // Se non ci sono termini → non mostrare nulla
    if (empty($allowed_terms)) {
        $query->set('post__in', [0]); // Nessun risultato
        return;
    }

    // Applica il filtro tassonomia
    $tax_query = [
        [
            'taxonomy' => 'argomenti',
            'field'    => 'term_id',
            'terms'    => $allowed_terms,
            'operator' => 'IN',
        ]
    ];

    $existing_tax_query = $query->get('tax_query');
    if ($existing_tax_query) {
        $tax_query = array_merge($existing_tax_query, $tax_query);
    }

    $query->set('tax_query', $tax_query);
});



add_action('load-post.php', 'ruoli_editoriali_controlla_accesso_edit_post');
function ruoli_editoriali_controlla_accesso_edit_post() {
    // Controlli iniziali
    if (!is_admin()) return;
    if (!isset($_GET['post'])) return;
    $post_id = intval($_GET['post']);
    if ($post_id <= 0) return;

    // Permetti agli admin
    if (current_user_can('administrator')) return;

    $post = get_post($post_id);
    if (!$post) return;

    // Recupera ruolo utente
    $user = wp_get_current_user();
    if (empty($user->roles)) return;
    $user_role = $user->roles[0];

    // Recupera argomenti permessi per il ruolo
    $ruoli_argomenti = get_option('ruoli_editoriali_argomenti', []);
    $allowed_terms = isset($ruoli_argomenti[$user_role]) ? $ruoli_argomenti[$user_role] : [];

    // Se non ha termini permessi, blocco
    if (empty($allowed_terms)) {
        wp_die(__('Non hai il permesso di modificare questo post.'), 403);
    }

    // Recupera i termini "argomenti" del post
    $post_terms = wp_get_post_terms($post_id, 'argomenti', ['fields' => 'ids']);
    if (is_wp_error($post_terms)) {
        wp_die(__('Errore nel controllo dei termini.'), 500);
    }

    // Verifica intersezione
    $intersection = array_intersect($allowed_terms, $post_terms);
    if (empty($intersection)) {
        // Nessun termine in comune => accesso negato
        wp_die(__('Non hai il permesso di modificare questo post.'), 403);
    }

    // Altrimenti, accesso ok
}


add_action('admin_menu', 'gestione_ruoli_editoriali');

function gestione_ruoli_editoriali() {
    add_users_page(
        'Gestione Ruoli Editoriali',
        'Gestione Ruoli Editoriali',
        'edit_posts',
        'gestione_ruoli_editoriali_completo',
        'pagina_ruoli_editoriali_completo'
    );
}

function ruoli_editoriali_get_caps_for_action($action, $post_types) {
    $caps = [];
    foreach ($post_types as $ptype) {
        $pt = get_post_type_object($ptype);
        if (!$pt || empty($pt->cap)) continue;

        $cap_obj = $pt->cap;
        switch ($action) {
            case 'edit':
                $caps = array_merge($caps, array_filter([
                    $cap_obj->edit_posts ?? '',
                    $cap_obj->edit_others_posts ?? '',
                    $cap_obj->edit_published_posts ?? '',
                    $cap_obj->edit_private_posts ?? ''
                ]));
                break;
            case 'publish':
                if (!empty($cap_obj->publish_posts)) $caps[] = $cap_obj->publish_posts;
                break;
            case 'delete':
                $caps = array_merge($caps, array_filter([
                    $cap_obj->delete_posts ?? '',
                    $cap_obj->delete_others_posts ?? '',
                    $cap_obj->delete_published_posts ?? '',
                    $cap_obj->delete_private_posts ?? ''
                ]));
                break;
        }
    }
    return array_unique($caps);
}

function ruoli_editoriali_role_has_all_caps($role_obj, $type, $range) {
    if (!$role_obj) return false;
    if($type === "publish"){
        if (empty($role_obj->capabilities[$type.'_posts'])) return false;
    } else {
        if($range === "all"){
            if (empty($role_obj->capabilities[$type.'_others_posts'])) return false;
        } else {
            if (empty($role_obj->capabilities[$type.'_posts'])) return false;
        }
    }
    return true;   
}

function pagina_ruoli_editoriali_completo() {
    global $wp_roles;
    $actions = ['edit', 'publish', 'delete'];
    $post_types = get_post_types(['show_ui' => true], 'names');
    $taxonomy = 'argomenti';

    // Recupera argomenti per ruolo
    $ruoli_argomenti = get_option('ruoli_editoriali_argomenti', []);

    // --- Aggiungi nuovo ruolo ---
    if (isset($_POST['aggiungi_ruolo']) && !empty($_POST['nome_ruolo'])) {
        $slug = sanitize_title($_POST['nome_ruolo']);
        $nome = sanitize_text_field($_POST['nome_ruolo']);
        $capabilities = ['read' => true, 'edit_posts' => true];

        foreach ($actions as $action) {
            $caps_action = ruoli_editoriali_get_caps_for_action($action, $post_types);
            foreach ($caps_action as $cap) {
                if (!isset($capabilities[$cap])) $capabilities[$cap] = false;
            }
        }

        if (!get_role($slug)) {
            add_role($slug, $nome, $capabilities);
            echo '<div class="notice notice-success"><p>Ruolo "'.esc_html($nome).'" aggiunto!</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>Il ruolo esiste già!</p></div>';
        }
    }

    // --- Elimina ruolo ---
    if (isset($_POST['elimina_ruolo']) && !empty($_POST['role_slug'])) {
        $slug = sanitize_text_field($_POST['role_slug']);
        if ($slug !== 'administrator' && get_role($slug)) {
            remove_role($slug);
            unset($ruoli_argomenti[$slug]);
            update_option('ruoli_editoriali_argomenti', $ruoli_argomenti);
            echo '<div class="notice notice-success"><p>Ruolo eliminato!</p></div>';
        }
    }

    // --- Salva capabilities + argomenti ---
    if (isset($_POST['salva_caps']) && isset($_POST['caps'])) {
        foreach ($_POST['caps'] as $role_slug => $role_caps) {
            if ($role_slug === 'administrator') continue;
            $role = get_role($role_slug);
            if (!$role) continue;

            // Edit
            $edit_caps = ruoli_editoriali_get_caps_for_action('edit', $post_types);
            $edit_own_caps = array_filter($edit_caps, fn($c) => strpos($c,'edit_others')===false);
            $edit_all_caps = $edit_caps;
            foreach ($edit_caps as $cap) $role->remove_cap($cap);
            if (isset($role_caps['edit'])) {
                if ($role_caps['edit'] === 'own') foreach ($edit_own_caps as $cap) $role->add_cap($cap);
                elseif ($role_caps['edit'] === 'all') foreach ($edit_all_caps as $cap) $role->add_cap($cap);
            }

            // Publish
            $publish_caps = ruoli_editoriali_get_caps_for_action('publish', $post_types);
            foreach ($publish_caps as $cap) $role->remove_cap($cap);
            if (!empty($role_caps['publish'])) foreach ($publish_caps as $cap) $role->add_cap($cap);

            // Delete
            $delete_caps = ruoli_editoriali_get_caps_for_action('delete', $post_types);
            $delete_own_caps = array_filter($delete_caps, fn($c)=> strpos($c,'delete_others')===false);
            foreach ($delete_caps as $cap) $role->remove_cap($cap);
            if (!empty($role_caps['delete'])) {
                if ($role_caps['delete'] === 'own') foreach ($delete_own_caps as $cap) $role->add_cap($cap);
                elseif ($role_caps['delete'] === 'all') foreach ($delete_caps as $cap) $role->add_cap($cap);
            }

            $role->add_cap('read');
            $role->add_cap('edit_posts');

            // Salva argomenti
            $selected_terms = isset($_POST['argomenti'][$role_slug]) ? array_map('intval', $_POST['argomenti'][$role_slug]) : [];
            $ruoli_argomenti[$role_slug] = $selected_terms;
        }

        update_option('ruoli_editoriali_argomenti', $ruoli_argomenti);
        echo '<div class="notice notice-success"><p>Capabilities e argomenti aggiornati!</p></div>';
    }

    // Recupera termini della tassonomia
    $argomenti_terms = get_terms([
        'taxonomy' => $taxonomy,
        'hide_empty' => false,
    ]);
    ?>

    <div class="wrap">
        <h1>Gestione Ruoli Editoriali</h1>

        <h2>Aggiungi nuovo ruolo</h2>
        <form method="post">
            <input type="text" name="nome_ruolo" required placeholder="Nome del ruolo">
            <input type="submit" name="aggiungi_ruolo" class="button button-primary" value="Aggiungi ruolo">
        </form>

        <h2>Ruoli esistenti</h2>
        <form method="post">
            <table class="widefat">
                <thead>
                    <tr>
                        <th>Ruolo</th>
                        <th>Edit</th>
                        <th>Publish</th>
                        <th>Delete</th>
                        <th>Argomenti</th>
                        <th>Elimina</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $ruoli_standard = ['subscriber','contributor','author','editor','administrator'];
                foreach ($wp_roles->roles as $slug => $info):
                    if (in_array($slug, $ruoli_standard)) continue;
                    $role_obj = get_role($slug);
                    $has_edit_all = ruoli_editoriali_role_has_all_caps($role_obj, 'edit', 'all');
                    $has_edit_own = ruoli_editoriali_role_has_all_caps($role_obj, 'edit', 'own') && !$has_edit_all;
                    $has_publish = ruoli_editoriali_role_has_all_caps($role_obj, 'publish', null);
                    $has_delete_all = ruoli_editoriali_role_has_all_caps($role_obj, 'delete', 'all');
                    $has_delete_own = ruoli_editoriali_role_has_all_caps($role_obj, 'delete', 'own') && !$has_delete_all;
                    $has_delete_disabled = !$has_delete_own && !$has_delete_all;
                    $selected_terms = $ruoli_argomenti[$slug] ?? [];
                ?>
                <tr>
                    <td><?php echo esc_html($info['name']); ?></td>
                    <td>
                        <select name="caps[<?php echo esc_attr($slug); ?>][edit]">
                            <option value="own" <?php selected($has_edit_own); ?>>Solo i suoi post</option>
                            <option value="all" <?php selected($has_edit_all); ?>>Di qualsiasi utente</option>
                        </select>
                    </td>
                    <td>
                        <input type="checkbox" name="caps[<?php echo esc_attr($slug); ?>][publish]" value="1" <?php checked($has_publish); ?>>
                    </td>
                    <td>
                        <select name="caps[<?php echo esc_attr($slug); ?>][delete]">
                            <option value="own" <?php selected($has_delete_own); ?>>Solo i suoi post</option>
                            <option value="all" <?php selected($has_delete_all); ?>>Di qualsiasi utente</option>
                            <option value="disabled" <?php selected($has_delete_disabled); ?>>Disabilita</option>
                        </select>
                    </td>
                    <td>
                        <?php if (!empty($argomenti_terms)): ?>
                        <div class="argomenti-box" style="max-height: 150px; overflow:auto; border:1px solid #ddd; padding:5px;">
                            <?php foreach ($argomenti_terms as $term): ?>
                                <label style="display:block;margin-bottom:3px;">
                                    <input type="checkbox" name="argomenti[<?php echo esc_attr($slug); ?>][]" value="<?php echo $term->term_id; ?>"
                                        <?php checked(in_array($term->term_id, $selected_terms)); ?>>
                                    <?php echo esc_html($term->name); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                            <em>Nessun termine disponibile</em>
                        <?php endif; ?>
                    </td>
                    <td>
                        <input type="hidden" name="role_slug" value="<?php echo esc_attr($slug); ?>">
                        <button type="submit" name="elimina_ruolo" class="button button-secondary">Elimina</button>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <br>
            <input type="submit" name="salva_caps" class="button button-primary" value="Salva capabilities e argomenti">
        </form>
    </div>
<?php
}
?>