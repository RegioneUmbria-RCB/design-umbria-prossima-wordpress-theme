<?php
$theme_metabox    = get_option('theme_metabox');
$image_id         = $theme_metabox['logo_id'];
$site_name        = $theme_metabox['site_name'] ?? "";
$subtitle         = $theme_metabox['subtitle'] ?? "";
$social_facebook  = $theme_metabox['social_facebook'] ?? "";
$social_twitter   = $theme_metabox['social_twitter'] ?? "";
$social_instagram = $theme_metabox['social_instagram'] ?? "";
$social_youtube   = $theme_metabox['social_youtube'] ?? "";
$social_linkedin  = $theme_metabox['social_linkedin'] ?? "";

$footer_metabox   = get_option('footer_metabox');
$show_social      = $footer_metabox['show_social'] ?? "off";
$footer_columns   = $footer_metabox['footer_columns'] ?? [];
?>
<?php get_template_part("template-parts/valuta-servizio"); ?>
<footer class="it-footer" id="footer">
  <div class="it-footer-main">
    <div class="container pb-5">

      <!-- Logo e brand -->
      <div class="row">
        <div class="col-12 footer-items-wrapper logo-wrapper">
          <div class="it-brand-wrapper">
            <a href="<?php echo esc_url(home_url('/')); ?>">
              <?php echo wp_get_attachment_image(
                intval($image_id),
                'small',
                false,
                [
                  'class' => 'py-2 py-lg-0',
                  'alt'   => 'Logo del sito',
                  'style' => 'object-fit:contain; max-height:82px; width:fit-content;'
                ]
              ); ?>
              <div class="it-brand-text">
                <span class="h2 d-block"><?php echo $site_name; ?></span>
                <span><?php echo $subtitle; ?></span>
              </div>
            </a>
          </div>
        </div>
      </div>

      <!-- Sezioni principali -->
      <div class="row">
        <?php if (!empty($footer_columns) && is_array($footer_columns)) : ?>
          <?php foreach ($footer_columns as $index => $col) : ?>
            <?php
              $items     = json_decode($col['item_list'], true);
              $column_id = 'menu-' . sanitize_title($col['column_name']);
            ?>
            <div class="col-md-3 footer-items-wrapper">
              <h3 class="footer-heading-title">
                <?php echo esc_html($col['column_name']); ?>
              </h3>
              <div class="menu-<?php echo esc_attr(sanitize_title($col['column_name'])); ?>-container">
                <ul id="<?php echo esc_attr($column_id); ?>" class="footer-list">
                  <?php if (!empty($items) && is_array($items)) : ?>
                    <?php foreach ($items as $item) : ?>
                      <li>
                        <a href="<?php echo esc_url($item['link']); ?>">
                          <?php if (!empty($item['image']) && $item['image'] != 0) : ?>
                            <?php echo wp_get_attachment_image($item['image'], 'small', false, [
                              'style' => 'object-fit:contain; max-height:'.(!empty($item['label'])?'20px; margin-right:10px;':'82px; margin-top:5px;').' width:fit-content;'
                            ]); 
                            ?>
                          <?php endif; ?>
                          <?php echo esc_html($item['label']); ?>
                        </a>
                      </li>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </ul>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>

        <!-- Social -->
        <?php if (!empty($show_social) && $show_social === "on") : ?>
          <div class="col-md-3 footer-items-wrapper">
            <h3 class="footer-heading-title">Seguici su</h3>
            <ul class="list-inline text-start social">

              <?php if (!empty($social_facebook)) : ?>
                <li class="list-inline-item">
                  <a class="p-2 text-white" href="<?php echo $social_facebook; ?>" aria-label="Facebook" target="_blank">
                    <svg class="icon icon-sm icon-white align-top">
                      <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-facebook"></use>
                    </svg>
                    <span class="visually-hidden">facebook</span>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (!empty($social_twitter)) : ?>
                <li class="list-inline-item">
                  <a class="p-2 text-white" href="<?php echo $social_twitter; ?>" aria-label="Twitter" target="_blank">
                    <svg class="icon icon-sm icon-white align-top">
                      <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-twitter"></use>
                    </svg>
                    <span class="visually-hidden">twitter</span>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (!empty($social_instagram)) : ?>
                <li class="list-inline-item">
                  <a class="p-2 text-white" href="<?php echo $social_instagram; ?>" aria-label="Instagram" target="_blank">
                    <svg class="icon icon-sm icon-white align-top">
                      <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-instagram"></use>
                    </svg>
                    <span class="visually-hidden">instagram</span>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (!empty($social_youtube)) : ?>
                <li class="list-inline-item">
                  <a class="p-2 text-white" href="<?php echo $social_youtube; ?>" aria-label="Youtube" target="_blank">
                    <svg class="icon icon-sm icon-white align-top">
                      <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-youtube"></use>
                    </svg>
                    <span class="visually-hidden">youtube</span>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (!empty($social_linkedin)) : ?>
                <li class="list-inline-item">
                  <a class="p-2 text-white" href="<?php echo $social_linkedin; ?>" aria-label="Linkedin" target="_blank">
                    <svg class="icon icon-sm icon-white align-top">
                      <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-linkedin"></use>
                    </svg>
                    <span class="visually-hidden">linkedin</span>
                  </a>
                </li>
              <?php endif; ?>

            </ul>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</footer>

