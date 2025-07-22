<?php
require 'db.php';

$stmt = $pdo->query("SELECT * FROM candidature ORDER BY submitted_at DESC");
$candidature = $stmt->fetchAll();

echo "<h1>Candidature ricevute</h1>";

foreach ($candidature as $candidatura) {
    echo "<div style='border:1px solid #ccc; margin:20px; padding:15px'>";
    echo "<strong>Modulo:</strong> " . htmlspecialchars($candidatura['form_id']) . "<br>";
    echo "<strong>Data:</strong> " . $candidatura['submitted_at'] . "<br>";
    echo "<strong>Documento:</strong> <a href='../" . htmlspecialchars($candidatura['documento']) . "' target='_blank'>Visualizza</a><br>";

    $dati = json_decode($candidatura['dati_json'], true);
    if (is_array($dati)) {
        echo "<ul>";
        foreach ($dati as $key => $value) {
            echo "<li><strong>" . htmlspecialchars($key) . ":</strong> ";
            if (is_array($value)) {
                echo implode(", ", array_map('htmlspecialchars', $value));
            } else {
                echo htmlspecialchars($value);
            }
            echo "</li>";
        }
        echo "</ul>";
    }
    echo "</div>";
}
