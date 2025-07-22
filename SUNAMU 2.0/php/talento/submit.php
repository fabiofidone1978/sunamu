<?php
// === CONFIG ===
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
    'veridicità' => isset($_POST['veridicità']) ? 'Sì' : 'No',
    'gdpr' => isset($_POST['gdpr']) ? 'Sì' : 'No',
    'autorizzazione_minore' => isset($_POST['autorizzazione_minore']) ? 'Sì' : 'No',
    'regolamento' => isset($_POST['regolamento']) ? 'Sì' : 'No',
];

// === Composizione email ===
$subject = "Nuova candidatura – $form_id";
$body = "Dati inviati:\n\n";
foreach ($data as $key => $value) {
    $body .= ucfirst(str_replace('_', ' ', $key)) . ": $value\n";
}

$headers = "From: noreply@sunamu.music\r\n";
$headers .= "Reply-To: " . $data['email'] . "\r\n";
$headers .= "Content-Type: text/plain; charset=utf-8\r\n";

// === Invio email ===
$mail_sent = mail($to_email, $subject, $body, $headers);

// === Redirect o errore ===
if ($mail_sent) {
    header("Location: $redirect_url");
    exit;
} else {
    exit("Errore durante l'invio dell'email.");
}
