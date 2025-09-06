<?php
session_start();
require '../conexao.php';

$usuario = $_SESSION['usuario_id'] ?? null;
if (!$usuario) {
    echo '<p class="text-danger">Usuário não autenticado.</p>';
    exit();
}

$query = $pdo->prepare("SELECT * FROM tarefas WHERE usuario_id = ? ORDER BY data ASC, hora ASC");
$query->execute([$usuario]);
$tarefas = $query->fetchAll(PDO::FETCH_ASSOC);

if (count($tarefas) === 0) {
    echo '<p class="text-muted">Nenhuma tarefa encontrada.</p>';
    exit();
}

// Envolvemos a tabela em table-responsive para celulares
echo "<div class='table-responsive'>";
echo "<table class='table table-bordered table-hover'>";
echo "<thead class='table-light'>";
echo "  <tr>";
echo "    <th>Descrição</th>";
echo "    <th>Data</th>";
echo "    <th>Hora</th>";
echo "    <th>Status</th>";
echo "    <th>Ações</th>";
echo "  </tr>";
echo "</thead>";
echo "<tbody>";

foreach ($tarefas as $tarefa) {
    $dataF = implode('/', array_reverse(explode('-', $tarefa['data'])));
    $horaF = date("H:i", strtotime($tarefa['hora']));
    $status = $tarefa['status'];

    $badgeClass = ($status === 'Agendada') ? 'badge bg-primary' : 'badge bg-success';

    echo "<tr>";
    echo "  <td>{$tarefa['descricao']}</td>";
    echo "  <td>{$dataF}</td>";
    echo "  <td>{$horaF}</td>";
    echo "  <td><span class='{$badgeClass}'>{$status}</span></td>";
    echo "  <td>";
    echo "    <div class='d-flex flex-wrap gap-2'>";
    if ($status === 'Agendada') {
        echo "      <button class='btn btn-sm btn-success' onclick=\"concluirTarefa({$tarefa['id']})\">Concluir</button>";
    }
    echo "      <button class='btn btn-sm btn-warning' onclick=\"abrirModalEditar({$tarefa['id']}, '{$tarefa['descricao']}', '{$tarefa['data']}', '{$tarefa['hora']}', '{$tarefa['hora_alerta']}')\">Editar</button>";
    echo "      <button class='btn btn-sm btn-danger' onclick=\"excluirTarefa({$tarefa['id']})\">Excluir</button>";
    echo "    </div>";
    echo "  </td>";
    echo "</tr>";
}

echo "</tbody>";
echo "</table>";
echo "</div>"; // FECHA o table-responsive
?>
<script>
function concluirTarefa(id) {
  if (confirm('Marcar tarefa como concluída?')) {
    fetch('../paginas/concluir_tarefa.php?id=' + id)
      .then(() => location.reload());
  }
}
function excluirTarefa(id) {
  if (confirm('Deseja excluir esta tarefa?')) {
    fetch('../paginas/excluir_tarefa.php?id=' + id)
      .then(() => location.reload());
  }
}
</script>

