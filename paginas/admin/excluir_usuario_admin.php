<?php
require '../../conexao.php';

$id_usuario = $_GET['id'] ?? '';

if (!$id_usuario || !is_numeric($id_usuario)) {
    echo "ID de usuário inválido.";
    exit();
}

// Excluir o usuário
$stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
$stmt->execute([$id_usuario]);

echo "Usuário excluído com sucesso!";
?>
