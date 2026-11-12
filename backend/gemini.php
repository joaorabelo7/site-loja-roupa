<?php
require_once 'config.php';

// Retorna o texto gerado pelo Gemini, ou null em caso de falha (o motivo vai em $erro).
function gemini_texto(string $sistema, string $usuario, ?string &$erro = null, bool $json = false): ?string {
    if (GEMINI_API_KEY === '' || GEMINI_API_KEY === 'COLE_SUA_CHAVE_AQUI') { $erro = 'Chave do Gemini não configurada em config.php'; return null; }
    if (!function_exists('curl_init')) { $erro = 'Extensão curl do PHP desativada'; return null; }

    $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . GEMINI_MODEL . ':generateContent';
    $corpo = json_encode([
        'system_instruction' => ['parts' => [['text' => $sistema]]],
        'contents'           => [['role' => 'user', 'parts' => [['text' => $usuario]]]],
        'generationConfig'   => array_merge(['temperature' => 0.3, 'maxOutputTokens' => 1024],
                                            $json ? ['responseMimeType' => 'application/json'] : []),
    ], JSON_UNESCAPED_UNICODE);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'x-goog-api-key: ' . GEMINI_API_KEY],
        CURLOPT_POSTFIELDS     => $corpo,
    ]);
    $res = curl_exec($ch);
    if ($res === false) { $erro = curl_error($ch); curl_close($ch); return null; }
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $dados = json_decode($res, true);
    if ($http !== 200) { $erro = "HTTP $http: " . ($dados['error']['message'] ?? 'erro desconhecido'); return null; }

    $texto = '';
    foreach ($dados['candidates'][0]['content']['parts'] ?? [] as $parte) {
        if (isset($parte['text']) && empty($parte['thought'])) $texto .= $parte['text'];
    }
    $texto = trim($texto);
    if ($texto === '') { $erro = 'Resposta vazia do modelo'; return null; }
    return $texto;
}