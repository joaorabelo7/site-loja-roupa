<?php
session_start();
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
require_once '../backend/conexao.php';
$titulo = 'Editar catálogo';

try {
    $produtos = $pdo->query("SELECT * FROM produto ORDER BY id_produto DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $produtos = [];
    $_GET['status'] = 'erro';
}

include 'header.php';
$status = $_GET['status'] ?? '';
?>
<div class="cabecalho">
  <h1>Editar catálogo</h1>
  <button class="btn" type="button" id="btnNovo">Adicionar produto</button>
</div>

<?php if ($status === 'sucesso'): ?>
  <div class="aviso ok" role="status">Alteração salva com sucesso.</div>
<?php elseif ($status === 'erro'): ?>
  <div class="aviso erro" role="alert">Não foi possível concluir a ação. Tente novamente.</div>
<?php endif; ?>

<?php if (!$produtos): ?>
  <div class="vazio">
    <h2>Nenhum produto cadastrado</h2>
    <p>Clique em "Adicionar produto" para começar.</p>
  </div>
<?php else: ?>
<div class="tabela-wrap">
<table class="tabela">
  <thead><tr><th>Foto</th><th>Produto</th><th>Preço</th><th>Ações</th></tr></thead>
  <tbody>
  <?php foreach ($produtos as $p): ?>
    <tr>
      <td><?php if (!empty($p['foto'])): ?><img class="mini" src="<?= h($p['foto']) ?>" alt=""><?php else: ?><div class="mini sem-foto"></div><?php endif; ?></td>
      <td><strong><?= h($p['nome']) ?></strong><br><span class="desc-tab"><?= h($p['descricao']) ?></span></td>
      <td class="preco">R$ <?= number_format((float)$p['preco'], 2, ',', '.') ?></td>
      <td>
        <div class="acoes">
          <button type="button" class="btn sec peq btnEditar"
            data-id="<?= (int)$p['id_produto'] ?>"
            data-nome="<?= h($p['nome']) ?>"
            data-desc="<?= h($p['descricao']) ?>"
            data-preco="<?= h($p['preco']) ?>"
            data-foto="<?= h($p['foto']) ?>">Editar produto</button>
          <form action="../backend/excluir_produto.php" method="post" onsubmit="return confirm('Excluir &quot;<?= h($p['nome']) ?>&quot;?')">
            <input type="hidden" name="id_produto" value="<?= (int)$p['id_produto'] ?>">
            <button class="btn perigo" type="submit">Excluir</button>
          </form>
        </div>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php endif; ?>

<dialog id="modal" aria-labelledby="modalTitulo">
  <form method="post" id="formProduto">
    <h2 id="modalTitulo">Novo produto</h2>
    <input type="hidden" name="id_produto" id="f_id">
    <label for="f_nome">Nome</label>
    <input id="f_nome" name="nome_produto" required>
    <label for="f_desc">Descrição</label>
    <textarea id="f_desc" name="desc_produto" rows="3"></textarea>
    <label for="f_preco">Preço (R$)</label>
    <input id="f_preco" name="preco_produto" type="number" step="0.01" min="0" required>
    <label for="f_foto">Link da foto</label>
    <input id="f_foto" name="foto_produto" placeholder="https://... ou imagens/camiseta.jpg">
    <img class="previa" id="f_previa" alt="" hidden>
    <div class="modal-botoes">
      <button type="button" class="btn sec" id="btnCancelar">Cancelar</button>
      <button type="submit" class="btn">Salvar produto</button>
    </div>
  </form>
</dialog>

<script>
const modal = document.getElementById('modal');
const form  = document.getElementById('formProduto');
const previa = document.getElementById('f_previa');
const campos = { id:f_id, nome:f_nome, desc:f_desc, preco:f_preco, foto:f_foto };

function atualizaPrevia(){
  previa.hidden = !campos.foto.value;
  if (campos.foto.value) previa.src = campos.foto.value;
}
function abrir(dados){
  const edit = !!dados;
  modalTitulo.textContent = edit ? 'Editar produto' : 'Novo produto';
  form.action = edit ? '../backend/processa_editar.php' : '../backend/processa_adicionar.php';
  campos.id.disabled = !edit;
  campos.id.value    = edit ? dados.id : '';
  campos.nome.value  = edit ? dados.nome : '';
  campos.desc.value  = edit ? dados.desc : '';
  campos.preco.value = edit ? dados.preco : '';
  campos.foto.value  = edit ? dados.foto : '';
  atualizaPrevia();
  modal.showModal();
  campos.nome.focus();
}
btnNovo.addEventListener('click', () => abrir(null));
document.querySelectorAll('.btnEditar').forEach(b => b.addEventListener('click', () => abrir(b.dataset)));
btnCancelar.addEventListener('click', () => modal.close());
modal.addEventListener('click', e => { if (e.target === modal) modal.close(); });
campos.foto.addEventListener('input', atualizaPrevia);
</script>
<?php include 'footer.php'; ?>