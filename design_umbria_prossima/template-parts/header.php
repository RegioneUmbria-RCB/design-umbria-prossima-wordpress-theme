<?php
$theme_metabox     = get_option('theme_metabox');
$image_id          = $theme_metabox['logo_id'];
$site_name         = $theme_metabox['site_name'] ?? "";
$subtitle          = $theme_metabox['subtitle'] ?? "";
$social_facebook   = $theme_metabox['social_facebook'] ?? "";
$social_twitter    = $theme_metabox['social_twitter'] ?? "";
$social_instagram  = $theme_metabox['social_instagram'] ?? "";
$social_youtube    = $theme_metabox['social_youtube'] ?? "";
$social_linkedin   = $theme_metabox['social_linkedin'] ?? "";
$alert_active      = $theme_metabox['alert_enable'] ?? null;
$alert_type        = $theme_metabox['alert_type'] ?? null;
$alert_message     = $theme_metabox['alert_message'] ?? "";

$header_metabox    = get_option('header_metabox');
$show_social       = $header_metabox['show_social'] ?? "off";
$show_ente         = $header_metabox['show_ente'] ?? "off";
$ente              = $header_metabox['ente'] ?? "";
$link_ente         = $header_metabox['link_ente'] ?? "#";
$primary_menu      = $header_metabox['primary_menu'] ?? [];
$secondary_menu    = $header_metabox['secondary_menu'] ?? [];
?>
<?php get_template_part('template-parts/alert-component', null, array('active'=>$alert_active, 'type'=>$alert_type, 'message'=>$alert_message)); ?>
<header class="it-header-wrapper shadow-sm" data-bs-target="#header-nav-wrapper">
  <div class="it-header-slim-wrapper">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <div class="it-header-slim-wrapper-content">

            <?php if (!empty($show_ente) && $show_ente === "on"): ?>
              <a class="d-block navbar-brand fw-bold" href="<?php echo $link_ente; ?>">
                <?php echo $ente; ?>
              </a>
            <?php endif; ?>

            <div class="it-header-slim-right-zone" role="navigation">
              <a class="btn btn-primary btn-icon btn-full" href="#" data-element="personal-area-login">
                <span class="rounded-icon" aria-hidden="true">
                  <svg class="icon icon-primary">
                    <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-user"></use>
                  </svg>
                </span>
                <span class="d-none d-lg-block">Accedi all'area personale</span>
              </a>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="it-nav-wrapper">
    <div class="it-header-center-wrapper">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="it-header-center-content-wrapper">
              <div class="it-brand-wrapper">
                <a href="<?php echo esc_url(home_url('/')); ?>">
                  <?php echo wp_get_attachment_image(
                    intval($image_id),
                    'small',
                    false,
                    [
                      'alt'   => 'Logo del sito',
                      'class' => 'py-2 py-lg-0',
                      'style' => 'object-fit:contain; max-height:82px; width:fit-content;'
                    ]
                  ); ?>
                  <div class="it-brand-text">
                    <div class="it-brand-title"><?php echo $site_name; ?></div>
                    <div class="it-brand-tagline d-none d-md-block"><?php echo $subtitle; ?></div>
                  </div>
                </a>
              </div>

              <div class="it-right-zone">
                <?php if (!empty($show_social) && $show_social === "on"): ?>
                  <div class="it-socials d-none d-md-flex">
                    <span>Seguici su</span>
                    <ul>
                      <?php if (!empty($social_facebook)): ?>
                        <li>
                          <a href="<?php echo $social_facebook; ?>" aria-label="Facebook" target="_blank">
                            <svg class="icon">
                              <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-facebook"></use>
                            </svg>
                          </a>
                        </li>
                      <?php endif; ?>

                      <?php if (!empty($social_twitter)): ?>
                        <li>
                          <a href="<?php echo $social_twitter; ?>" aria-label="Twitter" target="_blank">
                            <svg class="icon">
                              <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-twitter"></use>
                            </svg>
                          </a>
                        </li>
                      <?php endif; ?>

                      <?php if (!empty($social_instagram)): ?>
                        <li>
                          <a href="<?php echo $social_instagram; ?>" aria-label="Instagram" target="_blank">
                            <svg class="icon">
                              <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-instagram"></use>
                            </svg>
                          </a>
                        </li>
                      <?php endif; ?>

                      <?php if (!empty($social_youtube)): ?>
                        <li>
                          <a href="<?php echo $social_youtube; ?>" aria-label="Youtube" target="_blank">
                            <svg class="icon">
                              <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-youtube"></use>
                            </svg>
                          </a>
                        </li>
                      <?php endif; ?>

                      <?php if (!empty($social_linkedin)): ?>
                        <li>
                          <a href="<?php echo $social_linkedin; ?>" aria-label="Linkedin" target="_blank">
                            <svg class="icon">
                              <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-linkedin"></use>
                            </svg>
                          </a>
                        </li>
                      <?php endif; ?>
                    </ul>
                  </div>
                <?php endif; ?>

                <div class="it-search-wrapper">
                  <span class="d-none d-md-block">Cerca</span>
                  <button class="search-link rounded-icon" type="button" data-bs-toggle="modal" data-bs-target="#search-modal" aria-label="Cerca nel sito">
                    <svg class="icon icon-primary">
                      <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-search"></use>
                    </svg>
                  </button>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Navbar -->
    <div class="it-header-navbar-wrapper" id="header-nav-wrapper">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="navbar navbar-expand-lg has-megamenu">

              <button class="custom-navbar-toggler" type="button" aria-controls="nav4" aria-expanded="false"
                aria-label="Mostra/Nascondi la navigazione" data-bs-target="#nav4"
                data-bs-toggle="navbarcollapsible" data-focus-mouse="false">
                <svg class="icon">
                  <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-burger"></use>
                </svg>
              </button>

              <div class="navbar-collapsable" id="nav4" style="display: none;" aria-hidden="true">
                <div class="overlay fade" style="display: none;"></div>
                <div class="close-div">
                  <button class="btn close-menu" type="button" data-focus-mouse="false">
                    <span class="visually-hidden">Nascondi la navigazione</span>
                    <svg class="icon">
                      <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-close-big"></use>
                    </svg>
                  </button>
                </div>

                <div class="menu-wrapper">
                  <!-- Menu principale -->
                  <nav aria-label="Principale">
                    <ul class="navbar-nav" id="menu-main-menu" data-element="main-navigation">
                      <?php foreach ($primary_menu as $item): ?>
                        <?php if (!empty($item['link']) && !empty($item['label'])): ?>
                          <li class="nav-item">
                            <a class="nav-link" href="<?php echo $item['link']; ?>">
                              <?php if (!empty($item['icon_id'])) {
                                echo wp_get_attachment_image(
                                  intval($item['icon_id']),
                                  'small',
                                  false,
                                  [
                                    'class' => 'me-2',
                                    'style' => 'object-fit:contain; height:20px; width:fit-content;'
                                  ]
                                );
                              } ?>
                              <span><?php echo $item['label']; ?></span>
                            </a>
                          </li>
                        <?php endif; ?>
                      <?php endforeach; ?>
                    </ul>
                  </nav>

                  <!-- Menu secondario -->
                  <nav aria-label="Secondaria">
                    <ul id="menu-argomenti" class="navbar-nav navbar-secondary">
                      <?php foreach ($secondary_menu as $item): ?>
                        <?php if (!empty($item['link']) && !empty($item['label'])): ?>
                          <li class="nav-item">
                            <a class="nav-link" href="<?php echo $item['link']; ?>">
                              <?php if (!empty($item['icon_id'])) {
                                echo wp_get_attachment_image(
                                  intval($item['icon_id']),
                                  'small',
                                  false,
                                  [
                                    'class' => 'me-2',
                                    'style' => 'object-fit:contain; height:20px; width:fit-content;'
                                  ]
                                );
                              } ?>
                              <span class="fw-bold"><?php echo $item['label']; ?></span>
                            </a>
                          </li>
                        <?php endif; ?>
                      <?php endforeach; ?>
                    </ul>
                  </nav>

                  <!-- Social nella mobile navbar -->
                  <?php if (!empty($show_social) && $show_social === "on"): ?>
                    <div class="it-socials">
                      <span>Seguici su</span>
                      <ul>
                        <?php if (!empty($social_facebook)): ?>
                          <li>
                            <a href="<?php echo $social_facebook; ?>" aria-label="Facebook" target="_blank">
                              <svg class="icon">
                                <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-facebook"></use>
                              </svg>
                              <span class="visually-hidden">facebook</span>
                            </a>
                          </li>
                        <?php endif; ?>

                        <?php if (!empty($social_twitter)): ?>
                          <li>
                            <a href="<?php echo $social_twitter; ?>" aria-label="Twitter" target="_blank">
                              <svg class="icon">
                                <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-twitter"></use>
                              </svg>
                              <span class="visually-hidden">twitter</span>
                            </a>
                          </li>
                        <?php endif; ?>

                        <?php if (!empty($social_instagram)): ?>
                          <li>
                            <a href="<?php echo $social_instagram; ?>" aria-label="Instagram" target="_blank">
                              <svg class="icon">
                                <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-instagram"></use>
                              </svg>
                              <span class="visually-hidden">instagram</span>
                            </a>
                          </li>
                        <?php endif; ?>

                        <?php if (!empty($social_youtube)): ?>
                          <li>
                            <a href="<?php echo $social_youtube; ?>" aria-label="Youtube" target="_blank">
                              <svg class="icon">
                                <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-youtube"></use>
                              </svg>
                              <span class="visually-hidden">youtube</span>
                            </a>
                          </li>
                        <?php endif; ?>

                        <?php if (!empty($social_linkedin)): ?>
                          <li>
                            <a href="<?php echo $social_linkedin; ?>" aria-label="Linkedin" target="_blank">
                              <svg class="icon">
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
          </div>
        </div>
      </div>
    </div>
  </div>
</header>
