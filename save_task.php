<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}
require_once 'conexao.php';

$titulo = $_POST['titulo'] ?? '';
$conteudo = $_POST['conteudo'] ?? '';
$categoria = $_POST['categoria'] ?? '';
$user_id = $_SESSION['user']['id'];
$task_date = date('Y-m-d H:i:s');

if (empty($titulo) || empty($conteudo) || empty($categoria)) {
    $_SESSION['flash_error'] = 'Preencha todos os campos!';
    header('Location: adm.php');
    exit();
}

try {
    $sql = "INSERT INTO task 
              (task_title, task_content, task_category, user_id, task_date) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$titulo, $conteudo, $categoria, $user_id, $task_date]);

    $_SESSION['flash_success'] = 'Task publicada com sucesso!';
} catch (PDOException $e) {
    $_SESSION['flash_error'] = 'Erro ao publicar Task: ' . $e->getMessage();
}

header('Location: CRUD_task.php');
exit();
