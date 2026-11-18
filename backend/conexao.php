<?php

$charset = 'utf8mb4';

// Detecta se está rodando no seu PC (XAMPP) ou no servidor
$ehLocal = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1']);

if ($ehLocal) {
    $host    = 'localhost';
    $banco   = 'loja';
    $usuario = 'root';
    $senha   = '';
} else {
    $host    = 'sql312.infinityfree.com';
    $banco   = 'if0_43436535_root';
    $usuario = 'if0_43436535';
    $senha   = 'joaovilma1';
}

$dsn = "mysql:host=$host;dbname=$banco;charset=$charset";

$opcoes = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $usuario, $senha, $opcoes);
} catch (PDOException $e) {
    // Temporário, para descobrir o erro real. Depois volte para a mensagem genérica.
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}