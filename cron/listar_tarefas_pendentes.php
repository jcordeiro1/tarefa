<?php
require '../conexao.php'; // Ajusta o caminho conforme sua estrutura

date_default_timezone_set('America/Sao_Paulo');

echo "<h2>Tarefas Pendentes de Notificação</h2>";

$query = $pdo->query("
    SELECT 
        id, 
        titulo, 
        data, 
        hora_alerta, 
        status, 
        notificado, 
        descricao 
    FROM 
        tarefas 
    WHERE 
        data = CURDATE() 
        AND status = 'Agendada' 
        AND notificado = 'Não'
    ORDER BY hora_alerta ASC
");

$tarefas = $query->fetchAll(PDO::FETCH_ASSOC);

if (count($tarefas) == 0) {
    echo "<p><strong>Nenhuma tarefa pendente.</strong></p>";
} else {
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr>
            <th>ID</th>
            <th>Título</th>
            <th>Data</th>
            <th>Hora Alerta</th>
            <th>Status</th>
            <th>Notificado</th>
            <th>Descrição</th>
          </tr>";
    foreach ($tarefas as $tarefa) {
        echo "<tr>";
        echo "<td>{$tarefa['id']}</td>";
        echo "<td>{$tarefa['titulo']}</td>";
        echo "<td>{$tarefa['data']}</td>";
        echo "<td>{$tarefa['hora_alerta']}</td>";
        echo "<td>{$tarefa['status']}</td>";
        echo "<td>{$tarefa['notificado']}</td>";
        echo "<td>{$tarefa['descricao']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<br><p>Hora Atual do Servidor: <strong>" . date('H:i:s') . "</strong></p>";
?>
