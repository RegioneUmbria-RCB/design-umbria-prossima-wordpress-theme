<?php
global $post, $inline, $hide_arguments;
$argomenti = get_the_terms($post, 'argomenti');
$post_url = get_permalink();

if ($hide_arguments) $argomenti = array();
?>

<div class="dropdown <?php echo $inline ? 'd-inline' : '' ?>">
    <button
        class="btn btn-dropdown dropdown-toggle text-decoration-underline d-inline-flex align-items-center fs-0 text-primary"
        type="button"
        id="shareActions"
        data-bs-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false"
        aria-label="condividi sui social"
    >
        <svg class="icon" aria-hidden="true">
            <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-share"></use>
        </svg>
        <small>Condividi</small>
    </button>
    <div class="dropdown-menu shadow-lg" aria-labelledby="shareActions">
        <div class="link-list-wrapper">
            <ul class="link-list" role="menu">
                <li role="none">
                <a class="list-item px-2" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $post_url; ?>" target="_blank" role="menuitem">
                <svg class="icon" aria-hidden="true">
                    <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-facebook"></use>
                    </svg>
                    <span class="text-primary">Facebook</span></a
                >
                </li>
                <li role="none">
                <a class="list-item px-2" href="https://twitter.com/intent/tweet?text=<?php echo $post_url; ?>" target="_blank" role="menuitem">
                <svg class="icon" aria-hidden="true">
                    <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-twitter"></use>
                    </svg>
                    <span class="text-primary">Twitter</span></a
                >
                </li>
                <li role="none">
                <a class="list-item px-2" href="https://www.linkedin.com/shareArticle?url=<?php echo $post_url; ?>" target="_blank" role="menuitem">
                <svg class="icon" aria-hidden="true">
                    <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-linkedin"></use>
                    </svg>
                    <span class="text-primary">Linkedin</span></a
                >
                </li>
                <li role="none">
                <a class="list-item px-2" href="https://api.whatsapp.com/send?text=<?php echo $post_url; ?>" target="_blank" role="menuitem">
                <svg class="icon" aria-hidden="true">
                    <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-whatsapp"></use>
                    </svg>
                    <span class="text-primary">Whatsapp</span></a
                >
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="dropdown <?php echo $inline ? 'd-inline' : '' ?>">
    <button
        class="btn btn-dropdown dropdown-toggle text-decoration-underline d-inline-flex align-items-center fs-0 text-primary"
        type="button"
        id="viewActions"
        data-bs-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false"
    >
        <svg class="icon" aria-hidden="true">
        <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-more-items"
        ></use>
        </svg>
        <small>Vedi azioni</small>
    </button>
    <div class="dropdown-menu shadow-lg" aria-labelledby="viewActions">
        <div class="link-list-wrapper">
            <ul class="link-list" role="menu">
                <li role="none">
                <a class="list-item px-2" href="#" onclick="window.print()" role="menuitem">
                    <svg class="icon" aria-hidden="true">
                    <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-print"></use>
                    </svg>
                    <span class="text-primary">Stampa</span></a
                >
                </li>
                <li role="none">
                <a class="list-item px-2" href="#" role="menuitem" onclick="window.listenElements(this, '[data-audio]')"><svg class="icon" aria-hidden="true">
                    <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-hearing"></use>
                    </svg>
                    <span class="text-primary">Ascolta</span></a
                >
                </li>
                <li role="none">
                <a class="list-item px-2" href="mailto:?subject=<?php echo the_title(); ?>&body=<?php echo get_permalink(); ?>" role="menuitem"
                    ><svg class="icon" aria-hidden="true">
                    <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-mail"></use>
                    </svg>
                    <span class="text-primary">Invia</span></a
                >
                </li>
            </ul>
        </div>
    </div>
</div>
<?php if (is_array($argomenti) && count($argomenti) ) { ?>
<div class="mt-4 mb-4">
    <span class="subtitle-small">Argomenti</span>
    <ul class="d-flex flex-wrap gap-1">
        <?php foreach ($argomenti as $argomento) { ?>
        <li>
            <a class="chip chip-simple" href="<?php echo get_term_link($argomento->term_id); ?>" data-element="service-topic">
                <span class="chip-label"><?php echo $argomento->name; ?></span>
            </a>
        </li>
        <?php } ?>
    </ul>
</div>
<?php } ?>