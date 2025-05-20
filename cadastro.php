<?php
require_once 'conexao.php';
$pdo = $conn;

$mensagem = '';
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);

        if (empty($nome) || empty($email) || empty($senha)) {
            throw new Exception("Todos os campos são obrigatórios!");
        }

        $stmt = $pdo->prepare("INSERT INTO users 
                            (user_name, user_email, user_password ) 
                            VALUES (:nome, :email, :senha )");

        $stmt->execute([
            ':nome' => $nome,
            ':email' => $email,
            ':senha' => $senha,
        ]);

        if ($stmt->rowCount() > 0) {
            $sucesso = true;
            $mensagem = "Cadastro de administrador realizado com sucesso!";
        }

    } catch (PDOException $e) {
        $mensagem = "Erro no cadastro: " . $e->getMessage();
    } catch (Exception $e) {
        $mensagem = $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="./img/TaskSynch-Icon.png">
    <link rel="stylesheet" href="cadastro.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

<body>
    <?php if ($sucesso): ?>
        <script>
            Swal.fire({
                title: 'Sucesso!',
                text: '<?= $mensagem ?>',
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#B91D32'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'index.php';
                }
            });
        </script>
    <?php endif; ?>
    <div class="right">
        <img src="./img/TaskSynch-Logo.png" class="logo" alt="TaskSynch Logo">
        <h1>CADASTRE-SE</h1>
        <form method="POST" action="">
            <label>Nome completo</label>
            <input type="text" name="nome" required placeholder="Digite seu nome">

            <label>Email</label>
            <input type="email" name="email" required placeholder="Digite seu email">

            <label>Senha</label>
            <input type="password" name="senha" required placeholder="Crie uma senha">

            <button type="submit" id="botao-cadastrar">Cadastrar</button>
        </form>
    </div>

    <a href="index.php" class="back-arrow">&#8592;</a>
</body>

</html>