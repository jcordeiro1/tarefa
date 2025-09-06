<?php
session_start();
require '../conexao.php';

$telefone = $_POST['telefone'] ?? '';
$senha = $_POST['senha'] ?? '';

if (!$telefone || !$senha) {
    echo "Preencha todos os campos.";
    exit();
}

$telefone_numerico = preg_replace('/[^0-9]/', '', $telefone);

// Buscar e validar senha pura
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE telefone = :telefone AND senha = :senha");
$stmt->execute([
    'telefone' => $telefone_numerico,
    'senha' => $senha
]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario) {
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    $_SESSION['usuario_telefone'] = $usuario['telefone'];
    $_SESSION['usuario_nivel'] = $usuario['nivel'];
    echo "ok";
} else {
    echo "Telefone ou senha inválidos.";
}
?>
