<?php
session_start();
require '../conexao.php';

$usuario_id = $_SESSION['usuario_id'] ?? null;
$id = $_POST['id'] ?? null;
$descricao = trim($_POST['descricao'] ?? '');
$data = $_POST['data'] ?? '';
$hora = $_POST['hora'] ?? '';
$hora_alerta = $_POST['hora_alerta'] ?? '';

if (!$usuario_id || !$id || !$descricao || !$data || !$hora) {
    echo "Preencha todos os campos.";
    exit();
}

// Verifica se a tarefa pertence ao usuário
$stmt = $pdo->prepare("SELECT id FROM tarefas WHERE id = ? AND usuario_id = ?");
$stmt->execute([$id, $usuario_id]);
$tarefa = $stmt->fetch();

if (!$tarefa) {
    echo "Tarefa não encontrada ou acesso negado.";
    exit();
}

// Atualiza a tarefa
$stmt = $pdo->prepare("UPDATE tarefas SET descricao = ?, data = ?, hora = ?, hora_alerta = ? WHERE id = ?");
$stmt->execute([$descricao, $data, $hora, $hora_alerta, $id]);

echo "Tarefa atualizada com sucesso!";
?>
