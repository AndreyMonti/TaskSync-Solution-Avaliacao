<?php
require 'conexao.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD']==='GET' && isset($_GET['id'])) {
  $id = (int)$_GET['id'];
  $stmt = $conn->prepare("SELECT task_id, task_title, task_content FROM task WHERE task_id=?");
  $stmt->execute([$id]);
  echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
  exit;
}

if ($_SERVER['REQUEST_METHOD']==='POST') {
  $id      = $_POST['task_id'];
  $titulo  = $_POST['titulo'];
  $conteudo= $_POST['conteudo'];
  $upd = $conn->prepare("UPDATE task SET task_title=?, task_content=? WHERE task_id=?");
  $success = $upd->execute([$titulo,$conteudo,$id]);
  echo json_encode(['success'=>$success]);
}