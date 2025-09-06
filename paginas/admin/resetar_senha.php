<?php 
require '../../conexao.php';

$id = $_POST['id'] ?? '';

if (!$id) {
    echo "ID inválido.";
    exit();
}

// Buscar o usuário
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo "Usuário não encontrado.";
    exit();
}

// Gerar nova senha aleatória
$nova_senha = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);

// Atualizar a senha (sem hash)
$stmt = $pdo->prepare("UPDATE usuarios SET senha = :senha WHERE id = :id");
$stmt->execute([
    'senha' => $nova_senha,
    'id' => $id
]);

// WhatsApp
$telefone_envio = '55' . preg_replace('/[^0-9]/', '', $usuario['telefone']);

// Saudação dinâmica
date_default_timezone_set('America/Sao_Paulo');
$horaAtual = date('H');
if ($horaAtual >= 5 && $horaAtual < 12) {
    $saudacao = "☀️ Bom dia";
} elseif ($horaAtual >= 12 && $horaAtual < 18) {
    $saudacao = "🌤️ Boa tarde";
} else {
    $saudacao = "🌙 Boa noite";
}

// Mensagem personalizada
$mensagem = "$saudacao, *{$usuario['nome']}*! 👋\n\n";
$mensagem .= "🔐 *Recuperação de Senha*\n\n";
$mensagem .= "🆕 *Nova senha:* $nova_senha\n\n";
$mensagem .= "🌐 *Acesse o sistema:*\n";
$mensagem .= "👉 https://tarefa.jc.inf.br\n\n";
$mensagem .= "📞 *Suporte:* (45) 99988-2100\n";
$mensagem .= "_Estamos prontos para ajudar você!_\n\n";
$mensagem .= "🤖 *Equipe Sistema de Tarefas*";

// **Importante**: NÃO usar utf8_encode aqui!

// Enviar via API
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://chatbot.menuia.com/api/create-message',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array(
        'appkey' => '3979c836-f007-4a1e-8780-c2cb9ed08d84',
        'authkey' => 'v04k27RV4ALwrcfNBeyo2fBWWdeVbmOBJ1pwo1PZkdyfozv7b3',
        'to' => $telefone_envio,
        'message' => $mensagem
    ),
));
$response = curl_exec($curl);
$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

// Log
file_put_contents('../../log_menuia.txt', date('Y-m-d H:i:s') . "\nRESETAR SENHA\nHTTP: $http_status\nResposta: $response\n\n", FILE_APPEND);

echo "Nova senha enviada para o WhatsApp!";
?>
