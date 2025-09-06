<?php
require '../conexao.php';

// Pegar dados do formulário
$nome = trim($_POST['nome'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$senha = trim($_POST['senha'] ?? '');

if (!$nome || !$telefone || !$senha) {
    echo "Preencha todos os campos.";
    exit();
}

// Limpar máscara do telefone
$telefone_numerico = preg_replace('/[^0-9]/', '', $telefone);

// Validar telefone
if (strlen($telefone_numerico) < 10 || strlen($telefone_numerico) > 11) {
    echo "Telefone invalido.";
    exit();
}

// Verificar se telefone já existe
$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE telefone = ?");
$stmt->execute([$telefone_numerico]);
if ($stmt->rowCount() > 0) {
    echo "Este WhatsApp ja esta cadastrado.";
    exit();
}

// Definir nível padrão
$nivel = 'usuario';

// Inserir usuário
$stmt = $pdo->prepare("INSERT INTO usuarios (nome, telefone, senha, nivel) VALUES (?, ?, ?, ?)");
$stmt->execute([$nome, $telefone_numerico, $senha, $nivel]);

// Saudação conforme hora
date_default_timezone_set('America/Sao_Paulo');
$horaAtual = date('H');

if ($horaAtual >= 5 && $horaAtual < 12) {
    $saudacao = "️ Bom dia";
} elseif ($horaAtual >= 12 && $horaAtual < 18) {
    $saudacao = "️ Boa tarde";
} else {
    $saudacao = " Boa noite";
}

// Mensagem de boas-vindas formatada
$mensagem .= "$saudacao, *$nome*! \n\n";
$mensagem .= " *Bem-vindo ao sistema de tarefas!*\n\n";
$mensagem .= " *Nome:* $nome\n";
$mensagem .= " *Senha:* `$senha`\n\n"; // senha entre crase
$mensagem .= " *Acesse seu painel:*\n";
$mensagem .= " https://tarefa.jc.inf.br\n\n";
$mensagem .= "_Estamos aqui para ajudar gerenciar suas tarefas!_\n";
$mensagem .= " *Equipe sistema de tarefas*";

// WhatsApp: prefixar com 55
$telefone_envio = '55' . $telefone_numerico;

// Enviar WhatsApp via API
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://chatbot.menuia.com/api/create-message',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array(
        'appkey' => '3979c836-f007-4a1e-8780-c2cb9ed08d84', // SUA APPKEY
        'authkey' => 'v04k27RV4ALwrcfNBeyo2fBWWdeVbmOBJ1pwo1PZkdyfozv7b3', // SUA AUTHKEY
        'to' => $telefone_envio,
        'message' => $mensagem
    ),
));
$response = curl_exec($curl);
curl_close($curl);

echo "Usuario cadastrado e mensagem enviada com sucesso!";
?>
