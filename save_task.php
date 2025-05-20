<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}
require_once 'conexao.php';

$conteudo = $_POST['conteudo'] ?? '';
$titulo = $_POST['titulo'] ?? '';
$categoria = $_POST['categoria'] ?? '';
$task_date = date('Y-m-d H:i:s');
$user_id = $_SESSION['user']['id'];

if (empty($conteudo) || empty($titulo) || empty($categoria)) {
    $_SESSION['flash_error'] = 'Preencha todos os campos!';
    header('Location: CRUD_task.php');
    exit();
}

try {
    $sql = "INSERT INTO task 
              (task_content, task_title, task_category, task_date, user_id ) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$conteudo, $titulo, $categoria, $task_date, $user_id]);

    $_SESSION['flash_success'] = 'Task publicada com sucesso!';
} catch (PDOException $e) {
    $_SESSION['flash_error'] = 'Erro ao publicar Task: ' . $e->getMessage();
}

header('Location: CRUD_task.php');
exit();
