<?php
require '../conexao.php'; // Ajuste o caminho se necessário

date_default_timezone_set('America/Sao_Paulo');

// ID da tarefa que você quer resetar (ou comente essa linha e reseta todas não notificadas de hoje)
$id_tarefa = $_GET['id'] ?? null;

if ($id_tarefa) {
    $pdo->query("UPDATE tarefas SET notificado = 'Não', hash = NULL WHERE id = '$id_tarefa'");
    echo "Notificação da tarefa ID {$id_tarefa} resetada com sucesso!";
} else {
    $pdo->query("UPDATE tarefas SET notificado = 'Não', hash = NULL WHERE data = CURDATE()");
    echo "Todas as notificações de hoje foram resetadas com sucesso!";
}
?>
