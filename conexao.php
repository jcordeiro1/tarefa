<?php
header('Content-Type: text/html; charset=utf-8');

// Definir fuso horário
date_default_timezone_set('America/Sao_Paulo');

$servidor = 'localhost';
$banco = 'jcinf_tarefa';
$usuario = 'jcinf_tarefa';
$senha = 'JJacy@140277';

try {
    $pdo = new PDO("mysql:host=$servidor;dbname=$banco;charset=utf8mb4", $usuario, $senha, [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Erro na conexão com o banco de dados: ' . $e->getMessage());
}
?>
