<?php
session_start();
require_once "db.php";

// 1. Estrazione dalla tabella originale (es. form_submissions)
$query = "SELECT form_id, dati_json, documento, submitted_at FROM candidature";
$stmt = $pdo->query($query);
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($submissions as $row) {
    $form_id = $row['form_id'];
    $submitted_at = $row['submitted_at'];
    $json = json_decode($row['dati_json'], true);

    // Estrai i dati se presenti
    $nome = $json['nome'] ?? null;
    $email = $json['email'] ?? null;
    $telefono = $json['telefono'] ?? null;
    $colloquio = null;
    if (!empty($json['preferenza_1'])) {
        // Esempio: "Giovedì 19/06 ore 14:00"
        if (preg_match('/(\d{2})\/(\d{2}).*?(\d{2}:\d{2})/', $json['preferenza_1'], $match)) {
            // Ricostruisce la data in formato YYYY-MM-DD HH:MM
            $giorno = $match[1];
            $mese = $match[2];
            $ora = $match[3];
            $anno = date('Y'); // usa l'anno corrente (puoi adattare se serve)
            $colloquio = "$anno-$mese-$giorno $ora:00";
        }
    }

    // Inserimento nella tabella "appuntamenti" solo se non già presente
    $insert = $pdo->prepare("
        INSERT INTO appuntamenti (form_id, nome, email, telefono, disponibilita_colloquio, data_invio)
        VALUES (:form_id, :nome, :email, :telefono, :colloquio, :data_invio)
    ");
    $insert->execute([
        ':form_id' => $form_id,
        ':nome' => $nome,
        ':email' => $email,
        ':telefono' => $telefono,
        ':colloquio' => $colloquio,
        ':data_invio' => $submitted_at
    ]);
}

// 2. Visualizzazione tabella appuntamenti
$filtro_nome = $_GET['filtro_nome'] ?? '';
$filtro_data_da = $_GET['data_da'] ?? '';
$filtro_data_a = $_GET['data_a'] ?? '';

$query = "SELECT * FROM appuntamenti WHERE 1=1";
$params = [];

if ($filtro_nome !== '') {
    $query .= " AND (nome LIKE :filtro OR email LIKE :filtro)";
    $params[':filtro'] = "%$filtro_nome%";
}
if ($filtro_data_da !== '') {
    $query .= " AND disponibilita_colloquio >= :data_da";
    $params[':data_da'] = $filtro_data_da;
}
if ($filtro_data_a !== '') {
    $query .= " AND disponibilita_colloquio <= :data_a";
    $params[':data_a'] = $filtro_data_a;
}
$query .= " ORDER BY disponibilita_colloquio ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$appuntamenti = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <title>Appuntamenti – Talento in Studio</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            padding: 2rem;
        }

        h1 {
            margin-bottom: 1rem;
        }

        .logout {
            float: right;
            text-decoration: none;
            font-size: 0.9rem;
        }

        form.filtro {
            margin-bottom: 1.5rem;
            background: #fff;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 0 4px #ccc;
        }

        input[type="text"],
        input[type="date"] {
            padding: 0.5rem;
            margin-right: 1rem;
        }

        input[type="submit"] {
            padding: 0.5rem 1rem;
            background: #333;
            color: white;
            border: none;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            background: #fff;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 0.75rem;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        a.email-link {
            color: #0066cc;
            text-decoration: none;
        }

        .csv-export {
            margin-bottom: 1rem;
            display: inline-block;
            padding: 0.5rem 1rem;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
    </style>
</head>

<body>

    <h1>Appuntamenti fissati <a class="logout" href="logout.php">Logout</a></h1>

    <a class="csv-export" href="export.php" target="_blank">📄 Esporta CSV</a>

    <form method="get" class="filtro">
        <label>Nome o Email:</label>
        <input type="text" name="filtro_nome" value="<?= htmlspecialchars($filtro_nome) ?>">
        <label>Dal:</label>
        <input type="date" name="data_da" value="<?= htmlspecialchars($filtro_data_da) ?>">
        <label>Al:</label>
        <input type="date" name="data_a" value="<?= htmlspecialchars($filtro_data_a) ?>">
        <input type="submit" value="Filtra">
    </form>

    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Telefono</th>
                <th>Data colloquio</th>
                <th>Data invio</th>
                <th>Contatta</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($appuntamenti) === 0): ?>
                <tr>
                    <td colspan="6">Nessun risultato trovato.</td>
                </tr>
            <?php endif; ?>
            <?php foreach ($appuntamenti as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nome']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['telefono']) ?></td>
                    <td>
                        <?= !empty($row['disponibilita_colloquio']) && strtotime($row['disponibilita_colloquio']) !== false
                            ? date('d/m/Y H:i', strtotime($row['disponibilita_colloquio']))
                            : '—' ?>
                    </td>
                    <td><?= date('d/m/Y H:i', strtotime($row['data_invio'])) ?></td>
                    <td><a class="email-link"
                            href="mailto:<?= htmlspecialchars($row['email']) ?>?subject=Talento in Studio&body=Ciao <?= rawurlencode($row['nome']) ?>, ti contattiamo per il tuo colloquio...">✉️
                            Email</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>

</html>