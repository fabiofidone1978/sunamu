<?php
$artisti = [];
if (file_exists('artisti.json')) {
    $json = file_get_contents('artisti.json');
    $artisti = json_decode($json, true);
}
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>I Nostri Artisti - Sunamu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/style.css" />
</head>

<body class="bg-black text-warning">
    <div id="navbar-placeholder"></div>

    <section class="py-5">
        <div class="container">
            <h1 class="text-center mb-5 display-5 fw-bold">I Nostri Artisti</h1>
            <div id="artists-grid" class="row row-cols-1 row-cols-md-2 g-4">
                <?php foreach ($artisti as $artista): ?>
                    <div class="col">
                        <div class="card h-100 bg-dark text-white"
                            onclick="showArtistDetail('<?php echo $artista['id']; ?>')">
                            <img src="<?php echo htmlspecialchars($artista['immagini'][0]); ?>"
                                class="card-img-top artist-img object-fit-cover" alt="Artista in scena"
                                style="height:250px;object-fit:cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($artista['nome']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($artista['descrizione']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php foreach ($artisti as $artista): ?>
                <div id="<?php echo $artista['id']; ?>" class="detail" style="display:none;">
                    <h2 class="mb-3"><?php echo htmlspecialchars($artista['nome']); ?></h2>
                    <?php if (!empty($artista['citazione'])): ?>
                        <blockquote class="blockquote text-warning fst-italic">
                            "<?php echo htmlspecialchars($artista['citazione']); ?>"</blockquote>
                    <?php endif; ?>

                    <?php if (!empty($artista['video'])): ?>
                        <div class="ratio ratio-16x9 mt-4">
                            <iframe src="<?php echo htmlspecialchars($artista['video']); ?>" title="Video Artista"
                                allowfullscreen></iframe>
                        </div>
                    <?php else: ?>
                        <div id="carousel-<?php echo $artista['id']; ?>" class="carousel slide mt-4" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <?php foreach ($artista['immagini'] as $index => $img): ?>
                                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                        <img src="<?php echo htmlspecialchars($img); ?>" class="d-block w-100" alt="Slide artista">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <button class="btn btn-warning mt-4" onclick="backToGrid()">Torna indietro</button>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <div id="footer-placeholder"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showArtistDetail(id) {
            document.getElementById('artists-grid').style.display = 'none';
            document.querySelectorAll('.detail').forEach(div => div.style.display = 'none');
            document.getElementById(id).style.display = 'block';
        }

        function backToGrid() {
            document.querySelectorAll('.detail').forEach(div => div.style.display = 'none');
            document.getElementById('artists-grid').style.display = 'flex';
        }

        fetch('partials/navbar.html')
            .then(res => res.text())
            .then(data => document.getElementById('navbar-placeholder').innerHTML = data);

        fetch('partials/footer.html')
            .then(res => res.text())
            .then(data => document.getElementById('footer-placeholder').innerHTML = data);
    </script>
</body>

</html>