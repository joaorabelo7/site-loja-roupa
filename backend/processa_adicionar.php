<?php
require_once 'verifica_admin.php';
require_once 'conexao.php';
require_once 'upload_imagem.php';

$nome_produto  = $_POST['nome_produto']  ?? "";
$desc_produto  = $_POST['desc_produto']  ?? "";
$preco_produto = $_POST['preco_produto'] ?? "";
$caimento_produto = in_array($_POST['caimento_produto'] ?? '', ['slim', 'regular', 'boxy', 'oversized'], true) ? $_POST['caimento_produto'] : null;
$foto = '';

try {
    if (isset($_FILES['foto_arquivo']) && $_FILES['foto_arquivo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $foto = salvar_imagem($_FILES['foto_arquivo']);
    }

    $stmt = $pdo->prepare("INSERT INTO produto (nome, descricao, preco, foto, caimento)
                           VALUES (:nome, :descricao, :preco, :foto, :caimento)");
    $stmt->execute([
        ':nome'      => $nome_produto,
        ':descricao' => $desc_produto,
        ':preco'     => $preco_produto,
        ':foto'      => $foto,
        ':caimento'  => $caimento_produto
    ]);
    header("Location: ../frontend/admin_catalogo.php?status=sucesso");
} catch (Throwable $e) {
    apagar_imagem($foto);
    header("Location: ../frontend/admin_catalogo.php?status=erro&detalhe=" . urlencode($e->getMessage()));
}
exit;