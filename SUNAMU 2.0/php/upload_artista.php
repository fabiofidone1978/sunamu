<?php
// === Percorsi file ===
$uploadDir = __DIR__ . '/../assets/images/artisti/';
$jsonFile = __DIR__ . '/../artisti.json';

// === Solo POST ===
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin.html?esito=errore');
    exit;
}

// === Funzione sanificazione
function sanitize($text)
{
    return htmlspecialchars(trim($text), ENT_QUOTES, 'UTF-8');
}

// === Raccolta dati
$nome = sanitize($_POST['nome'] ?? '');
$bio = sanitize($_POST['bio'] ?? '');
$socialRaw = $_POST['social'] ?? '';

// === Validazione base
if (!$nome || !$bio || !isset($_FILES['immagine']) || $_FILES['immagine']['error'] !== UPLOAD_ERR_OK) {
    header('Location: ../admin.html?esito=errore');
    exit;
}

// === Gestione immagine
$ext = strtolower(pathinfo($_FILES['immagine']['name'], PATHINFO_EXTENSION));
$filename = uniqid('artista_', true) . '.' . $ext;
$destPath = $uploadDir . $filename;

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if (!move_uploaded_file($_FILES['immagine']['tmp_name'], $destPath)) {
    header('Location: ../admin.html?esito=errore');
    exit;
}

// === Parsing link social
$social = array_filter(array_map('trim', explode("\n", $socialRaw)));

// === Nuovo artista
$artista = [
    'nome' => $nome,
    'bio' => $bio,
    'immagine' => 'assets/images/artisti/' . $filename,
    'social' => $social
];

// === Aggiornamento JSON
$artisti = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];
$artisti[] = $artista;

file_put_contents($jsonFile, json_encode($artisti, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

// === Redirect con esito
header('Location: ../admin.html?esito=ok');
exit;
?>