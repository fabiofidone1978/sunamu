<style>
    .autocomplete-lista {
        border: 1px solid #ccc;
        max-height: 200px;
        overflow-y: auto;
        position: absolute;
        background: white;
        z-index: 1000;
    }

    .autocomplete-lista div {
        padding: 4px;
        cursor: pointer;
    }

    .autocomplete-lista div:hover {
        background-color: #eee;
    }
</style>

<form action="submit.php" method="POST" enctype="multipart/form-data" id="form-candidatura">
    <input type="hidden" name="form_id" value="<?= htmlspecialchars($form_id ?? 'form-organico') ?>">

    <h3>Dati Anagrafici</h3>
    <input name="nome" required placeholder="Nome e Cognome">

    <label for="data_nascita">Data di nascita</label>
    <input name="data_nascita" id="data_nascita" required type="date">
    <div id="maggiorenne_check" style="margin-bottom:1rem; font-weight: bold;"></div>

    <input name="luogo_nascita" required list="comuni-list" placeholder="Comune di nascita" id="luogo_nascita"
        autocomplete="off">
    <datalist id="comuni-list"></datalist>

    <input name="provincia" id="provincia_output" placeholder="Provincia" readonly>
    <input name="sigla" id="sigla_output" placeholder="Sigla" readonly>
    <input name="regione" id="regione_output" placeholder="Regione" readonly>

    <input name="indirizzo" required placeholder="Indirizzo">

    <?php
    $prefissi = json_decode(file_get_contents('assets/data/prefissi.json'), true);
    ?>
    <select name="prefisso" id="prefisso" required>
        <?php foreach ($prefissi as $p): ?>
            <option value="<?= htmlspecialchars($p['code']) ?>">
                <?= htmlspecialchars($p['country']) ?> (<?= htmlspecialchars($p['code']) ?>)
            </option>
        <?php endforeach; ?>
    </select>

    <input name="telefono" required placeholder="Telefono" id="telefono">
    <span class="error" id="telError" style="display:none; color:#f88;">Numero non valido</span>

    <input name="email" required type="email" placeholder="Email" id="email">
    <span class="error" id="emailError" style="display:none; color:#f88;">Email non valida</span>



    <div id="dati_genitore" style="display: none;">
        <h3>Dati Genitore (se minorenne)</h3>
        <input name="genitore_nome" id="genitore_nome" placeholder="Nome e Cognome">
        <input name="genitore_relazione" id="genitore_relazione" placeholder="Relazione (Padre/Madre/Tutore)">
        <input name="genitore_telefono" id="genitore_telefono" placeholder="Telefono">
        <input name="genitore_email" id="genitore_email" placeholder="Email">
        <span class="error" id="genitoreEmailError" style="display:none; color:#f88;">Email genitore non valida</span>
    </div>

    <h3>Profilo Artistico</h3>
    <label><input type="checkbox" name="strumento[]" value="Voce"> Voce</label>
    <label><input type="checkbox" name="strumento[]" value="Chitarra"> Chitarra</label>
    <label><input type="checkbox" name="strumento[]" value="Pianoforte"> Pianoforte</label>
    <label><input type="checkbox" name="strumento[]" value="Produzione"> Produzione</label>
    <label><input type="checkbox" name="strumento[]" value="Altro"> Altro</label>

    <p>Hai mai registrato un brano in studio?</p>
    <select name="registrato">
        <option value="Sì">Sì</option>
        <option value="No">No</option>
    </select>

    <p>Hai già pubblicato musica?</p>
    <select name="pubblicato">
        <option value="Sì">Sì</option>
        <option value="No">No</option>
    </select>

    <label>Motivazione</label>
    <textarea name="motivazione" required></textarea>

    <h3>Pacchetto di interesse</h3>
    <select name="pacchetto" required>
        <option>Base</option>
        <option>Pro</option>
        <option>Premium</option>
        <option>Da decidere dopo il colloquio</option>
    </select>


    <h3>Consensi</h3>
    <label><input type="checkbox" name="veridicità" required> Dichiaro che tutte le informazioni sono
        veritiere</label><br>
    <label><input type="checkbox" name="gdpr" required> Acconsento al trattamento dati GDPR</label><br>
    <label id="autorizzazione_label">
        <input type="checkbox" name="autorizzazione_minore" id="autorizzazione_minore" required>
        (Se minorenne) Autorizzo la partecipazione
    </label>
    <label><input type="checkbox" name="regolamento" required> Accetto il regolamento</label><br>

    <button type="submit" id="submitBtn">Invia candidatura</button>
