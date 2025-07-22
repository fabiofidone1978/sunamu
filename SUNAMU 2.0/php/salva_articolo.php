<?php
$data = json_decode(file_get_contents('../php/posts.json'), true);

$new = [
    "titolo" => $_POST["titolo"],
    "categoria" => $_POST["categoria"],
    "estratto" => $_POST["estratto"],
    "contenuto" => $_POST["contenuto"],
    "autore" => $_POST["autore"],
    "data" => date("Y-m-d")
];

$data[] = $new;

file_put_contents('../php/posts.json', json_encode($data, JSON_PRETTY_PRINT));
header('Location: ../blog.html');
