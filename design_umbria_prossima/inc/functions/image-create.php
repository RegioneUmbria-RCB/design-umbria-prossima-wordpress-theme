<?php
    add_filter('intermediate_image_sizes_advanced', 'remove_default_image_sizes_except_original', 10, 1);

    function remove_default_image_sizes_except_original( $sizes ) {
        return [];
    }

    add_filter('wp_generate_attachment_metadata', 'generate_webp_in_separate_folder', 10, 2);

    function generate_webp_in_separate_folder($metadata, $attachment_id) {
        if (empty($metadata['file']) || !is_string($metadata['file'])) {
            return $metadata;
        }
        $upload_dir = wp_upload_dir();
        $base_dir = $upload_dir['basedir'];
        $original_file_path = $base_dir . '/' . $metadata['file'];
        if (!file_exists($original_file_path)) {
            return $metadata;
        }

        $webp_base_dir = $base_dir . '/webp/' . dirname($metadata['file']);
        if (!file_exists($webp_base_dir)) {
            wp_mkdir_p($webp_base_dir);
        }

        // WebP dell'immagine originale (full)
        $ext = strtolower(pathinfo($metadata['file'], PATHINFO_EXTENSION));
        if (in_array($ext, array('jpg', 'jpeg', 'png', 'gif'), true)) {
            $orig_webp_path = $webp_base_dir . '/' . pathinfo($metadata['file'], PATHINFO_FILENAME) . '.webp';
            convert_to_webp($original_file_path, $orig_webp_path);
        }

        $additional_sizes = wp_get_additional_image_sizes();
        foreach ($additional_sizes as $size_name => $size_attrs) {
            $w = isset($size_attrs['width']) ? (int) $size_attrs['width'] : 0;
            $h = isset($size_attrs['height']) ? (int) $size_attrs['height'] : 0;
            if ($w <= 0 || $h <= 0) {
                continue;
            }
            $crop = !empty($size_attrs['crop']);
            $resized_image = image_make_intermediate_size($original_file_path, $w, $h, $crop);

            if ($resized_image && !is_wp_error($resized_image)) {
                $resized_image_path = $base_dir . '/' . dirname($metadata['file']) . '/' . $resized_image['file'];

                $webp_path = $webp_base_dir . '/' . pathinfo($resized_image['file'], PATHINFO_FILENAME) . '.webp';

                convert_to_webp($resized_image_path, $webp_path);

                unlink($resized_image_path);

                $metadata['sizes'][$size_name] = array(
                    'file' => pathinfo($resized_image['file'], PATHINFO_FILENAME) . '.webp',
                    'width' => isset($resized_image['width']) ? (int) $resized_image['width'] : $w,
                    'height' => isset($resized_image['height']) ? (int) $resized_image['height'] : $h,
                );
            }
        }

        return $metadata;
    }

    function convert_to_webp($image_path, $webp_path) {
        $image_info = getimagesize($image_path);
        $mime_type = $image_info['mime'];

        switch ($mime_type) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($image_path);
                break;
            case 'image/png':
                $image = imagecreatefrompng($image_path);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($image_path);
                break;
            default:
                return;
        }

        imagewebp($image, $webp_path, 100);
        imagedestroy($image);
    }
?>
