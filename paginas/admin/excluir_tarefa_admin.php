<?php
require '../../conexao.php';

$id = $_GET['id'] ?? '';

if (!$id || !is_numeric($id)) {
    echo "ID inválido.";
    exit();
}

// Excluir apenas as tarefas do usuário
$stmt = $pdo->prepare("DELETE FROM tarefas WHERE usuario_id = ?");
$stmt->execute([$id]);

echo "Todas as tarefas do usuário foram excluídas com sucesso!";
?>
