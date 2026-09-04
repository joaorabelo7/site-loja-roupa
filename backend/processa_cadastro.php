<?php

require_once 'conexao.php';
session_start();

$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';
$telefone = $_POST['telefone'] ?? '';

if (!empty($nome) && !empty($email) && !empty($senha)) {

    // Procura se o e-mail já existe
    $sql = "SELECT id FROM usuarios WHERE email = ?";
    $resultado = $pdo->prepare($sql);
    $resultado->execute([$email]);

    if ($resultado->rowCount() > 0) {
        echo "<script>
                alert('Este e-mail já está cadastrado!');
                window.history.back();
              </script>";
        exit;
    }

    // Cria o hash da senha
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // Cadastra o usuário
    $sql = "INSERT INTO usuarios (nome, email, senha, telefone)
            VALUES (?, ?, ?, ?)";

    $resultado = $pdo->prepare($sql);
    $resultado->execute([
        $nome,
        $email,
        $senhaHash,
        $telefone
    ]);

    $_SESSION['mensagem'] = "Cadastro realizado com sucesso!";

    header("Location: login.php");
    exit;

} else {

    echo "<script>
            alert('Preencha todos os campos obrigatórios!');
            window.history.back();
          </script>";
    exit;
}

?>