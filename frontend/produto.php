<?php
require_once '../backend/conexao.php';
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM produto WHERE id_produto = ?");
$stmt->execute([$id]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$p) { header('Location: catalogo.php'); exit; }

$stmt = $pdo->prepare("SELECT tamanho, comprimento, peito, ombro, manga FROM tamanhos WHERE id_produto = ? ORDER BY id");
$stmt->execute([$id]);
$tamanhos = $stmt->fetchAll(PDO::FETCH_ASSOC);
$colunas = ['comprimento' => 'Comprimento', 'peito' => 'Peito', 'ombro' => 'Ombro', 'manga' => 'Manga'];
foreach ($colunas as $c => $_) if (!array_filter(array_column($tamanhos, $c), fn($v) => $v !== null)) unset($colunas[$c]);
$n = fn($v) => $v === null ? '—' : rtrim(rtrim(number_format((float)$v, 1, ',', ''), '0'), ',');

$titulo = $p['nome'];
include 'header.php';
?>
<p><a href="catalogo.php">← Voltar ao catálogo</a></p>
<div class="detalhe">
  <div>
    <?php if (!empty($p['foto'])): ?><img class="foto-grande" src="<?= h(foto_url($p['foto'])) ?>" alt="<?= h($p['nome']) ?>">
    <?php else: ?><div class="sem-foto">Sem foto</div><?php endif; ?>
  </div>
  <div>
    <h1><?= h($p['nome']) ?></h1>
    <p class="preco grande">R$ <?= number_format((float)$p['preco'], 2, ',', '.') ?></p>
    <p><?= nl2br(h($p['descricao'])) ?></p>

    <?php if ($tamanhos): ?>
      <p><strong>Tamanhos:</strong> <?= h(implode(' · ', array_column($tamanhos, 'tamanho'))) ?></p>
      <?php if (!empty($p['caimento'])): ?><p><strong>Caimento da peça:</strong> <?= h(ucfirst($p['caimento'])) ?></p><?php endif; ?>
      <?php if (!empty($p['modelagem'])): ?><p><strong>Modelagem:</strong> <?= h($p['modelagem']) ?></p><?php endif; ?>

      <details class="tab-medidas">
        <summary>Tabela de medidas da peça (cm)</summary>
        <div class="tabela-wrap"><table class="tabela" style="min-width:0">
          <thead><tr><th>Tam.</th><?php foreach ($colunas as $rot): ?><th><?= h($rot) ?></th><?php endforeach; ?></tr></thead>
          <tbody>
          <?php foreach ($tamanhos as $t): ?>
            <tr><td><strong><?= h($t['tamanho']) ?></strong></td><?php foreach ($colunas as $c => $_): ?><td><?= $n($t[$c]) ?></td><?php endforeach; ?></tr>
          <?php endforeach; ?>
          </tbody>
        </table></div>
      </details>

      <section class="provador">
        <h2>Qual tamanho serve em mim?</h2>
        <p class="dica">Medidas do seu corpo, em cm. Não guardamos esses dados.</p>
        <form id="formTamanho">
          <input type="hidden" name="id_produto" value="<?= (int)$p['id_produto'] ?>">
          <div class="linha3">
            <div><label for="m_peito">Peito *</label><input id="m_peito" name="peito" inputmode="decimal" required placeholder="ex.: 98"></div>
            <div><label for="m_ombro">Ombros</label><input id="m_ombro" name="ombro" inputmode="decimal" placeholder="ex.: 45"></div>
            <div><label for="m_altura">Altura</label><input id="m_altura" name="altura" inputmode="decimal" placeholder="ex.: 178"></div>
          </div>
          <p class="dica">Peito: ao redor da parte mais larga. Ombros: de uma ponta à outra, nas costas.</p>
          <label for="m_caimento">Caimento desejado</label>
          <select id="m_caimento" name="caimento">
            <option value="slim">Slim (ajustado)</option>
            <option value="regular" selected>Regular</option>
            <option value="boxy">Boxy (reto e largo)</option>
            <option value="oversized">Oversized (bem folgado)</option>
          </select>
          <label for="m_comentario">Algo que devemos saber? (opcional)</label>
          <textarea id="m_comentario" name="comentario" rows="2" maxlength="200" placeholder="ex.: prefiro mais comprida, braços longos"></textarea>
          <button class="btn" type="submit">Descobrir meu tamanho</button>
        </form>
        <div id="resultado" class="resultado" aria-live="polite"></div>
      </section>
    <?php endif; ?>
  </div>
</div>

<script>
const f = document.getElementById('formTamanho');
if (f) f.addEventListener('submit', async e => {
  e.preventDefault();
  const out = document.getElementById('resultado');
  out.textContent = 'Calculando...';
  try {
    const r = await fetch('../backend/recomendar_tamanho.php', { method: 'POST', body: new FormData(f) });
    const d = await r.json();
    if (d.ia_erro) console.warn('Gemini:', d.ia_erro);
    out.textContent = '';
    if (!d.ok) { out.textContent = d.mensagem; return; }
    const sel = document.createElement('div'); sel.className = 'selo'; sel.textContent = d.tamanho;
    const t = document.createElement('p'); t.textContent = d.texto;
    out.append(sel, t);
    if (d.ajustado) {
      const aj = document.createElement('p'); aj.className = 'dica';
      aj.textContent = 'Ajustamos o tamanho calculado (' + d.tamanho_calculado + ') considerando a modelagem da peça ou o seu comentário.';
      out.append(aj);
    }
    const alt = document.createElement('p'); alt.className = 'dica';
    alt.textContent = Object.entries(d.alternativas)
      .map(([k, v]) => k.charAt(0).toUpperCase() + k.slice(1) + ': ' + (v || '—')).join(' · ');
    out.append(alt);
  } catch (err) {
    out.textContent = 'Não foi possível calcular agora. Tente novamente.';
  }
});
</script>
<?php include 'footer.php'; ?>