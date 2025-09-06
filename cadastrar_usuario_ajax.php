<?php
require 'conexao.php';

$nome = $_POST['nome'] ?? '';
$senha = $_POST['senha'] ?? '';
$telefone = $_POST['telefone'] ?? '';

if (empty($nome) || empty($senha) || empty($telefone)) {
    echo "Todos os campos são obrigatórios.";
    exit();
}

// Verificar se já existe
$check = $pdo->prepare("SELECT id FROM usuarios WHERE nome = :nome");
$check->execute(['nome' => $nome]);
if ($check->rowCount() > 0) {
    echo "Esse nome já está em uso.";
    exit();
}

// Salvar no banco
$stmt = $pdo->prepare("INSERT INTO usuarios (nome, senha, telefone) VALUES (:nome, :senha, :telefone)");
$stmt->execute([
    'nome' => $nome,
    'senha' => $senha,
    'telefone' => $telefone
]);

// Enviar via WhatsApp pela API Menuia
$mensagem = "👋 Olá $nome!\n📲 Seu cadastro na Gestão de Tarefas foi concluído com sucesso!\nAgora você pode fazer login e começar a usar.";
$mensagem = str_replace("%0A", "\n", $mensagem);
$telefone_formatado = preg_replace('/\D/', '', $telefone);

$instancia = 'sua_instancia';
$token = 'seu_token';
$data_mensagem = date('Y-m-d H:i:s');

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://chatbot.menuia.com/api/create-message',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array(
        'appkey' => '3979c836-f007-4a1e-8780-c2cb9ed08d84', // seu appkey
        'authkey' => 'v04k27RV4ALwrcfNBeyo2fBWWdeVbmOBJ1pwo1PZkdyfozv7b3', // seu authkey
        'to' => $telefone_formatado,
        'message' => $mensagem,
        'agendamento' => $data_mensagem
    ),
));
$response = curl_exec($curl);
curl_close($curl);

echo "ok";
