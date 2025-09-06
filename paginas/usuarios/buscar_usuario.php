<?php
require '../../conexao.php';

$id = $_GET['id'] ?? '';

if (!$id) {
    echo json_encode(['erro' => 'ID não informado']);
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
$stmt->execute(['id' => $id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario) {
    echo json_encode($usuario);
} else {
    echo json_encode(['erro' => 'Usuário não encontrado']);
}
?>
