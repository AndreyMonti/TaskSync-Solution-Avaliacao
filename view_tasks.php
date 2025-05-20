<?php
require_once 'conexao.php';

if (!isset($conn)) {
    die("Erro: Conexão com o banco de dados não foi estabelecida.");
}

try {
    $stmt = $conn->query("
        SELECT t.*, user_name AS autor
        FROM task t
        INNER JOIN users u ON t.user_id = u.user_id
        ORDER BY task_date DESC
    ");

    $task = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro ao buscar notícias: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Tasks</title>
    <link rel="stylesheet" href="view_tasks.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="icon" type="image/png" href="./img/logo/logotop.png">
    <script src="noticias.js" defer></script>
</head>

<body>
    <section id="tasks-manuais">
        <h1>Tasks cadastradas</h1>
        <div class="masonry-layout">
            <?php foreach ($task as $task): ?>
                <div class="card-task">
                    <div class="categoria <?= strtolower(str_replace(' ', '-', $task['task_category'])) ?>">
                        <?= htmlspecialchars($task['task_category']) ?>
                    </div>
                    <h3><?= htmlspecialchars($task['task_title']) ?></h3>
                    <p>
                        <?=
                            nl2br(
                                preg_replace(
                                    '/(https?:\/\/[^\s]+)/',
                                    '<a href="$1" target="_blank">$1</a>',
                                    htmlspecialchars($task['task_content'])
                                )
                            )
                            ?>
                    </p>
                    <div class="rodape-card">
                        <span class="autor"><?= htmlspecialchars($task['autor']) ?></span>
                        <span class="data"><?= date('d/m/Y H:i', strtotime($task['task_date'])) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <div class="cadastro">
        <p>Editar tasks <a href="CRUD_task.php">Editar</a></p>
    </div>

</body>
</html>