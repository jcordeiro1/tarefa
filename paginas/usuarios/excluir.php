<?php
require_once '../../../conexao.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "ID inválido.";
    exit();
}

// Verifica se o usuário existe
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo "Usuário não encontrado.";
    exit();
}

// Excluir usuário
$stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
$stmt->execute([$id]);

echo "Usuário excluído com sucesso!";
?>
