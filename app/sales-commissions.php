<?php

// Incluir o arquivo de conexão
require 'helpers/database.php';
require 'helpers/http.php';

// Obter a conexão com o banco de dados
$pdo = getDatabaseConnection();

function saveSalesCommission(\PDO $pdo, array $record): void
{
    // Verificar se a comissão já existe na tabela
    $stmt = $pdo->prepare("SELECT id FROM sales_commissions WHERE id = :id");
    $stmt->execute([':id' => $record['id']]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Atualizar comissão existente
        $stmt = $pdo->prepare("
            UPDATE sales_commissions
            SET contract_id = :contract_id, broker_id = :broker_id, customer_id = :customer_id, 
                installments_number = :installments_number, bill_id = :bill_id, rate = :rate, 
                value = :value, base_value = :base_value, type = :type
            WHERE id = :id
        ");
    } else {
        // Inserir nova comissão
        $stmt = $pdo->prepare("
            INSERT INTO sales_commissions (
                id, contract_id, broker_id, customer_id, installments_number, bill_id, rate, 
                value, base_value, type
            ) VALUES (
                :id, :contract_id, :broker_id, :customer_id, :installments_number, :bill_id, :rate, 
                :value, :base_value, :type
            )
        ");
    }

    // Executar a query (insert ou update)
    $stmt->execute([
        ':id' => $record['id'],
        ':contract_id' => $record['contractId'],
        ':broker_id' => $record['brokerId'],
        ':customer_id' => $record['customerId'],
        ':installments_number' => $record['installmentsNumber'],
        ':bill_id' => $record['billId'],
        ':rate' => $record['rate'],
        ':value' => $record['value'],
        ':base_value' => $record['baseValue'],
        ':type' => $record['type']
    ]);
}

function saveCommissionInstallments(\PDO $pdo, int $commissionId, array $installments): void
{
    // Deletar parcelas antigas da comissão
    $stmt = $pdo->prepare("DELETE FROM sales_commission_installments WHERE commission_id = :commission_id");
    $stmt->execute([':commission_id' => $commissionId]);

    // Inserir novas parcelas
    foreach ($installments as $installment) {
        $stmt = $pdo->prepare("
            INSERT INTO sales_commission_installments (commission_id, installment_id, due_date, amount, status)
            VALUES (:commission_id, :installment_id, :due_date, :amount, :status)
        ");
        $stmt->execute([
            ':commission_id' => $commissionId,
            ':installment_id' => $installment['id'],
            ':due_date' => $installment['dueDate'],
            ':amount' => $installment['amount'],
            ':status' => $installment['status']
        ]);
    }
}

$offset = 0;
$limit = 200;
$totalRecords = 0;
$totalResults = 0;

do {
    $data = fetchData('sales-commissions', $offset, $limit);
    $totalRecords = $data['resultSetMetadata']['count'];
    $results = $data['results'];
    $totalResults += count($results);

    echo sprintf(
        "[%s] Importando %d registros do total de %d\n",
        date('d/m/Y H:i:s'),
        $totalResults,
        $totalRecords
    );

    // Inserir ou atualizar cada comissão e suas parcelas
    foreach ($results as $record) {
        saveSalesCommission($pdo, $record);
        saveCommissionInstallments($pdo, $record['id'], $record['installments']);
    }

    // Atualizar o offset para a próxima página
    $offset += $limit;

} while ($offset < $totalRecords);

echo "Importação de comissões de venda concluída.\n";