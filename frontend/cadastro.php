<?php
$titulo = 'Criar conta';
include 'header.php';
?>
<div class="cartao">
  <h1>Criar conta</h1>
  <form action="../backend/processa_cadastro.php" method="post">
    <label for="nome">Nome completo</label>
    <input id="nome" type="text" name="nome" required autocomplete="name">
    <label for="email">E-mail</label>
    <input id="email" type="email" name="email" required autocomplete="email">
    <label for="telefone">Telefone (opcional)</label>
    <input id="telefone" type="tel" name="telefone" autocomplete="tel" placeholder="(11) 99999-9999">
    <label for="senha">Senha</label>
    <input id="senha" type="password" name="senha" required minlength="6" autocomplete="new-password">
    <button class="btn" type="submit">Criar conta</button>
  </form>
  <p class="troca">Já tem conta? <a href="processa_login.php">Entrar</a></p>
</div>
<?php include 'footer.php'; ?>
