document.addEventListener("DOMContentLoaded", () => {
  if (!localStorage.getItem("cookieConsent")) {
    const banner = document.createElement("div");
    banner.innerHTML = `
      <div class="cookie-banner bg-dark text-white text-center p-3" style="position: fixed; bottom: 0; width: 100%; z-index: 1000;">
        Questo sito utilizza cookie per migliorare l'esperienza utente.
        <button id="acceptAll" class="btn btn-warning btn-sm ms-3 me-2">Accetta tutti</button>
        <button id="acceptTechOnly" class="btn btn-outline-light btn-sm">Solo tecnici</button>
        <a href="cookie-policy.html" class="btn btn-link text-decoration-underline text-light btn-sm">Informativa</a>
      </div>`;
    document.body.appendChild(banner);

    document.getElementById("acceptAll").addEventListener("click", () => {
      localStorage.setItem("cookieConsent", "all");
      banner.remove();
    });

    document.getElementById("acceptTechOnly").addEventListener("click", () => {
      localStorage.setItem("cookieConsent", "technical");
      banner.remove();
    });
  }
});
