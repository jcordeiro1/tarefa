<?php
require_once('../../conexao.php');

// Recebe telefone enviado via POST
$telefone = trim($_POST['telefone'] ?? '');

if (empty($telefone)) {
    echo "ERRO"; // Telefone vazio
    exit();
}

// Verifica se já existe telefone
$query = $pdo->prepare("SELECT id FROM usuarios WHERE telefone = :telefone");
$query->execute(['telefone' => $telefone]);

if ($query->rowCount() > 0) {
    echo "EXISTE"; // Telefone já cadastrado
} else {
    echo "NAO_EXISTE"; // Telefone liberado para cadastro
}
?>
