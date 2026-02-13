<?php
/**
 * Importa strutture dati dallo zip design-umbria-prossima (sempre scaricato da GitHub).
 * Lo zip design-umbria-prossima-wordpress-theme-main ha struttura:
 *   design-umbria-prossima-wordpress-theme-main/
 *   └── design_umbria_prossima/
 *       └── inc/origin-tema-comuni/
 *           ├── tipologie/
 *           ├── tassonomie/
 *           ├── options/
 *           ├── comuni_tipologie.json
 *           └── comuni_pagine.json
 */
define('ORIGIN_ZIP_ROOT', 'design-umbria-prossima-wordpress-theme-main/design_umbria_prossima');
define('ORIGIN_ZIP_URL', 'https://github.com/RegioneUmbria-RCB/design-umbria-prossima-wordpress-theme/archive/refs/heads/main.zip');

// 1. Aggiungi il sottomenu in Strumenti
add_action('admin_menu', function () {
    add_submenu_page(
        'tools.php',
        'Aggiorna strutture dati',
        'Aggiorna strutture dati',
        'manage_options',
        'importa-sezioni-tema',
        'pagina_import_sezioni'
    );
});

// 2. Pagina admin con pulsante e progress bar
function pagina_import_sezioni() {
    ?>
    <div class="wrap">
        <h1>Importa strutture dati dal tema design-umbria-prossima</h1>
        <p>Premi il pulsante per aggiornare: <strong>Tipologie</strong>, <strong>Tassonomie</strong> e <strong>Options</strong>.</p>
        
        <button id="importa-sezioni" class="button button-primary">Aggiorna strutture dati</button>

        <div id="barra-progresso" style="margin-top: 20px; width: 100%; background: #eee; border-radius: 4px; overflow: hidden; height: 20px;">
            <div id="progresso" style="width: 0; height: 100%; background: #0073aa; transition: width 0.3s;"></div>
        </div>

        <div id="risultato-import" style="margin-top: 20px;"></div>
    </div>

    <script type="text/javascript">
        document.getElementById('importa-sezioni').addEventListener('click', function () {
            const button = this;
            const progresso = document.getElementById('progresso');
            const risultato = document.getElementById('risultato-import');
            button.disabled = true;
            progresso.style.width = '0%';
            risultato.innerHTML = 'Importazione in corso...';

            fetch(ajaxurl + '?action=importa_tutte_le_sezioni')
                .then(response => response.json())
                .then(data => {
                    progresso.style.width = '100%';
                    button.disabled = false;

                    if (data.success) {
                        risultato.innerHTML = '<div class="notice notice-success"><p>' + data.data + '</p></div>';
                    } else {
                        risultato.innerHTML = '<div class="notice notice-error"><p>Aggiornamento fallito:<br>' + data.data + '</p></div>';
                    }
                })
                .catch(error => {
                    progresso.style.width = '100%';
                    risultato.innerHTML = '<div class="notice notice-error"><p>Errore AJAX: ' + error + '</p></div>';
                    button.disabled = false;
                });
        });
    </script>
    <?php
}

// 3. Gestore AJAX
add_action('wp_ajax_importa_tutte_le_sezioni', 'importa_strutture_dati_tema');

// 4. Funzione principale di importazione (riutilizzata anche all'attivazione del tema)
function importa_strutture_dati_tema() {
    $errors = [];
    if (!function_exists('download_url')) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }
    $zip_path = download_url(ORIGIN_ZIP_URL);
    if (is_wp_error($zip_path)) {
        if (defined('DOING_AJAX') && DOING_AJAX) {
            wp_send_json_error('Download fallito: ' . $zip_path->get_error_message());
        }
        return;
    }

    $result1 = importa_cartella_da_zip($zip_path, '/inc/origin-tema-comuni/tipologie', '/inc/origin-tema-comuni/tipologie');
    if ($result1 !== true) $errors[] = "Tipologie: $result1";

    $result2 = importa_cartella_da_zip($zip_path, '/inc/origin-tema-comuni/tassonomie', '/inc/origin-tema-comuni/tassonomie');
    if ($result2 !== true) $errors[] = "Tassonomie: $result2";

    $result3 = importa_cartella_da_zip($zip_path, '/inc/origin-tema-comuni/options', '/inc/origin-tema-comuni/options');
    if ($result3 !== true) $errors[] = "Options: $result3";

    $result4 = importa_file_da_zip($zip_path, '/inc/origin-tema-comuni/comuni_tipologie.json', '/inc/origin-tema-comuni/comuni_tipologie.json');
    if ($result4 !== true) $errors[] = "comuni_tipologie.json: $result4";

    $result5 = importa_file_da_zip($zip_path, '/inc/origin-tema-comuni/comuni_pagine.json', '/inc/origin-tema-comuni/comuni_pagine.json');
    if ($result5 !== true) $errors[] = "comuni_pagine.json: $result5";

    if (file_exists($zip_path)) {
        unlink($zip_path);
    }

    if (defined('DOING_AJAX') && DOING_AJAX) {
        if (empty($errors)) {
            wp_send_json_success('Le strutture dati sono state correttamente aggiornate.');
        } else {
            wp_send_json_error(implode('<br>', $errors));
        }
    } else {
        if (!empty($errors)) {
            error_log('[Tema] Errore importazione iniziale: ' . implode(' | ', $errors));
        } else {
            error_log('[Tema] Strutture dati importate correttamente all\'attivazione.');
        }
    }
}

// 5. Funzioni di supporto: importazione cartelle e file dallo zip design-umbria-prossima
function importa_cartella_da_zip($zip_path, $path_in_zip, $local_target) {
    $tmp_dir = wp_tempnam();
    unlink($tmp_dir);
    mkdir($tmp_dir);

    $zip = new ZipArchive;
    if ($zip->open($zip_path) !== TRUE) {
        return 'Impossibile aprire lo zip';
    }

    $zip->extractTo($tmp_dir);
    $zip->close();

    $extracted_path = $tmp_dir . '/' . ORIGIN_ZIP_ROOT . $path_in_zip;
    $destination = get_template_directory() . $local_target;

    if (!is_dir($extracted_path)) {
        return 'Cartella non trovata: ' . $path_in_zip;
    }

    if (!file_exists($destination)) {
        mkdir($destination, 0755, true);
    }

    copia_cartella($extracted_path, $destination);
    return true;
}

function importa_file_da_zip($zip_path, $file_path_in_zip, $local_target) {
    $tmp_dir = wp_tempnam();
    unlink($tmp_dir);
    mkdir($tmp_dir);

    $zip = new ZipArchive;
    if ($zip->open($zip_path) !== TRUE) {
        return 'Impossibile aprire lo zip';
    }

    $zip->extractTo($tmp_dir);
    $zip->close();

    $extracted_file_path = $tmp_dir . '/' . ORIGIN_ZIP_ROOT . $file_path_in_zip;
    $destination = get_template_directory() . $local_target;

    if (!file_exists($extracted_file_path)) {
        return 'File non trovato: ' . $file_path_in_zip;
    }

    $destination_dir = dirname($destination);
    if (!file_exists($destination_dir)) {
        mkdir($destination_dir, 0755, true);
    }

    if (!copy($extracted_file_path, $destination)) {
        return 'Copia del file fallita.';
    }

    return true;
}

function copia_cartella($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while(false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            $src_path = $src . '/' . $file;
            $dst_path = $dst . '/' . $file;
            if (is_dir($src_path)) {
                copia_cartella($src_path, $dst_path);
            } else {
                copy($src_path, $dst_path);
            }
        }
    }
    closedir($dir);
}

?>
