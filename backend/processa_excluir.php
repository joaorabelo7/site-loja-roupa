<?php

require_once 'conexao.php';

$id_produto = $_POST['id_produto'] ?? '';

$sql = "DELETE FROM produto
        WHERE id_produto = :id_produto";

try {
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':id_produto' => $id_produto
    ]);

    header("Location: ../frontend/catalogo.php?status=sucesso");
    exit;

} catch (PDOException $e) {
    header("Location: ../frontend/catalogo.php?status=erro");
    exit;
}
?>