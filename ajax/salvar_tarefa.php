<?php
session_start();
require '../conexao.php';

$id_usuario = $_POST['usuario_id'];
$descricao = $_POST['descricao'];
$data = $_POST['data'];
$hora = $_POST['hora'];

if (!$id_usuario || !$descricao || !$data || !$hora) {
  exit('Campos obrigatórios ausentes.');
}

// Consultar telefone do usuário
$query = $pdo->prepare("SELECT nome, telefone FROM usuarios WHERE id = ?");
$query->execute([$id_usuario]);
$usuario = $query->fetch(PDO::FETCH_ASSOC);
$nome = $usuario['nome'] ?? 'Usuário';
$telefone = preg_replace('/\D+/', '', $usuario['telefone']);

if (strlen($telefone) < 11) {
  exit('Telefone inválido para envio.');
}
$telefone = '55' . $telefone;

// Inserir tarefa no banco
$mensagem = "Olá *$nome*, você tem uma nova tarefa agendada:\n\n*Descrição:* $descricao\n*Data:* $data\n*Hora:* $hora";
$data_mensagem = date('Y-m-d H:i:s', strtotime("$data $hora -60 minutes"));

$stmt = $pdo->prepare("INSERT INTO tarefas (usuario_id, descricao, data, hora, prioridade, mensagem, status, notificado) VALUES (?, ?, ?, ?, 'Média', ?, 'Agendada', 0)");
$stmt->execute([$id_usuario, $descricao, $data, $hora, $mensagem]);

// Enviar via WhatsApp
$instancia = '3979c836-f007-4a1e-8780-c2cb9ed08d84';
$token = 'v04k27RV4ALwrcfNBeyo2fBWWdeVbmOBJ1pwo1PZkdyfozv7b3';

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
$response = curl_exec($curl);
curl_close($curl);

// Log
file_put_contents('../log_menuia.txt', date('Y-m-d H:i:s') . "\n" . print_r($response, true) . "\n\n", FILE_APPEND);

echo 'Tarefa salva e WhatsApp agendado!';
