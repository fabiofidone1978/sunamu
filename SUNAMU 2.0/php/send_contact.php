<?php
// send_contact.php
$host = "31.11.39.211";
$user = "Sql1855273";
$pass = "Daniela.77";
$dbname = "sunam220_Sql1855273_5";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$nome = htmlspecialchars($_POST['nome']);
$email = htmlspecialchars($_POST['email']);
$messaggio = htmlspecialchars($_POST['messaggio']);

$stmt = $conn->prepare("INSERT INTO contatti (nome, email, messaggio) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $nome, $email, $messaggio);
$stmt->execute();
$stmt->close();
$conn->close();

$to = "sunamuevent@gmail.com";
$subject = "Nuovo messaggio da Sunamu";
$body = "Hai ricevuto un nuovo messaggio da:\n\nNome: $nome\nEmail: $email\nMessaggio: $messaggio";
$headers = "From: info@sunamu.music\r\n";
$headers .= "Bcc: $bcc\r\n";

mail($to, $subject, $body, $headers);

header("Location: ../contatti.html?success=1");
exit();
