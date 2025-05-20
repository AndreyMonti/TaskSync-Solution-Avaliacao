<?php
session_start();

require_once "conexao.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE user_email = ? AND user_password = ?");
        $stmt->execute([$email, $senha ]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['user'] = [
                'id' => $user['user_id'],
                'nome' => $user['user_name'],
                'email' => $user['user_email'],
            ];
            header('Location: view_tasks.php');
            exit();
        } else {
            $erro = "Credenciais inválidas! Verifique email, senha e código de acesso.";
        }
    } catch (PDOException $e) {
        $erro = "Erro no login: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="./img/TaskSynch-Icon.png">
    <link rel="stylesheet" href="index.css">
</head>

<body>
    <div class="right">
        <img src="./img/TaskSynch-Logo.png" class="logo" alt="TaskSynch Logo">
        <h1>BEM VINDO AO <br><span>TaskSynch!</span></h1>
        <form method="POST" action="">
            <?php if (isset($erro)): ?>
                <div class="erro"><?= $erro ?></div>
            <?php endif; ?>

            <label>Email</label>
            <input type="email" name="email" required placeholder="Coloque seu email">

            <label>Senha</label>
            <input type="password" name="senha" required placeholder="Coloque sua senha">

            <button type="submit" id="botao-entrar">Entrar</button>
        </form>

        <div class="cadastro">
            <p>Não tem uma conta? <a href="cadastro.php">Cadastre-se</a></p>
        </div>
    </div>
</body>
</html>