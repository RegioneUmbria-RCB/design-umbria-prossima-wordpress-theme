<?php
global $post;
global $gallery_field;
$media = dci_get_meta($gallery_field);
?>

<article id="gallery" class="it-page-section mb-30 mt-5">
  <h3 class="h4 mb-3">Galleria fotografica</h3>
  <div class="row g-4">  
    <?php 
    $index = 0;
    foreach($media as $file): 
      $attachment_id = attachment_url_to_postid($file);
      $alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
      if (empty($alt)) {
        $alt = get_the_title($attachment_id); // fallback
      }
    ?>
      <div class="col-12 col-md-4 col-lg-4">
        <div class="border border-light rounded shadow-sm">
          <div class="card no-after rounded">
            <div class="img-responsive-wrapper">
              <div class="img-responsive img-responsive-panoramic">
                <figure class="img-wrapper">
                  <img src="<?php echo esc_url($file); ?>" 
                       alt="<?php echo esc_attr($alt); ?>" 
                       class="img-thumbnail gallery-image" 
                       style="cursor: pointer;" 
                       data-bs-toggle="modal" 
                       data-bs-target="#galleryModal" 
                       data-bs-image="<?php echo esc_url($file); ?>" 
                       data-bs-alt="<?php echo esc_attr($alt); ?>"
                       data-bs-index="<?php echo $index; ?>">
                </figure>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php 
      $index++;
    endforeach; ?>
  </div>
</article>

<!-- Modal -->
<div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
  <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
  <button type="button" class="position-absolute top-50 start-0 translate-middle-y btn btn-link text-white fs-1" id="galleryPrev" aria-label="Previous">&#10094;</button>
  <button type="button" class="position-absolute top-50 end-0 translate-middle-y btn btn-link text-white fs-1" id="galleryNext" aria-label="Next"> &#10095;</button>
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-transparent border-0 shadow-none position-relative">
      <img src="" alt="" class="img-fluid rounded shadow" id="galleryModalImage">
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const galleryModal = document.getElementById('galleryModal');
  const modalImage = document.getElementById('galleryModalImage');
  const images = document.querySelectorAll('.gallery-image');
  let currentIndex = 0;

  function showImage(index) {
    const img = images[index];
    modalImage.src = img.getAttribute('data-bs-image');
    modalImage.alt = img.getAttribute('data-bs-alt');
    currentIndex = index;
  }

  // Apertura modale
  galleryModal.addEventListener('show.bs.modal', function (event) {
    const trigger = event.relatedTarget;
    const index = parseInt(trigger.getAttribute('data-bs-index'));
    showImage(index);
  });

  // Freccia sinistra
  document.getElementById('galleryPrev').addEventListener('click', function() {
    let newIndex = (currentIndex - 1 + images.length) % images.length;
    showImage(newIndex);
  });

  // Freccia destra
  document.getElementById('galleryNext').addEventListener('click', function() {
    let newIndex = (currentIndex + 1) % images.length;
    showImage(newIndex);
  });

  // Navigazione con tastiera
  document.addEventListener('keydown', function(event) {
    if (!galleryModal.classList.contains('show')) return;
    if (event.key === 'ArrowLeft') {
      document.getElementById('galleryPrev').click();
    } else if (event.key === 'ArrowRight') {
      document.getElementById('galleryNext').click();
    }
  });
});
</script>
