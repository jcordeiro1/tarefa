<?php
session_start();
require '../conexao.php';

$descricao = trim($_POST['descricao'] ?? '');
$data = trim($_POST['data'] ?? '');
$hora = trim($_POST['hora'] ?? '');
$hora_alerta = trim($_POST['hora_alerta'] ?? '');
$usuario = $_SESSION['usuario_id'] ?? null;

if (!$usuario || !$descricao || !$data || !$hora || !$hora_alerta) {
    echo "Dados incompletos!";
    exit();
}

try {
    $stmt = $pdo->prepare("INSERT INTO tarefas (descricao, data, hora, hora_alerta, status, usuario_id) VALUES (?, ?, ?, ?, 'Agendada', ?)");
    $stmt->execute([$descricao, $data, $hora, $hora_alerta, $usuario]);

    // Buscar dados do usuário
    $query = $pdo->prepare("SELECT nome, telefone FROM usuarios WHERE id = ?");
    $query->execute([$usuario]);
    $usuarioData = $query->fetch(PDO::FETCH_ASSOC);

    $nomeUsuario = $usuarioData['nome'];
    $telefone = '55' . preg_replace('/[^0-9]/', '', $usuarioData['telefone']);

    $dataFormatada = implode('/', array_reverse(explode('-', $data)));
    $horaFormatada = date("H:i", strtotime($hora));

    $mensagem = "✅ *Tarefa cadastrada com sucesso!*\n\n";
    $mensagem .= "👤 *Usuário:* {$nomeUsuario}\n";
    $mensagem .= "📝 *Descrição:* {$descricao}\n";
    $mensagem .= "📅 *Data:* {$dataFormatada}\n";
    $mensagem .= "⏰ *Hora:* {$horaFormatada}\n";

    // Enviar WhatsApp via Menuia API
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://chatbot.menuia.com/api/create-message',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => array(
        'appkey' => '3979c836-f007-4a1e-8780-c2cb9ed08d84',
        'authkey' => 'v04k27RV4ALwrcfNBeyo2fBWWdeVbmOBJ1pwo1PZkdyfozv7b3',
        'to' => $telefone,
        'message' => $mensagem
      )
    ));
    $resposta = curl_exec($curl);
    curl_close($curl);

    // Registrar no log
    file_put_contents('../log_menuia.txt', date('Y-m-d H:i:s') . "\nENVIO NOVA TAREFA\nPara: $telefone\nMensagem:\n$mensagem\nResposta: $resposta\n\n", FILE_APPEND);

    echo "Tarefa salva com sucesso!";
} catch (Exception $e) {
    echo "Erro ao salvar: " . $e->getMessage();
}
?>
