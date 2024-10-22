<?php

require_once 'app.php';

function fetchData(string $path, int $offset = 0, int $limit = 200): array
{
    $envVar = loadEnvVars();
    $baseUrl = $envVar['SIENGE_ERP_BASE_URL'];
    $username = $envVar['SIENGE_ERP_USERNAME'];
    $password = $envVar['SIENGE_ERP_PASSWORD'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$baseUrl/{$path}?offset=$offset&limit=$limit");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, "{$username}:{$password}");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        die('Erro na requisição: ' . curl_error($ch));
    }

    curl_close($ch);
    return json_decode($response, true);
}
