<?php
require_once 'verifica_admin.php';
require_once 'conexao.php';
require_once 'upload_imagem.php';

$id_produto = $_POST['id_produto'] ?? '';

try {
    $stmt = $pdo->prepare("SELECT foto FROM produto WHERE id_produto = ?");
    $stmt->execute([$id_produto]);
    $foto = (string)$stmt->fetchColumn();

    $stmt = $pdo->prepare("DELETE FROM produto WHERE id_produto = :id_produto");
    $stmt->execute([':id_produto' => $id_produto]);

    apagar_imagem($foto);
    header("Location: ../frontend/admin_catalogo.php?status=sucesso");
} catch (PDOException $e) {
    header("Location: ../frontend/admin_catalogo.php?status=erro");
}
exit;