<?php
require_once 'verifica_admin.php';
require_once 'conexao.php';

$id  = (int)($_POST['id_produto'] ?? 0);
$tam = $_POST['tamanho'] ?? [];
$num = function ($v) { $v = str_replace(',', '.', trim((string)$v)); return is_numeric($v) ? (float)$v : null; };

try {
    $pdo->beginTransaction();
    $modelagem = mb_substr(trim((string)($_POST['modelagem'] ?? '')), 0, 255);
    $pdo->prepare("UPDATE produto SET modelagem = ? WHERE id_produto = ?")->execute([$modelagem !== '' ? $modelagem : null, $id]);

    $pdo->prepare("DELETE FROM tamanhos WHERE id_produto = ?")->execute([$id]);
    $ins = $pdo->prepare("INSERT INTO tamanhos (id_produto, tamanho, comprimento, peito, ombro, manga) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($tam as $i => $nome) {
        $nome = trim($nome);
        if ($nome === '') continue;
        $ins->execute([$id, mb_substr($nome, 0, 10),
            $num($_POST['comprimento'][$i] ?? ''), $num($_POST['peito'][$i] ?? ''),
            $num($_POST['ombro'][$i] ?? ''), $num($_POST['manga'][$i] ?? '')]);
    }
    $pdo->commit();
    header("Location: ../frontend/admin_tamanhos.php?id=$id&status=sucesso");
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    header("Location: ../frontend/admin_tamanhos.php?id=$id&status=erro");
}
exit;