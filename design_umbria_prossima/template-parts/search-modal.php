<div class="modal fade search-modal" id="search-modal" tabindex="-1" style="display: none;" data-focus-mouse="false" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content perfect-scrollbar">
      <div class="modal-body">
        <form role="search" id="search-form-modal" method="get" class="search-form" action="/search/">
          <div class="container">
            <div class="row variable-gutters">
              <div class="col">
                <div class="modal-title">
                  <button class="search-link d-md-none" type="button" data-bs-toggle="modal" data-bs-target="#search-modal" aria-label="Cerca nel sito">
                    <svg class="icon icon-primary icon-md">
                      <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-arrow-left"></use>
                    </svg>
                  </button>
                  <h2>Cerca</h2>
                  <button class="search-link d-none d-md-block" type="button" data-bs-toggle="modal" data-bs-target="#search-modal" data-dismiss="modal" aria-label="Chiudi e torna alla pagina precedente" data-focus-mouse="false">
                    <svg class="icon icon-primary icon-md">
                      <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-close-big"></use>
                    </svg>
                  </button>
                </div>
                  <div class="form-group">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <div class="input-group-text">
                          <svg class="icon icon-primary icon-md">
                            <use href="<?php echo get_template_directory_uri(); ?>/inc/origin-tema-comuni/bootstrap-italia/svg/sprites.svg#it-search"></use>
                          </svg>
                        </div>
                      </div>
                      <label for="search" class="active">Con Etichetta</label>
                      <input type="search" class="form-control" id="search" name="s" placeholder="Cerca nel sito" value="">
                    </div>
                    <button type="submit" class="btn btn-primary">
                      <span class="">Cerca</span>
                    </button>
                  </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>