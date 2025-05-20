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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Painel Administrativo - Tasks</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="CRUD_task.css" />
  <link rel="icon" type="image/png" href="./img/TaskSynch-Icon.png" />
</head>

<body>
  <!-- Seção exclusiva para o card de fazer upload -->
  <section id="upload-task-section">
    <div class="action-card action-card-full" id="btn-open-publish">
      <i class="fas fa-newspaper"></i>
      <h2>Fazer upload de Task</h2>
    </div>
  </section>

  <!-- Seção com o restante do conteúdo (lista de tasks) -->
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

  <!-- Modal de Publicar Nova Task -->
  <div id="modal-task" class="modal">
    <div class="modal-conteudo">
      <span class="fechar" id="close-publish">&times;</span>
      <h2>Publicar Nova Task</h2>
      <form method="POST" action="save_task.php" id="form-task">
        <div class="form-group">
          <label>Título:</label>
          <input type="text" name="titulo" required />
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

  <!-- Modal de Editar Task -->
  <div id="modal-edit-task" class="modal">
    <div class="modal-conteudo">
      <span class="fechar" id="close-edit">&times;</span>
      <h2>Editar Task</h2>
      <form id="form-edit-task">
        <input type="hidden" name="task_id" id="edit-task-id" />
        <div class="form-group">
          <label>Título:</label>
          <input type="text" name="titulo" id="edit-task-titulo" required />
        </div>
        <div class="form-group">
          <label>Conteúdo:</label>
          <textarea name="conteudo" id="edit-task-conteudo" rows="4" required></textarea>
        </div>
        <div class="form-group">
          <label>Categoria:</label>
          <select name="categoria" id="edit-task-categoria" required>
            <option value="A Fazer">A Fazer</option>
            <option value="Fazendo">Fazendo</option>
            <option value="Concluído">Concluído</option>
          </select>
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
    // --- Controles do Modal de Publicar Task ---
    const btnOpenPublish = document.getElementById('btn-open-publish');
    const modalPublish = document.getElementById('modal-task');
    const btnClosePublish = document.getElementById('close-publish');

    btnOpenPublish.addEventListener('click', function(e) {
      e.preventDefault();
      modalPublish.style.display = 'flex';
    });
    btnClosePublish.addEventListener('click', function() {
      modalPublish.style.display = 'none';
    });
    window.addEventListener('click', function(event) {
      if (event.target === modalPublish) {
        modalPublish.style.display = 'none';
      }
    });

    // --- Controles do Modal de Editar Task ---
    const modalEdit = document.getElementById('modal-edit-task');
    const btnCloseEdit = document.getElementById('close-edit');

    function openEditModal(taskId) {
      fetch('edit_task.php?id=' + encodeURIComponent(taskId))
        .then(response => response.json())
        .then(data => {
          document.getElementById('edit-task-id').value = data.task_id;
          document.getElementById('edit-task-titulo').value = data.titulo;
          document.getElementById('edit-task-conteudo').value = data.conteudo;
          document.getElementById('edit-task-categoria').value = data.categoria;
          modalEdit.style.display = 'flex';
        })
        .catch(() => {
          Swal.fire('Erro', 'Não foi possível carregar os dados da task.', 'error');
        });
    }

    document.querySelectorAll('.btn-edit-task').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        openEditModal(this.dataset.id);
      });
    });
    btnCloseEdit.addEventListener('click', function() {
      modalEdit.style.display = 'none';
    });
    window.addEventListener('click', function(event) {
      if (event.target === modalEdit) {
        modalEdit.style.display = 'none';
      }
    });

    document.getElementById('form-edit-task').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      fetch('edit_task.php', {
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
        })
        .catch(() => {
          Swal.fire('Erro', 'Falha na comunicação com o servidor.', 'error');
        });
    });

    // --- Exclusão de Task ---
    document.querySelectorAll('.btn-delete').forEach(btn => {
      btn.addEventListener('click', function() {
        const id = this.dataset.id;
        Swal.fire({
          title: `Tem certeza?`,
          text: `Esta task será excluída permanentemente.`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Sim, excluir',
          cancelButtonText: 'Não, cancelar'
        }).then((result) => {
          if (result.isConfirmed) {
            fetch('delete_task.php', {
              method: 'POST',
              headers: {'Content-Type': 'application/x-www-form-urlencoded'},
              body: `task_id=${encodeURIComponent(id)}`
            })
              .then(res => res.json())
              .then(data => {
                if (data.success) {
                  Swal.fire('Excluído!', 'A task foi removida com sucesso.', 'success')
                    .then(() => location.reload());
                } else {
                  Swal.fire('Erro', 'Não foi possível excluir a task.', 'error');
                }
              })
              .catch(() => {
                Swal.fire('Erro', 'Falha na comunicação com o servidor.', 'error');
              });
          }
        });
      });
    });
  </script>
</body>

</html>
