<?php
require '../conexao.php';

// Pegar o telefone do formulário
$telefone = trim($_POST['telefone'] ?? '');

if (!$telefone) {
    echo "Informe seu WhatsApp.";
    exit();
}

// Limpar máscara
$telefone_numerico = preg_replace('/[^0-9]/', '', $telefone);

// Buscar usuário
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE telefone = :telefone");
$stmt->execute(['telefone' => $telefone_numerico]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo "Telefone não encontrado.";
    exit();
}

// Gerar nova senha aleatória
$nova_senha = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);

// Atualizar senha no banco (senha pura, sem hash)
$stmt = $pdo->prepare("UPDATE usuarios SET senha = :senha WHERE id = :id");
$stmt->execute([
    'senha' => $nova_senha,
    'id' => $usuario['id']
]);

// Saudação conforme hora
date_default_timezone_set('America/Sao_Paulo');
$horaAtual = date('H');

if ($horaAtual >= 5 && $horaAtual < 12) {
    $saudacao = "☀️ Bom dia";
} elseif ($horaAtual >= 12 && $horaAtual < 18) {
    $saudacao = "🌤️ Boa tarde";
} else {
    $saudacao = "🌙 Boa noite";
}

// Montar mensagem com melhor formatação
$mensagem = "*Login - Sistema de Tarefas*\n";
$mensagem .= "Transforme a eficiência do seu negócio com nosso sistema de gestão inovador...\n";
$mensagem .= "https://tarefa.jc.inf.br\n\n";

$mensagem .= "$saudacao, *{$usuario['nome']}*! 👋\n\n";
$mensagem .= "🔐 *Recuperação de Senha*\n\n";
$mensagem .= "🆕 *Nova senha:* `{$nova_senha}`\n\n"; // senha entre crases para destaque
$mensagem .= "🌐 *Acesse o sistema:*\n";
$mensagem .= "👉 https://tarefa.jc.inf.br\n\n";
$mensagem .= "📞 *Suporte:* (45) 99988-2100\n";
$mensagem .= "_Estamos prontos para ajudar você!_\n\n";
$mensagem .= "🤖 *Equipe Sistema de Tarefas*";

// WhatsApp: prefixar com 55
$telefone_envio = '55' . $telefone_numerico;

// Enviar via WhatsApp
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://chatbot.menuia.com/api/create-message',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array(
        'appkey' => '3979c836-f007-4a1e-8780-c2cb9ed08d84', // SUA APPKEY
        'authkey' => 'v04k27RV4ALwrcfNBeyo2fBWWdeVbmOBJ1pwo1PZkdyfozv7b3', // SUA AUTHKEY
        'to' => $telefone_envio,
        'message' => $mensagem
    ),
));
$response = curl_exec($curl);
curl_close($curl);

echo "Nova senha enviada no WhatsApp!";
?>
