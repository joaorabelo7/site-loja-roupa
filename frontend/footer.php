</main>
<footer class="rodape">
  <div class="rodape-grade">
    <div>
      <a class="logo" href="catalogo.php">Nome da Loja</a>
      <p class="rodape-texto">Roupas pensadas para o seu dia a dia, com qualidade e estilo.</p>
    </div>
    <div>
      <h3>Navegação</h3>
      <ul>
        <li><a href="catalogo.php">Catálogo</a></li>
        <?php if (!empty($logado)): ?>
          <?php if (!empty($admin)): ?><li><a href="admin_catalogo.php">Editar catálogo</a></li><?php endif; ?>
          <li><a href="logout.php">Sair</a></li>
        <?php else: ?>
          <li><a href="login.php">Entrar</a></li>
          <li><a href="cadastro.php">Criar conta</a></li>
        <?php endif; ?>
      </ul>
    </div>
    <div>
      <h3>Contato</h3>
      <ul>
        <li><a href="mailto:contato@sualoja.com">contato@sualoja.com</a></li>
        <li><a href="tel:+5511999999999">(11) 99999-9999</a></li>
        <li><a href="https://instagram.com/sualoja" target="_blank" rel="noopener">@sualoja</a></li>
      </ul>
    </div>
  </div>
  <div class="rodape-base">
    <small>© <?= date('Y') ?> Nome da Loja. Todos os direitos reservados.</small>
  </div>
</footer>
</body>
</html>