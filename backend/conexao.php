<?php
$host    = 'localhost';
$banco   = 'loja';
$usuario = 'root';
$senha   = '';          // no XAMPP o padrão é vazio; no Laragon também
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$banco;charset=$charset";

$opcoes = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // erros viram PDOException (seu try/catch depende disso)
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,                    // prepared statements de verdade
];

try {
    $pdo = new PDO($dsn, $usuario, $senha, $opcoes);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados.");
}