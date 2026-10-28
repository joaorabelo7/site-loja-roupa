<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$logado = isset($_SESSION['id']);
$admin  = !empty($_SESSION['admin']);
$titulo = $titulo ?? 'Catálogo';
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($titulo) ?> | Nome da Loja</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@600;800&family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>
<header class="topo">
  <a class="logo" href="catalogo.php">Nome da Loja</a>
  <nav>
    <a href="catalogo.php">Catálogo</a>
    <?php if ($logado): ?>
      <span>Olá, <?= h(explode(' ', $_SESSION['nome'])[0]) ?></span>
      <a href="logout.php">Sair</a>
    <?php else: ?>
      <a href="login.php">Entrar</a>
      <a class="btn sec peq" href="cadastro.php">Criar conta</a>
    <?php endif; ?>
  </nav>
</header>
<main>