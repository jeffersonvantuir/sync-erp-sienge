<?php

require_once 'app.php';

function getDatabaseConnection(): \PDO
{
    $envVars = loadEnvVars();

    $host = $envVars['MYSQL_HOST'];
    $port = $envVars['MYSQL_PORT'];
    $dbname = $envVars['MYSQL_DATABASE_NAME'];
    $user = $envVars['MYSQL_USERNAME'];
    $password = $envVars['MYSQL_PASSWORD'];

    try {
        $pdo = new \PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (\PDOException $e) {
        die("Erro de conexão com o banco de dados: " . $e->getMessage());
    }
}
