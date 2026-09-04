<?php
  session_start();
  require_once 'conexao.php';

  $email = $_POST['email'] ?? '';
  $senha = $_POST['senha'] ?? '';

  try{
    // vai no banco verificar se existe esse email
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);

    // vai no banco de dados e tranforma as informações do usuário em um array associativo
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // se exister o usuario e senha estiver correta o login é feito
    if($usuario && password_verify($senha, $usuario['senha'])){

    $_SESSION['id'] = $usuario['id'];
    $_SESSION['nome'] = $usuario['nome'];
    $_SESSION['email'] = $usuario['email'];

    header("Location: index.php");
    exit;
    } else {
      echo "<script>alert('E-mail ou senha incorretos!'); window.history.back();</script>";
        exit;
    }

  }catch (PDOException $e) {
    die("Erro no login: " . $e->getMessage());
  }
?>