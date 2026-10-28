<?php
require_once '../backend/conexao.php';
$titulo = 'Catálogo';

try {
    $produtos = $pdo->query("SELECT * FROM produto ORDER BY id_produto DESC")->fetchAll(PDO::FETCH_ASSOC);
    $erro = false;
} catch (PDOException $e) {
    $produtos = [];
    $erro = true;
}

include 'header.php';
?>
<div class="cabecalho">
  <h1>Catálogo</h1>
  <?php if ($admin): ?><a class="btn" href="admin_catalogo.php">Editar catálogo</a><?php endif; ?>
</div>

<?php if ($erro): ?>
  <div class="aviso erro" role="alert">Não foi possível carregar os produtos. Tente novamente.</div>
<?php endif; ?>

<?php if (!$produtos && !$erro): ?>
  <div class="vazio">
    <h2>Nenhum produto por aqui ainda</h2>
    <p><?= $admin ? 'Use "Editar catálogo" para adicionar o primeiro produto.' : 'Volte em breve para ver as novidades.' ?></p>
  </div>
<?php else: ?>
  <div class="grade">
  <?php foreach ($produtos as $p): ?>
    <article class="produto">
      <?php if (!empty($p['foto'])): ?>
        <img src="<?= h($p['foto']) ?>" alt="<?= h($p['nome']) ?>" loading="lazy">
      <?php else: ?>
        <div class="sem-foto">Sem foto</div>
      <?php endif; ?>
      <h2><?= h($p['nome']) ?></h2>
      <p class="desc"><?= h($p['descricao']) ?></p>
      <span class="preco">R$ <?= number_format((float)$p['preco'], 2, ',', '.') ?></span>
    </article>
  <?php endforeach; ?>
  </div>
<?php endif; ?>
<?php include 'footer.php'; ?>