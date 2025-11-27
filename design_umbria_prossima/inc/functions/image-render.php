<?php

function modify_image_src($attr, $attachment, $size) {
    // Controlla che l'attr e l'attachment siano validi
    if (isset($attr['src']) && is_string($attr['src'])) {
        $new_src = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $attr['src']);
        $new_src = str_replace('/uploads/', '/uploads/webp/', $new_src);
        $axes = get_post_meta($attachment->ID, 'focal_point', true);

        // Genera il srcset per WebP
        $webp_srcset = generate_webp_srcset($attachment->ID);

	if ($webp_srcset) {
            $attr['srcset'] = $webp_srcset;
        }
        
        // Controlla se il file WebP esiste prima di cambiarlo
        if (file_exists(str_replace(home_url('/'), ABSPATH, $new_src))) {
            $attr['src'] = $new_src;
        }

        if(str_contains($attr['style'], "max-height") || str_contains($attr['style'], "max-width")){
            $attr['style'] = $attr['style'].'object-fit:contain; width:auto;';
        }
        else{
            $attr['style'] = $attr['style'].'object-fit:cover;';
        }
       

        // Imposta il punto focale, se esistono dati
        if (isset($axes) && is_array($axes)) {
            $attr['style'] = $attr['style'].'object-position:' . $axes['x'] . '% ' . $axes['y'] . '%;';
        }
    }

    return $attr;
}

add_filter('wp_get_attachment_image_attributes', 'modify_image_src', 10, 3);


function generate_webp_srcset($attachment_id) {
    // Recupera le dimensioni aggiuntive definite nel tema o nei plugin
    $sizes = wp_get_additional_image_sizes();

    // Aggiungi dimensioni standard di WordPress
    $sizes['thumbnail'] = array('width' => get_option('thumbnail_size_w'), 'height' => get_option('thumbnail_size_h'));
    $sizes['medium'] = array('width' => get_option('medium_size_w'), 'height' => get_option('medium_size_h'));
    $sizes['large'] = array('width' => get_option('large_size_w'), 'height' => get_option('large_size_h'));

    $upload_dir = wp_upload_dir();

    // Ottieni i metadati dell'immagine
    $image_meta = wp_get_attachment_metadata($attachment_id);
    if (!$image_meta || !isset($image_meta['file'])) {
        return ''; // Se non ci sono metadati, ritorna una stringa vuota
    }

    $base_dir = $upload_dir['basedir'] . '/';
    $base_url = $upload_dir['baseurl'] . '/';
    $file_path = $image_meta['file'];

    $path_parts = explode('/', $file_path);
    $year = $path_parts[0];
    $month = $path_parts[1];

    $srcset = [];

    // Crea il srcset per ogni dimensione disponibile
    foreach ($sizes as $size_name => $size_info) {
        if (isset($image_meta['sizes'][$size_name])) {
            $resized_file = $image_meta['sizes'][$size_name]['file'];

            // Crea il nome del file WebP
            $webp_file = preg_replace('/\.(jpg|jpeg|png|gif)$/i', '.webp', $resized_file);
            $webp_path = $base_dir . 'webp/' . $year . '/' . $month . '/' . $webp_file;

            // Verifica se il file WebP esiste
            if (file_exists($webp_path)) {
                $webp_url = $base_url . 'webp/' . $year . '/' . $month . '/' . $webp_file;
                $srcset[] = $webp_url . ' ' . $size_info['width'] . 'w';
            }
        }
    }

    // Ritorna il srcset generato se disponibile
    if (!empty($srcset)) {
        return implode(', ', $srcset);
    }

    return ''; // Ritorna una stringa vuota se non ci sono srcset disponibili
}

?>

