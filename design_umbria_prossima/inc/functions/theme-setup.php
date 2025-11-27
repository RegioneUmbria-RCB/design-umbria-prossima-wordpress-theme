<?php
add_action('admin_menu', 'menu_setup');

function menu_setup() {
    add_menu_page(
        'Impostazioni Template',
        'Impostazioni Template',
        'manage_options',
        'impostazioni-template',
        'render_page',
        'dashicons-admin-generic',
        1
    );
}

function render_page() {
    $tab = isset($_GET['tab']) ? $_GET['tab'] : 'theme';
    ?>
    <div class="wrap">
        <h1>Impostazioni Template</h1>
        <h2 class="nav-tab-wrapper">
            <a href="?page=impostazioni-template&tab=theme" class="nav-tab <?php echo $tab === 'theme' ? 'nav-tab-active' : ''; ?>">Globali</a>
            <a href="?page=impostazioni-template&tab=header" class="nav-tab <?php echo $tab === 'header' ? 'nav-tab-active' : ''; ?>">Header</a>
            <a href="?page=impostazioni-template&tab=footer" class="nav-tab <?php echo $tab === 'footer' ? 'nav-tab-active' : ''; ?>">Footer</a>
            <a href="?page=impostazioni-template&tab=permissions" class="nav-tab <?php echo $tab === 'permissions' ? 'nav-tab-active' : ''; ?>">Permessi</a>
            <a href="?page=impostazioni-template&tab=homepage" class="nav-tab <?php echo $tab === 'homepage' ? 'nav-tab-active' : ''; ?>">Homepage</a>
            <a href="?page=impostazioni-template&tab=single" class="nav-tab <?php echo $tab === 'single' ? 'nav-tab-active' : ''; ?>">Single Page</a>
            <a href="?page=impostazioni-template&tab=taxonomy" class="nav-tab <?php echo $tab === 'taxonomy' ? 'nav-tab-active' : ''; ?>">Taxonomy Page</a>
            <a href="?page=impostazioni-template&tab=category" class="nav-tab <?php echo $tab === 'category' ? 'nav-tab-active' : ''; ?>">Category Page</a>
        </h2>

        <div class="tab-content" style="padding:1rem;">
            <?php
                if ( function_exists( 'cmb2_get_metabox_form' ) ) {
                    switch ($tab){
                        case "permissions":
                            echo cmb2_get_metabox_form('permissions_metabox','permissions_metabox');
                            break;
                        case "theme":
                            echo cmb2_get_metabox_form('theme_metabox','theme_metabox');
                            break;
                        case "header":
                            echo cmb2_get_metabox_form('header_metabox','header_metabox');
                            break;
                        case "footer":
                            echo cmb2_get_metabox_form('footer_metabox','footer_metabox');
                            break;
                        case "permissions":
                            echo cmb2_get_metabox_form('permissions_metabox','permissions_metabox');
                            break;
                        case "homepage":
                            echo cmb2_get_metabox_form('homepage_metabox','homepage_metabox');
                            break;
                        case "single":
                            echo cmb2_get_metabox_form('single_metabox','single_metabox');
                            break;
                        case "taxonomy":
                            echo cmb2_get_metabox_form('taxonomy_metabox','taxonomy_metabox');
                            break;
                        case "category":
                            echo cmb2_get_metabox_form('category_metabox','category_metabox');
                            break;
                    }
                } 
            ?>
        </div>
    </div>
    <?php
}

add_action('admin_menu', 'rimuovi_pagina_opzioni_category_metabox', 999);

function rimuovi_pagina_opzioni_category_metabox() {
    remove_menu_page('theme_metabox');
    remove_menu_page('header_metabox');
    remove_menu_page('footer_metabox');
    remove_menu_page('permissions_metabox');
    remove_menu_page('homepage_metabox');
    remove_menu_page('single_metabox');
    remove_menu_page('taxonomy_metabox');
    remove_menu_page('category_metabox');
}