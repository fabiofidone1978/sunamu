<?php
// === CONFIG ===
$upload_dir = __DIR__ . '/uploads/';
$to_email = 'info@sunamu.music,fabio.fidone@alice.it';
$redirect_url = 'success.html';

// === Validazione iniziale ===
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    exit('Accesso negato.');
}

// === Sanificazione helper ===
function sanitize($value)
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

// === Raccolta dati ===
$form_id = $_POST['form_id'] ?? 'form-organico';

$data = [
    'nome' => sanitize($_POST['nome'] ?? ''),
    'data_nascita' => $_POST['data_nascita'] ?? '',
    'luogo_nascita' => sanitize($_POST['luogo_nascita'] ?? ''),
    'indirizzo' => sanitize($_POST['indirizzo'] ?? ''),
    'telefono' => sanitize($_POST['telefono'] ?? ''),
    'email' => filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL),
    'genitore_nome' => sanitize($_POST['genitore_nome'] ?? ''),
    'genitore_relazione' => sanitize($_POST['genitore_relazione'] ?? ''),
    'genitore_telefono' => sanitize($_POST['genitore_telefono'] ?? ''),
    'genitore_email' => sanitize($_POST['genitore_email'] ?? ''),
    'strumento' => isset($_POST['strumento']) ? implode(', ', $_POST['strumento']) : '',
    'registrato' => sanitize($_POST['registrato'] ?? ''),
    'pubblicato' => sanitize($_POST['pubblicato'] ?? ''),
    'motivazione' => sanitize($_POST['motivazione'] ?? ''),
    'pacchetto' => sanitize($_POST['pacchetto'] ?? ''),
    'preferenza_1' => sanitize($_POST['preferenza_1'] ?? ''),
    'alternativa' => sanitize($_POST['alternativa'] ?? ''),
    'veridicità' => isset($_POST['veridicità']) ? 'Y' : 'X',
    'gdpr' => isset($_POST['gdpr']) ? 'Y' : 'X',
    'autorizzazione_minore' => isset($_POST['autorizzazione_minore']) ? 'Y' : 'X',
    'regolamento' => isset($_POST['regolamento']) ? 'Y' : 'X',
];

// === Upload documento ===
if (!isset($_FILES['documento']) || $_FILES['documento']['error'] !== UPLOAD_ERR_OK) {
    exit('Errore nel caricamento del documento.');
}

$ext = strtolower(pathinfo($_FILES['documento']['name'], PATHINFO_EXTENSION));
$filename = uniqid('doc_', true) . '.' . $ext;
$filepath = $upload_dir . $filename;

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if (!move_uploaded_file($_FILES['documento']['tmp_name'], $filepath)) {
    exit('Impossibile salvare il file.');
}

// === Invio Email (facoltativo, se abilitato) ===
$subject = "Nuova candidatura Talento in Studio ($form_id)";
$body = "";
foreach ($data as $key => $value) {
    $body .= ucfirst(str_replace('_', ' ', $key)) . ": $value\n";
}
$headers = "From: no-reply@sunamu.it";

$mail_sent = mail($to_email, $subject, $body, $headers);

// === Salvataggio su database (facoltativo) ===
require 'db.php'; // Connessione PDO consigliata
// === Preparazione dei dati in JSON ===
$json_data = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

// === Preparazione nome file documento ===
$documento_nome = basename($filename);

// === Salvataggio nel database ===
$stmt = $pdo->prepare("INSERT INTO candidature (form_id, dati_json, documento) VALUES (:form_id, :dati_json, :documento)");
$stmt->execute([
    'form_id' => $form_id,
    'dati_json' => $json_data,
    'documento' => $documento_nome
]);


// === Redirect finale ===
header("Location: $redirect_url");
exit;
