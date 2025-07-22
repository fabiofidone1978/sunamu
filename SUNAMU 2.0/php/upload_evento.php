<?php
// === CONFIG ===
$uploadDir = __DIR__ . '/../assets/images/eventi/';
$jsonFile = __DIR__ . '/../eventi.json';

// === Solo POST ===
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin.html?esito=errore');
    exit;
}

// === Sanifica ===
function sanitize($text)
{
    return htmlspecialchars(trim($text), ENT_QUOTES, 'UTF-8');
}

// === Raccogli dati ===
$titolo = sanitize($_POST['titolo'] ?? '');
$data = sanitize($_POST['data'] ?? '');
$luogo = sanitize($_POST['luogo'] ?? '');
$descrizione = sanitize($_POST['descrizione'] ?? '');

// === Gestione immagine ===
if (!isset($_FILES['immagine']) || $_FILES['immagine']['error'] !== UPLOAD_ERR_OK) {
    header('Location: ../admin.html?esito=errore');
    exit;
}
$ext = strtolower(pathinfo($_FILES['immagine']['name'], PATHINFO_EXTENSION));
$filename = uniqid('evento_', true) . '.' . $ext;
$destPath = $uploadDir . $filename;

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if (!move_uploaded_file($_FILES['immagine']['tmp_name'], $destPath)) {
    header('Location: ../admin.html?esito=errore');
    exit;
}

// === Nuovo evento ===
$evento = [
    'titolo' => $titolo,
    'data' => $data,
    'luogo' => $luogo,
    'descrizione' => $descrizione,
    'immagine' => 'assets/images/eventi/' . $filename
];

// === Aggiorna JSON ===
$eventi = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];
$eventi[] = $evento;
file_put_contents($jsonFile, json_encode($eventi, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

// === Redirect con successo ===
header('Location: ../admin.html?esito=ok');
exit;
?>