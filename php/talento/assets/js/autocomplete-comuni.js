document.addEventListener("DOMContentLoaded", () => {
  const comuneInput = document.getElementById("luogo_nascita");
  const provinciaInput = document.getElementById("provincia_output");
  const regioneInput = document.getElementById("regione_output");
  const siglaInput = document.getElementById("sigla_output");
  const datalist = document.getElementById("comuni-list");

  if (!comuneInput) return;

  fetch("assets/data/comuni.json")
    .then((response) => response.json())
    .then((comuni) => {
      // Popola datalist
      comuni.forEach((c) => {
        if (c.comune) {
          const option = document.createElement("option");
          option.value = c.comune;
          datalist.appendChild(option);
        }
      });

      comuneInput.addEventListener("input", () => {
        const valore = (comuneInput.value || "").toLowerCase().trim();
        const trovato = comuni.find(
          (c) => c.comune && c.comune.toLowerCase() === valore
        );

        if (trovato) {
          provinciaInput.value = trovato.provincia || "";
          regioneInput.value = trovato.regione || "";
          siglaInput.value = trovato.sigla || "";
        } else {
          provinciaInput.value = "";
          regioneInput.value = "";
          siglaInput.value = "";
        }
      });
    })
    .catch((error) =>
      console.error("Errore nel caricamento comuni.json", error)
    );
});
