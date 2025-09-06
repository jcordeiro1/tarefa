<?php
session_start();
require '../conexao.php';
$usuario_id = $_SESSION['usuario_id'];

$ag = $pdo->query("SELECT COUNT(*) FROM tarefas WHERE usuario_id = $usuario_id AND status = 'Agendada'")->fetchColumn();
$co = $pdo->query("SELECT COUNT(*) FROM tarefas WHERE usuario_id = $usuario_id AND status = 'Concluída'")->fetchColumn();
$nt = $pdo->query("SELECT COUNT(*) FROM tarefas WHERE usuario_id = $usuario_id AND notificado = 'Sim'")->fetchColumn();

echo json_encode(['valores' => [$ag, $co, $nt]]);
?>
