<?php
session_start();
require_once 'conexao.php';

$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

try {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        session_regenerate_id(true);
        $_SESSION['id']    = $usuario['id'];
        $_SESSION['nome']  = $usuario['nome'];
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['admin'] = ((int)($usuario['admin'] ?? 0) === 1);

        header("Location: ../frontend/catalogo.php");
        exit;
    } else {
        echo "<script>alert('E-mail ou senha incorretos!'); window.history.back();</script>";
        exit;
    }
} catch (PDOException $e) {
    die("Erro no login. Tente novamente.");
}