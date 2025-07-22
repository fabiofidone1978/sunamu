document.addEventListener("DOMContentLoaded", () => {
  fetch("artisti.json")
    .then((response) => response.json())
    .then((artisti) => {
      const grid = document.getElementById("artists-grid");
      const container = grid.parentElement;

      artisti.forEach((artista) => {
        // Griglia
        const col = document.createElement("div");
        col.className = "col";

        const card = document.createElement("div");
        card.className = "card h-100";
        card.onclick = () => showArtistDetail(artista.id);

        const img = document.createElement("img");
        img.className = "card-img-top artist-img";
        img.src = artista.img;
        img.alt = artista.nome;

        const body = document.createElement("div");
        body.className = "card-body";

        const h5 = document.createElement("h5");
        h5.className = "card-title";
        h5.textContent = artista.nome;

        const p = document.createElement("p");
        p.className = "card-text";
        p.textContent = artista.desc;

        body.appendChild(h5);
        body.appendChild(p);
        card.appendChild(img);
        card.appendChild(body);
        col.appendChild(card);
        grid.appendChild(col);

        // Dettaglio
        const detail = document.createElement("div");
        detail.id = artista.id;
        detail.className = "detail";

        const h2 = document.createElement("h2");
        h2.className = "mb-3";
        h2.textContent = artista.nome;
        detail.appendChild(h2);

        if (artista.cit) {
          const quote = document.createElement("blockquote");
          quote.className = "blockquote text-warning fst-italic";
          quote.textContent = `"${artista.cit}"`;
          detail.appendChild(quote);
        }

        if (artista.youtube) {
          const iframeWrapper = document.createElement("div");
          iframeWrapper.className = "ratio ratio-16x9 mt-4";

          const iframe = document.createElement("iframe");
          iframe.src = artista.youtube;
          iframe.title = "Video artista";
          iframe.allowFullscreen = true;

          iframeWrapper.appendChild(iframe);
          detail.appendChild(iframeWrapper);
        }

        if (artista.carousel && artista.carousel.length > 0) {
          const carouselId = `carousel-${artista.id}`;
          const carousel = document.createElement("div");
          carousel.id = carouselId;
          carousel.className = "carousel slide mt-4";
          carousel.setAttribute("data-bs-ride", "carousel");

          const inner = document.createElement("div");
          inner.className = "carousel-inner";

          artista.carousel.forEach((imgSrc, i) => {
            const item = document.createElement("div");
            item.className = "carousel-item";
            if (i === 0) item.classList.add("active");

            const img = document.createElement("img");
            img.src = imgSrc;
            img.className = "d-block w-100";
            img.alt = artista.nome + " Slide " + (i + 1);

            item.appendChild(img);
            inner.appendChild(item);
          });

          carousel.appendChild(inner);
          detail.appendChild(carousel);
        }

        if (artista.link) {
          const links = document.createElement("div");
          links.className = "mt-4";

          for (const [label, url] of Object.entries(artista.link)) {
            const a = document.createElement("a");
            a.href = url;
            a.target = "_blank";
            a.className = "btn btn-outline-warning btn-sm me-2";
            a.textContent = label;
            links.appendChild(a);
          }

          detail.appendChild(links);
        }

        const backBtn = document.createElement("button");
        backBtn.className = "btn btn-warning mt-4";
        backBtn.textContent = "Torna indietro";
        backBtn.onclick = backToGrid;
        detail.appendChild(backBtn);

        container.appendChild(detail);
      });
    })
    .catch((err) => console.error("Errore nel caricamento artisti:", err));
});
