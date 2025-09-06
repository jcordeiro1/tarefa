<?php
// Caminho do log
$arquivo_log = '../log_menuia.txt';

// Verificar se o arquivo existe
if (!file_exists($arquivo_log)) {
    echo "Arquivo de log não encontrado.";
    exit();
}

// Lê o conteúdo
$conteudo = file_get_contents($arquivo_log);

// Quebra as entradas por linha dupla
$entradas = explode("\n\n", trim($conteudo));

// HTML para exibir
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Log de Envios - Sistema de Tarefas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f4f4f4;
        }
        .log-entry {
            background: #fff;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
        }
        .log-entry pre {
            white-space: pre-wrap; 
            word-wrap: break-word;
        }
        h1 {
            color: #333;
        }
        .btn-clear {
            display: inline-block;
            margin-bottom: 20px;
            padding: 8px 12px;
            background: #d9534f;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-clear:hover {
            background: #c9302c;
        }
    </style>
</head>
<body>

<h1>📜 Log de Envios - Sistema de Tarefas</h1>

<a href="ver_log.php?limpar=1" class="btn-clear" onclick="return confirm('Tem certeza que deseja limpar o log?');">🧹 Limpar Log</a>

<?php
// Se o botão limpar for clicado
if (isset($_GET['limpar']) && $_GET['limpar'] == 1) {
    file_put_contents($arquivo_log, ''); // Limpa o conteúdo
    echo "<p>Log limpo com sucesso!</p>";
    exit();
}

// Mostra os registros
foreach (array_reverse($entradas) as $entrada) {
    echo '<div class="log-entry"><pre>' . htmlspecialchars($entrada) . '</pre></div>';
}
?>

</body>
</html>
