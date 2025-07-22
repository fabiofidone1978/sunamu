document.addEventListener("DOMContentLoaded", () => {
  const container = document.getElementById("eventi-container");
  const filtro = document.getElementById("filtroLocalita");
  let eventi = [];

  fetch("eventi.php")
    .then((res) => res.json())
    .then((data) => {
      eventi = data;
      popolaFiltro(eventi);
      render(eventi);
    });

  filtro.addEventListener("change", () => {
    const val = filtro.value;
    if (val === "tutti") {
      render(eventi);
    } else {
      render(eventi.filter((e) => e.localita === val));
    }
  });

  function popolaFiltro(eventi) {
    const localitaUniche = [...new Set(eventi.map((e) => e.localita))];
    localitaUniche.sort().forEach((loc) => {
      const opt = document.createElement("option");
      opt.value = loc;
      opt.textContent = loc;
      filtro.appendChild(opt);
    });
  }

  function render(lista) {
    container.innerHTML = "";
    lista.forEach((e) => {
      const col = document.createElement("div");
      col.className = "col";
      col.innerHTML = `
        <div class="card h-100">
          <img src="\${e.immagine}" class="card-img-top" alt="\${e.alt}">
          <div class="card-body">
            <h5 class="card-title">\${e.titolo}</h5>
            <p class="card-text">\${e.descrizione}</p>
            <p class="text-muted">\${e.data} - \${e.localita}</p>
          </div>
        </div>`;
      container.appendChild(col);
    });
  }
});
