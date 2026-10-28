<?php
session_start();
if (!isset($_SESSION['id'])) { header('Location: login.php'); exit; }
require_once '../backend/conexao.php';

$p = ['id_produto'=>'','nome'=>'','descricao'=>'','preco'=>'','foto'=>''];
$editando = false;

if (!empty($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM produto WHERE id_produto = ?");
    $stmt->execute([(int)$_GET['id']]);
    $achado = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($achado) { $p = $achado; $editando = true; }
}

$titulo = $editando ? 'Editar produto' : 'Novo produto';
$acao = $editando ? '../backend/processa_editar.php' : '../backend/processa_adicionar.php';
include 'header.php';
?>
<div class="cartao">
  <h1><?= h($titulo) ?></h1>
  <form action="<?= $acao ?>" method="post">
    <?php if ($editando): ?><input type="hidden" name="id_produto" value="<?= (int)$p['id_produto'] ?>"><?php endif; ?>
    <label for="nome_produto">Nome</label>
    <input id="nome_produto" name="nome_produto" required value="<?= h($p['nome']) ?>">
    <label for="desc_produto">Descrição</label>
    <textarea id="desc_produto" name="desc_produto" rows="4"><?= h($p['descricao']) ?></textarea>
    <label for="preco_produto">Preço (R$)</label>
    <input id="preco_produto" name="preco_produto" type="number" step="0.01" min="0" required value="<?= h($p['preco']) ?>">
    <label for="foto_produto">Link da foto</label>
    <input id="foto_produto" name="foto_produto" type="text" placeholder="https://... ou imagens/camiseta.jpg" value="<?= h($p['foto']) ?>">
    <?php if ($p['foto']): ?><img class="previa" src="<?= h($p['foto']) ?>" alt="Foto atual"><?php endif; ?>
    <button class="btn" type="submit">Salvar produto</button>
  </form>
  <p class="troca"><a href="catalogo.php">Cancelar</a></p>
</div>
<?php include 'footer.php'; ?>
