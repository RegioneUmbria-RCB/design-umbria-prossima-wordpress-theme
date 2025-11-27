function openFocalPointModal(postId, postImage, initialAxesPosition = null) {
  if (postId && postImage) {
    const modal = document.createElement("div");
    modal.classList.add("focal-point-modal");
    modal.innerHTML = `
      <div class="focal-point-modal-content">
          <span class="close-modal">&times;</span>
          <h2>Seleziona il Punto Focale</h2>
          <div class="image-container">
              <img id="focal-point-image" src="${postImage}" />
              <div id="focal-point-marker"></div>
          </div>
          <button style="margin-top:20px;" class="button button-primary" id="save-focal-point">Salva Punto Focale</button>
      </div>
  `;
    document.body.appendChild(modal);

    const closeModal = modal.querySelector(".close-modal");
    const focalPointMarker = document.getElementById("focal-point-marker");
    const focalPointImage = document.getElementById("focal-point-image");

    // Posiziona il marker se esiste già un punto focale
    if (initialAxesPosition) {
      focalPointMarker.style.left = `${initialAxesPosition?.x}%`;
      focalPointMarker.style.top = `${initialAxesPosition?.y}%`;
    }

    // Funzione per posizionare il marker sul click, in percentuale
    focalPointImage.addEventListener("click", function (event) {
      const rect = focalPointImage.getBoundingClientRect();
      const x = event.clientX - rect.left;
      const y = event.clientY - rect.top;

      // Calcola la posizione in percentuale
      const xPercent = (x / rect.width) * 100;
      const yPercent = (y / rect.height) * 100;

      // Posiziona il marker con valori percentuali
      focalPointMarker.style.left = `${xPercent}%`;
      focalPointMarker.style.top = `${yPercent}%`;

      // Salva i valori percentuali nei dataset del marker
      focalPointMarker.dataset.x = xPercent;
      focalPointMarker.dataset.y = yPercent;
    });

    modal.style.display = "block";

    // Chiudere la finestra modale
    closeModal.addEventListener("click", function () {
      modal.style.display = "none";
    });

    // Salva le coordinate del punto focale
    document
      .getElementById("save-focal-point")
      .addEventListener("click", function () {
        const x = focalPointMarker.dataset.x;
        const y = focalPointMarker.dataset.y;

        // Effettua una richiesta AJAX per salvare il punto focale
        const data = new FormData();

        data.append("action", "save_focal_point");
        data.append("post_id", postId);
        data.append("x", x);
        data.append("y", y);

        fetch(ajaxurl, {
          method: "POST",
          body: data,
        })
          .then((response) => response.json())
          .then((result) => {
            if (result.success) {
              alert("Punto focale salvato con successo!");
              modal.style.display = "none";
              window.location.reload();
            }
          });
      });
  }
}
