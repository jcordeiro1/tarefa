<?php
require '../conexao.php';

$id = $_GET['id'] ?? null;
if (!$id) exit('ID não fornecido');

$stmt = $pdo->prepare("UPDATE tarefas SET status = 'Concluída' WHERE id = ?");
$stmt->execute([$id]);
