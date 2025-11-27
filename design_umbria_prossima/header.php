<?php
  $metabox = get_option('theme_metabox');
  $color = $metabox['color'] ?? "green"
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width">
    <?php wp_head(); ?>
  </head>
  <body class="theme-<?php echo $color; ?>">
    <?php get_template_part('template-parts/header');?>
    <?php get_template_part('template-parts/search-modal');?>
    <main id="content" role="main">