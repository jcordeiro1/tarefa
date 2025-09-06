<?php
require 'conexao.php';

// Configurar sua instância e token da API Menuia
$instancia = 'sua_instancia';
$token = 'seu_token';

// Buscar tarefas com status "Agendada" e que vão ocorrer em 60 minutos
$stmt = $pdo->prepare("
    SELECT t.id, t.titulo, t.data, t.hora, t.mensagem, t.usuario_id, u.telefone
    FROM tarefas t
    INNER JOIN usuarios u ON u.id = t.usuario_id
    WHERE t.status = 'Agendada'
    AND CONCAT(t.data, ' ', t.hora) BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 61 MINUTE)
    AND t.notificado IS NULL
");
$stmt->execute();
$tarefas = $stmt->fetchAll();

foreach ($tarefas as $tarefa) {
    $mensagem = "⏰ *Lembrete de Tarefa!* ⏰\n" .
                "📝 *{$tarefa['titulo']}*\n" .
                "📅 *Hoje às {$tarefa['hora']}*\n\n" .
                "Prepare-se!";

    $telefone = preg_replace('/\D/', '', $tarefa['telefone']);
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

    // Marcar como notificado para não enviar novamente
    $update = $pdo->prepare("UPDATE tarefas SET notificado = 1 WHERE id = :id");
    $update->execute(['id' => $tarefa['id']]);
}
