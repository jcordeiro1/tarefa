<?php
require_once '../../../conexao.php';

// Receber dados
$id = $_POST['id'] ?? null;
$nome = trim($_POST['nome'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$senha = trim($_POST['senha'] ?? '');

// Validações
if (!$nome || !$telefone) {
    echo "Preencha todos os campos obrigatórios.";
    exit();
}

// Limpar máscara do telefone (salvar SEM o 55 e SEM máscara)
$telefone_numerico = preg_replace('/[^0-9]/', '', $telefone);

// Se tiver 13 dígitos começando com 55, remover o 55
if (strlen($telefone_numerico) === 13 && substr($telefone_numerico, 0, 2) === '55') {
    $telefone_numerico = substr($telefone_numerico, 2);
}

// Se for novo cadastro
if (empty($id)) {
    if (!$senha) {
        echo "Senha é obrigatória para cadastro.";
        exit();
    }

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, telefone, senha, nivel) VALUES (?, ?, ?, 'usuario')");
    $stmt->execute([$nome, $telefone_numerico, $senha_hash]);

    echo "Usuário cadastrado com sucesso!";
} else {
    // Atualizar existente
    if ($senha) {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, telefone = ?, senha = ? WHERE id = ?");
        $stmt->execute([$nome, $telefone_numerico, $senha_hash, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, telefone = ? WHERE id = ?");
        $stmt->execute([$nome, $telefone_numerico, $id]);
    }

    echo "Usuário atualizado com sucesso!";
}
?>
