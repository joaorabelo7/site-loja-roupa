<?php
require_once 'verifica_admin.php';
require_once 'conexao.php';
require_once 'upload_imagem.php';

$id_produto    = $_POST['id_produto']    ?? '';
$nome_produto  = $_POST['nome_produto']  ?? "";
$desc_produto  = $_POST['desc_produto']  ?? "";
$preco_produto = $_POST['preco_produto'] ?? "";
$caimento_produto = in_array($_POST['caimento_produto'] ?? '', ['slim', 'regular', 'boxy', 'oversized'], true) ? $_POST['caimento_produto'] : null;
$novaFoto = '';

try {
    $stmt = $pdo->prepare("SELECT foto FROM produto WHERE id_produto = ?");
    $stmt->execute([$id_produto]);
    $fotoAtual = (string)$stmt->fetchColumn();

    if (isset($_FILES['foto_arquivo']) && $_FILES['foto_arquivo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $novaFoto = salvar_imagem($_FILES['foto_arquivo']);
    }

    $stmt = $pdo->prepare("UPDATE produto
                           SET nome = :nome, descricao = :descricao, preco = :preco, foto = :foto, caimento = :caimento
                           WHERE id_produto = :id_produto");
    $stmt->execute([
        ':nome'       => $nome_produto,
        ':descricao'  => $desc_produto,
        ':preco'      => $preco_produto,
        ':foto'       => $novaFoto ?: $fotoAtual,
        ':caimento'   => $caimento_produto,
        ':id_produto' => $id_produto
    ]);

    if ($novaFoto) apagar_imagem($fotoAtual);   // remove a foto antiga
    header("Location: ../frontend/admin_catalogo.php?status=sucesso");
} catch (Throwable $e) {
    apagar_imagem($novaFoto);
    header("Location: ../frontend/admin_catalogo.php?status=erro&detalhe=" . urlencode($e->getMessage()));
}
exit;