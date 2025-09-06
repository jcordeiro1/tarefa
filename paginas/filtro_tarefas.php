<?php
session_start();
require '../conexao.php';

$usuario = $_SESSION['usuario_id'] ?? null;
if (!$usuario) {
    echo '<p class="text-danger">Usuário não autenticado.</p>';
    exit();
}

$status = $_GET['status'] ?? 'Todas';
$dataFiltro = $_GET['data'] ?? '';

$sql = "SELECT * FROM tarefas WHERE usuario_id = ?";
$params = [$usuario];

if ($status !== 'Todas') {
    $sql .= " AND status = ?";
    $params[] = $status;
}
if (!empty($dataFiltro)) {
    $sql .= " AND data = ?";
    $params[] = $dataFiltro;
}

$sql .= " ORDER BY data ASC, hora ASC";
$query = $pdo->prepare($sql);
$query->execute($params);
$tarefas = $query->fetchAll(PDO::FETCH_ASSOC);

if (count($tarefas) === 0) {
    echo '<p class="text-muted">Nenhuma tarefa encontrada com esse filtro.</p>';
    exit();
}

foreach ($tarefas as $tarefa) {
    $dataF = implode('/', array_reverse(explode('-', $tarefa['data'])));
    $horaF = date("H:i", strtotime($tarefa['hora']));
    $status = $tarefa['status'];
    $classe = $status === 'Agendada' ? 'border-primary' : 'border-success';

    echo "<div class='card mb-3 border $classe'>";
    echo "  <div class='card-body'>";
    echo "    <h5 class='card-title'>{$tarefa['descricao']}</h5>";
    echo "    <p class='card-text'><strong>Data:</strong> {$dataF} <strong>Hora:</strong> {$horaF}</p>";
    echo "    <p class='card-text'><strong>Status:</strong> {$status}</p>";

    if ($status === 'Agendada') {
        echo "    <button class='btn btn-sm btn-success me-2' onclick=\"concluirTarefa({$tarefa['id']})\">Concluir</button>";
    }
    echo "    <button class='btn btn-sm btn-danger' onclick=\"excluirTarefa({$tarefa['id']})\">Excluir</button>";
    echo "  </div>";
    echo "</div>";
}
?>
