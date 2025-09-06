<?php
session_start();
require '../conexao.php';

$titulo = $_POST['titulo'] ?? '';
$descricao = $_POST['descricao'] ?? '';
data_default_timezone_set('America/Sao_Paulo');
$data = $_POST['data'] ?? date('Y-m-d');
$hora = $_POST['hora'] ?? '08:00';
$usuario_id = $_SESSION['usuario_id'];
$telefone = $_SESSION['usuario_telefone'];

$pdo->prepare("INSERT INTO tarefas (usuario_id, titulo, descricao, data, hora, prioridade, status, notificado) VALUES (?, ?, ?, ?, ?, 'Média', 'Agendada', 0)")
    ->execute([$usuario_id, $titulo, $descricao, $data, $hora]);

// API Menuia
$mensagem = "Olá, você tem uma tarefa: $titulo\nData: $data às $hora\n$descricao";
$mensagem = str_replace("\n", "%0A", $mensagem);
$instancia = 'SUA_APPKEY';
$token = 'SUA_AUTHKEY';

$curl = curl_init();
curl_setopt_array($curl, [
  CURLOPT_URL => 'https://chatbot.menuia.com/api/create-message',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => [
    'appkey' => $instancia,
    'authkey' => $token,
    'to' => $telefone,
    'message' => $mensagem
  ],
]);
$response = curl_exec($curl);
curl_close($curl);

echo 'Tarefa cadastrada e mensagem enviada!';
?>