<?php
require_once('../conexao.php');

date_default_timezone_set('America/Sao_Paulo');

$dataAtual = date('Y-m-d');
$horaAtual = date('H:i:s');

// DEBUG
echo "[DEBUG] Hora Atual: $horaAtual | Data Atual: $dataAtual\n";

// Consulta inteligente — agora comparando por SEGUNDOS!
$sql = "SELECT t.*, u.nome, u.telefone 
        FROM tarefas t 
        JOIN usuarios u ON t.usuario_id = u.id 
        WHERE t.data = ? 
          AND t.status = 'Agendada'
          AND t.notificado = 'Não'
          AND ABS(TIMESTAMPDIFF(SECOND, CONCAT(t.data, ' ', t.hora_alerta), NOW())) <= 120";
$stmt = $pdo->prepare($sql);
$stmt->execute([$dataAtual]);
$tarefas = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($tarefas) == 0) {
    echo "[DEBUG] Nenhuma tarefa encontrada para notificação.\n";
}

foreach ($tarefas as $tarefa) {
    $id = $tarefa['id'];
    $nomeUsuario = $tarefa['nome'];
    $telefone = '55' . preg_replace('/[^0-9]/', '', $tarefa['telefone']);
    $descricao = $tarefa['descricao'];
    $hora = date("H:i", strtotime($tarefa['hora']));
    $dataFormatada = implode('/', array_reverse(explode('-', $tarefa['data'])));

    // Monta a mensagem
    $mensagem = "🚨 *Lembrete de Tarefa!*\n\n";
    $mensagem .= "👤 *Usuário:* {$nomeUsuario}\n";
    $mensagem .= "✍️ *Descrição:* {$descricao}\n";
    $mensagem .= "📅 *Data:* {$dataFormatada}\n";
    $mensagem .= "⏰ *Hora:* {$hora}\n";

    echo "[DEBUG] Enviando WhatsApp para: $telefone | Tarefa ID: $id\n";

    // Envia a mensagem via API
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://chatbot.menuia.com/api/create-message',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => array(
            'appkey' => '3979c836-f007-4a1e-8780-c2cb9ed08d84', // seu appkey
            'authkey' => 'v04k27RV4ALwrcfNBeyo2fBWWdeVbmOBJ1pwo1PZkdyfozv7b3', // seu authkey
            'to' => $telefone,
            'message' => $mensagem
        )
    ));

    $respostaAPI = curl_exec($curl);
    curl_close($curl);

    $resposta = json_decode($respostaAPI, true);

    if ($resposta && isset($resposta['status']) && ($resposta['status'] == 'success' || $resposta['status'] == '200')) {
        // Atualiza tarefa como notificada
        $hash = $resposta['id'] ?? '';

        $sqlUpdate = "UPDATE tarefas SET notificado = 'Sim', hash = ?, data_notificacao = NOW() WHERE id = ?";
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->execute([$hash, $id]);

        echo "[DEBUG] Notificação enviada e tarefa ID $id atualizada com sucesso.\n";

        // Log de sucesso
        file_put_contents('../log_menuia.txt', date('Y-m-d H:i:s') . "\n[SUCESSO] Lembrete enviado.\nPara: $telefone\nMensagem:\n$mensagem\nResposta API: $respostaAPI\n\n", FILE_APPEND);

    } else {
        echo "[DEBUG] Falha ao notificar tarefa ID $id. Resposta da API: $respostaAPI\n";

        // Log de erro
        file_put_contents('../log_menuia.txt', date('Y-m-d H:i:s') . "\n[ERRO] Falha ao enviar lembrete.\nPara: $telefone\nMensagem:\n$mensagem\nResposta API: $respostaAPI\n\n", FILE_APPEND);
    }
}

echo "Finalizado às: " . date('H:i:s') . "\n";
?>
