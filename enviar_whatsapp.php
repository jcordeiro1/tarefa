<?php
// Defina o telefone e a mensagem de teste
$telefone_teste = '5545999882100'; // Substitua pelo seu número de WhatsApp (somente números + 55)
$mensagem_teste = "🔔 Teste de envio WhatsApp\n\nEsta é uma mensagem de teste enviada via API Menuia.";

// Iniciar cURL
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://chatbot.menuia.com/api/create-message',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => array(
        'appkey' => '3979c836-f007-4a1e-8780-c2cb9ed08d84',
        'authkey' => 'v04k27RV4ALwrcfNBeyo2fBWWdeVbmOBJ1pwo1PZkdyfozv7b3',
        'to' => $telefone_teste,
        'message' => $mensagem_teste
    )
));
$resposta = curl_exec($curl);
$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

// Mostrar o resultado
echo "Status HTTP: " . $http_status . "<br>";
echo "Resposta da API: <pre>" . htmlspecialchars($resposta) . "</pre>";

// Opcional: Gravar no log
file_put_contents('log_teste_whatsapp.txt', date('Y-m-d H:i:s') . "\nTESTE WHATSAPP\nPara: $telefone_teste\nMensagem:\n$mensagem_teste\nResposta: $resposta\n\n", FILE_APPEND);
?>
