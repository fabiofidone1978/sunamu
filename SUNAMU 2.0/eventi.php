<?php
$eventi = [];
if (file_exists('eventi.json')) {
    $json = file_get_contents('eventi.json');
    $eventi = json_decode($json, true);
}
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Eventi Musicali Sunamu - Concerti, Festival, Live Show</title>
    <meta name="description"
        content="Scopri i migliori eventi musicali organizzati da Sunamu: concerti, festival e spettacoli live in tutta Italia.">
    <link rel="canonical" href="https://www.sunamu.music/eventi.php">
    <meta name="robots" content="index, follow">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/style.css" />
</head>

<body class="bg-black text-white">
    <div id="navbar-placeholder"></div>

    <section class="py-5">
        <div class="container">
            <h1 class="mb-5 text-center display-5 fw-bold text-warning">Eventi</h1>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($eventi as $evento): ?>
                    <div class="col">
                        <div class="card h-100 bg-dark text-white">
                            <img src="<?php echo htmlspecialchars($evento['immagine']); ?>"
                                class="card-img-top object-fit-cover"
                                alt="<?php echo htmlspecialchars($evento['titolo']); ?>"
                                style="height: 250px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($evento['titolo']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($evento['descrizione']); ?></p>
                                <p class="text-muted"><?php echo htmlspecialchars($evento['data']); ?> -
                                    <?php echo htmlspecialchars($evento['luogo']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <div id="footer-placeholder"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    <script src="cookie-banner.js"></script>
    <script>
        fetch('partials/navbar.html')
            .then(res => res.text())
            .then(data => document.getElementById('navbar-placeholder').innerHTML = data);

        fetch('partials/footer.html')
            .then(res => res.text())
            .then(data => document.getElementById('footer-placeholder').innerHTML = data);
    </script>
</body>

</html>