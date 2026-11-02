<?php
require_once 'verifica_admin.php';
require_once 'conexao.php';
require_once 'upload_imagem.php';

$nome_produto  = $_POST['nome_produto']  ?? "";
$desc_produto  = $_POST['desc_produto']  ?? "";
$preco_produto = $_POST['preco_produto'] ?? "";
$foto = '';

try {
    if (isset($_FILES['foto_arquivo']) && $_FILES['foto_arquivo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $foto = salvar_imagem($_FILES['foto_arquivo']);
    }

    $stmt = $pdo->prepare("INSERT INTO produto (nome, descricao, preco, foto)
                           VALUES (:nome, :descricao, :preco, :foto)");
    $stmt->execute([
        ':nome'      => $nome_produto,
        ':descricao' => $desc_produto,
        ':preco'     => $preco_produto,
        ':foto'      => $foto
    ]);
    header("Location: ../frontend/admin_catalogo.php?status=sucesso");
} catch (Throwable $e) {
    apagar_imagem($foto);
    header("Location: ../frontend/admin_catalogo.php?status=erro&detalhe=" . urlencode($e->getMessage()));
}
exit;