<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$status = $_GET['status'] ?? 'todas';
$usuario_id = $_SESSION['usuario_id'];

$cond = '';
if ($status == 'agendada') $cond = "AND status = 'Agendada'";
elseif ($status == 'concluida') $cond = "AND status = 'Concluída'";
elseif ($status == 'notificada') $cond = "AND notificado = 1";

$stmt = $pdo->prepare("SELECT * FROM tarefas WHERE usuario_id = :usuario_id $cond ORDER BY data, hora");
$stmt->execute(['usuario_id' => $usuario_id]);
$tarefas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel de Tarefas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Minhas Tarefas</h1>
    <nav>
        <a href="?status=todas">Todas</a> |
        <a href="?status=agendada">Agendadas</a> |
        <a href="?status=concluida">Concluídas</a> |
        <a href="?status=notificada">Notificadas</a>
    </nav>

    <table border="1" cellpadding="10" cellspacing="0" style="margin-top: 20px; background: white;">
        <thead>
            <tr>
                <th>Título</th>
                <th>Data</th>
                <th>Hora</th>
                <th>Prioridade</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tarefas as $t): ?>
                <tr>
                    <td><?= htmlspecialchars($t['titulo']) ?></td>
                    <td><?= $t['data'] ?></td>
                    <td><?= $t['hora'] ?></td>
                    <td><?= $t['prioridade'] ?></td>
                    <td><?= $t['status'] ?></td>
                    <td>
                        <?php if ($t['status'] == 'Agendada'): ?>
                            <a href="concluir_tarefa.php?id=<?= $t['id'] ?>">Concluir</a>
                        <?php endif; ?>
                        <a href="excluir_tarefa.php?id=<?= $t['id'] ?>" onclick="return confirm('Excluir tarefa?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
