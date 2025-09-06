<?php
require '../../conexao.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'ID inv��lido']);
    exit();
}

// Buscar o usu��rio pelo ID
$stmt = $pdo->prepare("SELECT id, nome, telefone FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    http_response_code(404);
    echo json_encode(['error' => 'Usu��rio n�0�0o encontrado']);
    exit();
}

// Devolver o JSON
echo json_encode($usuario);
?>
