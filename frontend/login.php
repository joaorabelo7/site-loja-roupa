<?php
$titulo = 'Entrar';
include 'header.php';
$mensagem = $_SESSION['mensagem'] ?? '';
unset($_SESSION['mensagem']);
?>
<div class="cartao">
  <h1>Entrar</h1>
  <?php if ($mensagem): ?><div class="aviso ok" role="status"><?= h($mensagem) ?></div><?php endif; ?>
  <form action="../backend/processa_login.php" method="post">
    <label for="email">E-mail</label>
    <input id="email" type="email" name="email" required autocomplete="email">
    <label for="senha">Senha</label>
    <input id="senha" type="password" name="senha" required autocomplete="current-password">
    <button class="btn" type="submit">Entrar</button>
  </form>
  <p class="troca">Ainda não tem conta? <a href="cadastro.php">Criar conta</a></p>
</div>
<?php include 'footer.php'; ?>
