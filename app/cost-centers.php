<?php

// Incluir o arquivo de conexão
require 'helpers/database.php';
require 'helpers/http.php';

// Obter a conexão com o banco de dados
$pdo = getDatabaseConnection();

function saveToDatabase(\Pdo $pdo, array $record): void
{
    // Verificar se o registro já existe na tabela
    $stmt = $pdo->prepare("SELECT sienge_id FROM cost_centers WHERE sienge_id = :sienge_id");
    $stmt->execute([':sienge_id' => $record['id']]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $stmt = $pdo->prepare("
            UPDATE cost_centers 
            SET name = :name, cnpj = :cnpj, company_id = :company_id, modified_at = NOW()
            WHERE sienge_id = :sienge_id
        ");
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO cost_centers (sienge_id, name, cnpj, company_id, created_at, modified_at)
            VALUES (:sienge_id, :name, :cnpj, :company_id, NOW(), NOW())
        ");
    }

    $stmt->execute([
        ':sienge_id' => $record['id'],
        ':name' => $record['name'],
        ':cnpj' => $record['cnpj'],
        ':company_id' => $record['idCompany']
    ]);
}

$offset = 0;
$limit = 200;
$totalRecords = 0;
$totalResults = 0;

do {
    $data = fetchData('cost-centers', $offset, $limit);
    $totalRecords = $data['resultSetMetadata']['count'];
    $results = $data['results'];
    $totalResults += count($results);

    echo sprintf(
        "[%s] Importando %d registros do total de %d\n",
        date('d/m/Y H:i:s'),
        $totalResults,
        $totalRecords
    );

    // Inserir ou atualizar cada registro
    foreach ($results as $record) {
        saveToDatabase($pdo, $record);
    }

    // Atualizar o offset para a próxima página
    $offset += $limit;

} while ($offset < $totalRecords);

echo "Importação completa de Centros de Custos.\n";