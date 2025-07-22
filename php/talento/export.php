<?php
session_start();
if (!isset($_SESSION['autenticato'])) {
    die("Accesso non autorizzato.");
}

require 'db.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="candidature_export.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Data', 'Modulo', 'Documento', 'Campo', 'Valore']);

$stmt = $pdo->query("SELECT * FROM candidature ORDER BY submitted_at DESC");

while ($row = $stmt->fetch()) {
    $dati = json_decode($row['dati_json'], true);
    if (!is_array($dati)) continue;

    foreach ($dati as $campo => $valore) {
        if (is_array($valore)) $valore = implode(" | ", $valore);
        fputcsv($output, [
            $row['id'],
            $row['submitted_at'],
            $row['form_id'],
            $row['documento'],
            $campo,
            $valore
        ]);
    }
}

fclose($output);
exit;
?>