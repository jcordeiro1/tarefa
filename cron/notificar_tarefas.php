<?php
require '../conexao.php';

date_default_timezone_set('America/Sao_Paulo');
$agora = date('H:i:s');
$dataAtual = date('Y-m-d');

echo "[DEBUG] Hora Atual: $agora | Data Atual: $dataAtual\n";

$query = $pdo->query("SELECT t.*, u.nome AS nome_usuario, u.telefone AS telefone_usuario 
  FROM tarefas t 
  JOIN usuarios u ON t.usuario_id = u.id 
  WHERE t.status = 'Agendada' 
    AND t.notificado = 'Não' 
    AND t.data = CURDATE() 
    AND t.hora <= DATE_ADD(NOW(), INTERVAL 10 MINUTE)
    LIMIT 5
    ");
$tarefas = $query->fetchAll(PDO::FETCH_ASSOC);

if (count($tarefas) == 0) {
    echo "[DEBUG] Nenhuma tarefa encontrada para notificação.\n";
}

foreach ($tarefas as $tarefa) {
    $id = $tarefa['id'];
    $titulo = $tarefa['titulo'] ?? 'Tarefa';
    $descricao = $tarefa['descricao'];
    $data = $tarefa['data'];
    $hora = $tarefa['hora'];
    $hora_alerta = $tarefa['hora_alerta'];
    $nome_usuario = $tarefa['nome_usuario'];
    $telefone_usuario = $tarefa['telefone_usuario'];

    if (empty($telefone_usuario)) {
        echo "[DEBUG] Telefone vazio para tarefa ID $id. Pulando...\n";
        continue;
    }

    $telefone_envio = '55' . preg_replace('/[ ()-]+/', '', $telefone_usuario);
    $primeiroNome = explode(' ', $nome_usuario);
    $dataF = implode('/', array_reverse(explode('-', $data)));
    $horaF = date("H:i", strtotime($hora));
    $saudacao = "Olá";

    $mensagem = "$saudacao *{$primeiroNome[0]}*\n\n";
    $mensagem .= "*Sistema de Tarefas*\n\n";
    $mensagem .= "_Lembrete de Tarefa Agendada_\n";
    $mensagem .= "*Título:* $titulo\n";
    $mensagem .= "*Hora:* $horaF\n";
    $mensagem .= "*Data:* $dataF\n";
    $mensagem .= "*Descrição:* $descricao\n";

    $mensagem = str_replace("%0A", "\n", $mensagem);
    $data_agd = $data . ' ' . $hora_alerta;

    echo "[DEBUG] Enviando para $telefone_envio | Título: $titulo | Alerta: $hora_alerta\n";

    // Enviar WhatsApp via API Menuia
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://chatbot.menuia.com/api/create-message',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => array(
            'appkey' => '3979c836-f007-4a1e-8780-c2cb9ed08d84',
            'authkey' => 'v04k27RV4ALwrcfNBeyo2fBWWdeVbmOBJ1pwo1PZkdyfozv7b3',
            'to' => $telefone_envio,
            'message' => $mensagem
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    if ($response) {
        $resposta = json_decode($response, true);
        echo json_encode($resposta);
        if (isset($resposta['status']) && ($resposta['status'] == 'success' || $resposta['status'] == '200')) {
            $hash = $resposta['id'] ?? '';
            $pdo->query("UPDATE tarefas SET notificado = 'Sim', hash = '$hash', data_notificacao = NOW() WHERE id = '$id'");
            echo "[DEBUG] Notificação enviada e tarefa ID $id atualizada.\n";
        } else {
            echo "[DEBUG] Falha ao notificar tarefa ID $id. Resposta: $response\n";
        }
    } else {
        echo "[DEBUG] Erro de conexão com a API para tarefa ID $id.\n";
    }
}

echo "Finalizado às: " . date('H:i:s') . "\n";
?>