</form>

<!-- Validazione JS -->
<script>
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }


    document.getElementById('data_nascita').addEventListener('change', function () {
        const nascita = new Date(this.value);
        const oggi = new Date();
        let anni = oggi.getFullYear() - nascita.getFullYear();
        const mese = oggi.getMonth() - nascita.getMonth();
        if (mese < 0 || (mese === 0 && oggi.getDate() < nascita.getDate())) anni--;

        const msg = document.getElementById('maggiorenne_check');
        const datiGenitore = document.getElementById('dati_genitore');

        // Aggiunta: gestione autorizzazione minore
        const autorizzazioneBox = document.getElementById('autorizzazione_minore');
        const autorizzazioneLabel = document.getElementById('autorizzazione_label');

        if (anni >= 18) {
            msg.textContent = "✅ Utente maggiorenne";
            msg.style.color = "lightgreen";

            datiGenitore.style.display = "none";
            datiGenitore.querySelectorAll('input').forEach(el => {
                el.required = false;
                el.value = '';
            });

            autorizzazioneBox.required = false;
            autorizzazioneBox.checked = false;
            autorizzazioneLabel.style.display = "none";
        } else {
            msg.textContent = "⚠️ Utente minorenne: compilare i dati del genitore";
            msg.style.color = "orange";

            datiGenitore.style.display = "block";
            datiGenitore.querySelectorAll('input').forEach(el => {
                el.required = true;
            });

            autorizzazioneBox.required = true;
            autorizzazioneLabel.style.display = "block";
        }
    });


    document.getElementById('form-candidatura').addEventListener('submit', function (e) {
        let valid = true;

        const email = document.getElementById('email');
        if (!isValidEmail(email.value)) {
            document.getElementById('emailError').style.display = 'inline';
            valid = false;
        } else {
            document.getElementById('emailError').style.display = 'none';
        }

        const tel = document.getElementById('telefono');
        if (!/^\d{9,11}$/.test(tel.value)) {
            document.getElementById('telError').style.display = 'inline';
            valid = false;
        } else {
            document.getElementById('telError').style.display = 'none';
        }

        const genEmail = document.getElementById('genitore_email');
        if (genEmail && genEmail.required && !isValidEmail(genEmail.value)) {
            document.getElementById('genitoreEmailError').style.display = 'inline';
            valid = false;
        } else {
            document.getElementById('genitoreEmailError').style.display = 'none';
        }

        if (!valid) e.preventDefault();
    });


    window.addEventListener('DOMContentLoaded', () => {
        // Forza visibilità iniziale come maggiorenne
        const msg = document.getElementById('maggiorenne_check');
        const datiGenitore = document.getElementById('dati_genitore');
        const autorizzazioneBox = document.getElementById('autorizzazione_minore');
        const autorizzazioneLabel = document.getElementById('autorizzazione_label');

        datiGenitore.style.display = "none";
        datiGenitore.querySelectorAll('input').forEach(el => {
            el.required = false;
            el.value = '';
        });

        autorizzazioneBox.required = false;
        autorizzazioneBox.checked = false;
        autorizzazioneLabel.style.display = "none";

        msg.textContent = "";
    });
</script>

<script src="assets/js/autocomplete-comuni.js" defer></script>