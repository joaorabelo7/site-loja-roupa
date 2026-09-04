<?php
require_once'conexao.php';


$nome_produto = $_POST['nome_produto'] ?? "";
$desc_produto = $_POST['desc_produto'] ?? "";
$preco_produto = $_POST['preco_produto'] ?? "";
$foto_produto = $_POST['foto_produto'] ?? "";

$sql = "INSERT INTO produto (nome, descricao, preco, foto)
        VALUES (:nome, :descricao, :preco, :foto)";

try{
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':nome' => $nome_produto,
        ':descricao' => $desc_produto,
        ':preco' => $preco_produto,
        ':foto' => $foto_produto
    ]);
    header("Location: ../frontend/catalogo.php?status=sucessp");
    }catch(PDOException $e){
        header("Location: ../frontend/catalogo.php?status=erro");
    }
?>