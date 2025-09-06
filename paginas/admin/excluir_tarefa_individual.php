<?php
require '../../conexao.php';

$id = $_GET['id'] ?? '';

if (!$id) {
    echo "ID inválido.";
    exit();
}

$stmt = $pdo->prepare("DELETE FROM tarefas WHERE id = ?");
$stmt->execute([$id]);

echo "Tarefa excluída com sucesso!";
?>
