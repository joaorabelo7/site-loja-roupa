<?php
session_start();
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
require_once '../backend/conexao.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT nome, modelagem FROM produto WHERE id_produto = ?");
$stmt->execute([$id]);
$prod = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$prod) { header('Location: admin_catalogo.php'); exit; }

$stmt = $pdo->prepare("SELECT tamanho, comprimento, peito, ombro, manga FROM tamanhos WHERE id_produto = ? ORDER BY id");
$stmt->execute([$id]);
$linhas = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!$linhas) foreach (['XS','S','M','L','XL','XXL'] as $t) $linhas[] = ['tamanho'=>$t,'comprimento'=>'','peito'=>'','ombro'=>'','manga'=>''];

$titulo = 'Tamanhos';
include 'header.php';
$status = $_GET['status'] ?? '';
$n = fn($v) => $v === null || $v === '' ? '' : rtrim(rtrim(number_format((float)$v, 1, '.', ''), '0'), '.');
?>
<div class="cabecalho">
  <div><h1>Tamanhos</h1><p class="dica"><?= h($prod['nome']) ?></p></div>
  <a class="btn sec" href="admin_catalogo.php">Voltar ao painel</a>
</div>
<?php if ($status === 'sucesso'): ?><div class="aviso ok" role="status">Tabela salva.</div>
<?php elseif ($status === 'erro'): ?><div class="aviso erro" role="alert">Não foi possível salvar. Tente novamente.</div><?php endif; ?>

<form action="../backend/processa_tamanhos.php" method="post">
  <input type="hidden" name="id_produto" value="<?= $id ?>">
  <label for="modelagem">Observação de modelagem (opcional)</label>
  <input id="modelagem" name="modelagem" maxlength="255" value="<?= h($prod['modelagem'] ?? '') ?>" placeholder="ex.: modelagem ampla, veste um número maior; tecido com 5% de elastano">
  <p class="dica">A IA usa esta observação para ajustar o tamanho. Também é mostrada ao cliente.</p>

  <p class="dica" style="margin-top:20px">Medidas da <strong>peça</strong>, em cm. Peito é a circunferência (largura deitada × 2); ombro é de costura a costura. O cálculo usa <strong>peito</strong> e <strong>ombro</strong>; comprimento e manga aparecem para o cliente e a IA os considera.</p>
  <div class="tabela-wrap">
  <table class="tabela" id="tabTam">
    <thead><tr><th>Tamanho</th><th>Comprimento</th><th>Peito</th><th>Ombro</th><th>Manga</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($linhas as $l): ?>
      <tr>
        <td><input name="tamanho[]" maxlength="10" value="<?= h($l['tamanho']) ?>" aria-label="Tamanho"></td>
        <td><input name="comprimento[]" inputmode="decimal" value="<?= h($n($l['comprimento'])) ?>" aria-label="Comprimento"></td>
        <td><input name="peito[]" inputmode="decimal" value="<?= h($n($l['peito'])) ?>" aria-label="Peito"></td>
        <td><input name="ombro[]" inputmode="decimal" value="<?= h($n($l['ombro'])) ?>" aria-label="Ombro"></td>
        <td><input name="manga[]" inputmode="decimal" value="<?= h($n($l['manga'])) ?>" aria-label="Manga"></td>
        <td><button type="button" class="btn perigo remover">Remover</button></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  <div class="modal-botoes" style="justify-content:space-between;flex-wrap:wrap">
    <div style="display:flex;gap:12px;flex-wrap:wrap">
      <button type="button" class="btn sec" id="addLinha">Adicionar tamanho</button>
      <button type="button" class="btn sec" id="exemplo">Preencher com tabela de exemplo</button>
    </div>
    <button type="submit" class="btn">Salvar tabela</button>
  </div>
</form>
<script>
const corpo = document.querySelector('#tabTam tbody');
const modelo = corpo.rows[0].cloneNode(true);
function novaLinha(v = []) {
  const tr = modelo.cloneNode(true);
  [...tr.querySelectorAll('input')].forEach((i, k) => i.value = v[k] ?? '');
  corpo.appendChild(tr);
}
document.getElementById('addLinha').addEventListener('click', () => novaLinha());
document.getElementById('exemplo').addEventListener('click', () => {
  if (!confirm('Substituir a tabela atual pela de exemplo (XS a XXL)?')) return;
  corpo.innerHTML = '';
  // tamanho, comprimento, peito, ombro, manga
  [['XS',70,96,44,18],['S',72,100,46,19],['M',74,104,48,20],['L',76,108,50,21],['XL',78,112,52,22],['XXL',80,116,54,23]].forEach(novaLinha);
});
corpo.addEventListener('click', e => {
  if (e.target.classList.contains('remover') && corpo.rows.length > 1) e.target.closest('tr').remove();
});
</script>
<?php include 'footer.php'; ?>