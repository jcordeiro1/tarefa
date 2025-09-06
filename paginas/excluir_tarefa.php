<?php
require '../conexao.php';

$id = $_GET['id'] ?? null;
if (!$id) exit('ID não fornecido');

$stmt = $pdo->prepare("DELETE FROM tarefas WHERE id = ?");
$stmt->execute([$id]);
