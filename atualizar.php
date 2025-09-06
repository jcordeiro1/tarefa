<?php
require 'conexao.php';

// Define o ID do admin e a nova senha
$id_admin = 3; // ID do admin que você mostrou
$nova_senha = '12345678'; // Nova senha que o admin vai usar

// Criptografar a nova senha
$senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

// Atualizar no banco
$stmt = $pdo->prepare("UPDATE usuarios SET senha = :senha WHERE id = :id");
$stmt->execute([
    'senha' => $senha_hash,
    'id' => $id_admin
]);

echo "Senha do admin atualizada com sucesso!";
?>
