<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

require_once 'conexao.php';

try {
    $stmt = $conn->query("
        SELECT t.*, user_name AS autor
        FROM task t
        INNER JOIN users u ON t.user_id = u.user_id
        ORDER BY task_date DESC
    ");
    $tasks = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro ao buscar notícias: " . $e->getMessage());
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Tasks</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="CRUD_task.css">
    <link rel="icon" type="image/png" href="./img/logo/logotop.png">
</head>

<body>
    <main class="admin-container">
        <h1 class="welcome-message">Bem-vindo!</h1>
        <a href="#" class="action-card">
            <i class="fas fa-newspaper"></i>
            <h2>Fazer upload de Task</h2>
        </a>
    </main>

    <section id="task-admin">
        <h2>Tasks Cadastradas</h2>
        <div class="masonry-layout admin-layout">
            <?php foreach ($tasks as $task): ?>
                <div class="card-task">
                    <div class="categoria <?= strtolower(str_replace(' ', '-', $task['task_category'])) ?>">
                        <?= htmlspecialchars($task['task_category']) ?>
                    </div>
                    <h3><?= htmlspecialchars($task['task_title']) ?></h3>
                    <p><?= nl2br(htmlspecialchars($task['task_content'])) ?></p>
                    <div class="rodape-card">
                        <span class="autor"><?= htmlspecialchars($task['autor']) ?></span>
                        <span class="data"><?= date('d/m/Y H:i', strtotime($task['task_date'])) ?></span>
                    </div>

                    <button class="btn-edit-task" data-id="<?= $task['task_id'] ?>">
                        <i class="fas fa-edit"></i>
                    </button>

                    <button class="btn-delete" data-id="<?= $task['task_id'] ?>">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <a href="view_tasks.php" class="back-arrow">&#8592;</a>

    <div id="modal-task" class="modal">
        <div class="modal-conteudo">
            <span class="fechar">&times;</span>
            <h2>Publicar Nova Task</h2>
            <form method="POST" action="save_task.php" id="form-task">
                <div class="form-group">
                    <label>Título:</label>
                    <input type="text" name="titulo" required>
                </div>
                <div class="form-group">
                    <label>Conteúdo:</label>
                    <textarea name="conteudo" rows="5" required></textarea>
                </div>
                <div class="form-group">
                    <label>Categoria:</label>
                    <select name="categoria" required>
                        <option value="A Fazer">A Fazer</option>
                        <option value="Fazendo">Fazendo</option>
                        <option value="Concluído">Concluído</option>
                    </select>
                </div>
                <div style="text-align:right; margin-top:10px;">
                    <button type="submit" class="btn-publicar">Publicar Task</button>
                </div>
            </form>
        </div>
    </div>
    <div id="modal-edit-task" class="modal">
        <div class="modal-conteudo">
            <span class="fechar">&times;</span>
            <h2>Editar Task</h2>
            <form id="form-edit-task">
                <input type="hidden" name="task_id" id="edit-task-id">
                <div class="form-group">
                    <label>Título</label>
                    <input type="text" name="titulo" id="edit-task-titulo" required>
                </div>
                <div class="form-group">
                    <label>Conteúdo</label>
                    <textarea name="conteudo" id="edit-task-conteudo" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn-publicar">Salvar</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php
    $flashSuccess = $_SESSION['flash_success'] ?? null;
    $flashError = $_SESSION['flash_error'] ?? null;
    unset($_SESSION['flash_success'], $_SESSION['flash_error']);
    ?>
    <?php if ($flashSuccess): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: '<?= addslashes($flashSuccess) ?>',
                showConfirmButton: false,
                timer: 2000
            });
        </script>
    <?php endif; ?>
    <?php if ($flashError): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: '<?= addslashes($flashError) ?>'
            });
        </script>
    <?php endif; ?>

    <script>
        const modalTask = document.getElementById('modal-task');
        const btnTask = document.querySelector('.fa-newspaper').closest('a');

        function setupModal(btn, modal) {
            const span = modal.getElementsByClassName("fechar")[0];

            btn.onclick = function (e) {
                e.preventDefault();
                modal.style.display = "block";
            }

            span.onclick = function () {
                modal.style.display = "none";
            }

            window.onclick = function (event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        }

        setupModal(btnTask, modalTask);

        function setupDelete(buttonClass, deleteUrl, itemType) {
            document.querySelectorAll(buttonClass).forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.id;
                    Swal.fire({
                        title: `Tem certeza?`,
                        text: `Este ${itemType} será excluído permanentemente.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sim, excluir',
                        cancelButtonText: 'Não, cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(deleteUrl, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                body: `task_id=${encodeURIComponent(id)}`
                            })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire('Excluído!', `O ${itemType} foi removido com sucesso.`, 'success')
                                            .then(() => location.reload());
                                    } else {
                                        Swal.fire('Erro', `Não foi possível excluir o ${itemType}.`, 'error');
                                    }
                                })
                                .catch(() => {
                                    Swal.fire('Erro', 'Falha na comunicação com o servidor.', 'error');
                                });
                        }
                    });
                });
            });
        }

        setupDelete('.btn-delete', 'delete_task.php', 'task');

        function setupEdit(buttonSelector, modalId, formId, fetchUrl, fieldsMapper) {
            const modal = document.getElementById(modalId);
            const form = document.getElementById(formId);
            const close = modal.querySelector('.fechar');

            close.onclick = () => modal.style.display = 'none';
            window.onclick = e => { if (e.target == modal) modal.style.display = 'none'; };

            document.querySelectorAll(buttonSelector).forEach(btn => {
                btn.onclick = e => {
                    e.preventDefault();
                    const id = btn.dataset.id;
                    fetch(fetchUrl + '?id=' + id)
                        .then(r => r.json())
                        .then(data => {
                            fieldsMapper(data);
                            modal.style.display = 'block';
                        });
                };
            });

            form.onsubmit = e => {
                e.preventDefault();
                const formData = new FormData(form);
                fetch(fetchUrl, {
                    method: 'POST',
                    body: formData
                })
                    .then(r => r.json())
                    .then(resp => {
                        if (resp.success) {
                            Swal.fire({
                                icon: 'success',
                                title: resp.message || 'Atualizado com sucesso!',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        } else {
                            Swal.fire('Erro', resp.message || 'Não foi possível atualizar.', 'error');
                        }
                    });
            };
        }

        setupEdit(
            '.btn-edit-task',
            'modal-edit-task',
            'form-edit-task',
            'edit_task.php',
            data => {
                document.getElementById('edit-task-id').value = data.task_id;
                document.getElementById('edit-task-titulo').value = data.titulo;
                document.getElementById('edit-task-conteudo').value = data.conteudo;
            }
        );
    </script>
</body>

</html>