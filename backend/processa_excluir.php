<?php
require_once 'verifica_admin.php';
require_once 'conexao.php';

$id_produto = $_POST['id_produto'] ?? '';

try {
    $stmt = $pdo->prepare("DELETE FROM produto WHERE id_produto = :id_produto");
    $stmt->execute([':id_produto' => $id_produto]);
    header("Location: ../frontend/admin_catalogo.php?status=sucesso");
} catch (PDOException $e) {
    header("Location: ../frontend/admin_catalogo.php?status=erro");
}
exit;