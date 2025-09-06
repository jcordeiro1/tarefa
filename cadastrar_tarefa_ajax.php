<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    echo "Sessão expirada. Faça login novamente.";
    exit();
}

$titulo = $_POST['titulo'] ?? '';
$data = $_POST['data'] ?? '';
$prioridade = $_POST['prioridade'] ?? '';
$usuario_id = $_SESSION['usuario_id'];
$telefone = $_SESSION['usuario_telefone'];

if (!$titulo || !$data || !$prioridade) {
    echo "Preencha todos os campos.";
    exit();
}

$mensagem = "📋 *Nova Tarefa!*\n📝 $titulo\n📅 Data: $data\n🚨 Prioridade: $prioridade";

$stmt = $pdo->prepare("INSERT INTO tarefas (usuario_id, titulo, data, prioridade, mensagem) VALUES (:usuario_id, :titulo, :data, :prioridade, :mensagem)");
$stmt->execute([
    'usuario_id' => $usuario_id,
    'titulo' => $titulo,
    'data' => $data,
    'prioridade' => $prioridade,
    'mensagem' => $mensagem
]);

// Envio WhatsApp
$instancia = 'sua_instancia';
$token = 'seu_token';
$telefone = preg_replace('/\D/', '', $telefone);
$data_mensagem = date('Y-m-d H:i:s');

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://chatbot.menuia.com/api/create-message',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => array(
        'appkey' => $instancia,
        'authkey' => $token,
        'to' => $telefone,
        'message' => $mensagem,
        'agendamento' => $data_mensagem
    ),
));
curl_exec($curl);
curl_close($curl);

echo "ok";
