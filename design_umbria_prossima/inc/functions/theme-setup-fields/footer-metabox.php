<?php
add_action('admin_enqueue_scripts', 'footer_metabox_enqueue');

function footer_metabox_enqueue($hook) {
    if ($hook !== 'toplevel_page_impostazioni-template') {
        return;
    }

    wp_enqueue_media();
    wp_enqueue_script('jquery-ui-sortable');
}

add_action('cmb2_admin_init', 'footer_metabox');

function footer_metabox() {
    $cmb = new_cmb2_box(array(
        'id'            => 'footer_metabox',
        'title'         => __('Impostazioni Footer', 'tuo-text-domain'),
        'object_types'  => array('options-page'),
        'option_key'    => 'footer_metabox',
        'context'       => 'normal',
        'priority'      => 'high',
    ));

    $cmb->add_field( array(
        'name'    => __('Mostra canali social', 'tuo-text-domain'),
        'desc'    => __('Attiva per mostrare i canali social nel footer.', 'tuo-text-domain'),
        'id'      => 'show_social',
        'type'    => 'checkbox',
    ) );

    $group_field_id = $cmb->add_field(array(
        'id'          => 'footer_columns',
        'type'        => 'group',
        'description' => __('Aggiungi e riorganizza le colonne del footer.', 'tuo-text-domain'),
        'options'     => array(
            'group_title'   => __('Colonna {#}', 'tuo-text-domain'),
            'add_button'    => __('Aggiungi Colonna', 'tuo-text-domain'),
            'remove_button' => __('Rimuovi Colonna', 'tuo-text-domain'),
            'sortable'      => true,
        ),
    ));

   $cmb->add_group_field($group_field_id, array(
        'name'       => __('Nome della colonna', 'tuo-text-domain'),
        'id'         => 'column_name',
        'type'       => 'text',
    ));

    $cmb->add_group_field($group_field_id, array(
        'name' => __('Elenco contenuti', 'tuo-text-domain'),
        'id'   => 'item_list',
        'type' => 'textarea',
        'attributes' => array(
            'class' => 'cmb2-textarea-hidden'
        ),
    ));

    add_action('admin_footer', function() {
        ?>
        <script>
        jQuery(document).ready(function($) {
            function updateTextarea($wrapper, $textarea) {
                var data = [];
                $wrapper.find('.js-item').each(function() {
                    var $el = $(this);
                    data.push({
                        label: $el.find('.js-label').val(),
                        link:  $el.find('.js-link').val(),
                        image: parseInt($el.find('.js-image').val()) || 0
                    });
                });
                $textarea.val(JSON.stringify(data));
            }

            function addItem($wrapper, item) {
                var $item = $(`
                    <div class="js-item" style="margin-bottom:10px;padding:10px;border:1px solid #ddd;display:flex;gap:1rem;align-items:center;">
                        <span class="dashicons dashicons-move js-move" style="cursor:move;"></span>
                        <input type="text" placeholder="Label" class="js-label" value="${item.label}">
                        <input type="text" placeholder="Link" class="js-link" value="${item.link}">
                        <div class="js-image-wrapper" style="display:flex; gap:10px; align-items:center;">
                            <button type="button" class="button js-upload-image">Seleziona immagine</button>
                            <button type="button" class="button js-remove-image" style="display:none;">Rimuovi immagine</button>
                            <span class="js-image-preview"></span>
                            <input type="hidden" class="js-image" value="${item.image}">
                        </div>
                        <button type="button" class="button js-remove-item" style="margin-left:auto;">Rimuovi</button>
                    </div>
                `);
                $wrapper.append($item);

                if (item.image) {
                    var attachment = wp.media.attachment(item.image);

                    attachment.fetch().done(function() {
                        var data = attachment.toJSON();
                        if (data.url) {
                            $item.find('.js-image-preview').html('<img src="' + data.url + '" style="max-width:80px;">');
                            $item.find('.js-remove-image').show();
                        }
                    });
                }

                $item.on('click', '.js-remove-item', function() {
                    $item.remove();
                    updateTextarea($wrapper, $wrapper.closest('.cmb-repeatable-grouping').find('textarea[id$="item_list"]'));
                });

                $item.on('click', '.js-remove-image', function() {
                    $item.find('.js-image').val('');
                    $item.find('.js-image-preview').html('');
                    $(this).hide();
                    updateTextarea($wrapper, $wrapper.closest('.cmb-repeatable-grouping').find('textarea[id$="item_list"]'));
                });

                $item.on('click', '.js-upload-image', function(e) {
                    e.preventDefault();
                    var file_frame;
                    if (file_frame) {
                        file_frame.open();
                        return;
                    }
                    file_frame = wp.media.frames.file_frame = wp.media({
                        title: 'Seleziona immagine',
                        button: { text: 'Usa questa immagine' },
                        multiple: false
                    });
                    file_frame.on('select', function() {
                        var attachment = file_frame.state().get('selection').first().toJSON();
                        $item.find('.js-image').val(attachment.id);
                        $item.find('.js-image-preview').html('<img src="' + attachment.url + '" style="max-width:80px;">');
                        $item.find('.js-remove-image').show();
                        updateTextarea($wrapper, $wrapper.closest('.cmb-repeatable-grouping').find('textarea[id$="item_list"]'));
                    });
                    file_frame.open();
                });

                $item.find('input').on('change keyup', function() {
                    updateTextarea($wrapper, $wrapper.closest('.cmb-repeatable-grouping').find('textarea[id$="item_list"]'));
                });
            }

            function initColumns() {
                $('.cmb-repeatable-grouping').each(function() {
                    var $column = $(this);

                    if ($column.find('.js-items-wrapper').length === 0) {
                        var $wrapperHtml = '<div class="js-items-wrapper"></div><button type="button" class="button js-add-item">Aggiungi elemento</button>';
                        $column.find('.cmb2-textarea-hidden').after($wrapperHtml);
                    }

                    var $wrapper = $column.find('.js-items-wrapper');
                    var $textarea = $column.find('textarea[id$="item_list"]');

                    var itemsData = [];
                    try { itemsData = JSON.parse($textarea.val()); } catch(e) {}
                    
                    $wrapper.empty();
                    itemsData.forEach(function(item) { addItem($wrapper, item); });

                    $column.off('click', '.js-add-item').on('click', '.js-add-item', function() {
                        addItem($wrapper, { label: '', link: '', image: 0 });
                        updateTextarea($wrapper, $textarea);
                    });

                    $wrapper.sortable({
                        handle: '.js-move',
                        update: function() { updateTextarea($wrapper, $textarea); }
                    });
                });
            }

            initColumns();

            $(document).on('click', '.cmb-add-group-row', function() {
                setTimeout(initColumns, 100);
            });
        });
        </script>
        <style>
            .cmb2-textarea-hidden {
                display: none !important;
            }
        </style>
        <?php
    });
}