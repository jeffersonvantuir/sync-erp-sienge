<?php

// Incluir o arquivo de conexão
require 'helpers/database.php';
require 'helpers/http.php';

// Obter a conexão com o banco de dados
$pdo = getDatabaseConnection();

function saveCommission(\PDO $pdo, array $record): void
{
    $stmt = $pdo->prepare("SELECT commission_id FROM commissions WHERE commission_id = :commission_id");
    $stmt->execute([':commission_id' => $record['commissionID']]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $stmt = $pdo->prepare("
            UPDATE commissions
            SET enterprise_id = :enterprise_id, enterprise_name = :enterprise_name, bill_number = :bill_number, 
                customer_id = :customer_id, customer_name = :customer_name, customer_situation_type = :customer_situation_type, 
                unit_name = :unit_name, broker_id = :broker_id, broker_name = :broker_name, billing_broker_id = :billing_broker_id, 
                billing_broker_name = :billing_broker_name, block_edit = :block_edit, value = :value, installment_percentage = :installment_percentage, 
                installment_status = :installment_status, payment_operation_type = :payment_operation_type, 
                sales_contract_number = :sales_contract_number, contract_bill_number = :contract_bill_number, 
                contract_percentage_paid = :contract_percentage_paid, consider_embedded_interest = :consider_embedded_interest, 
                commission_released_to_be_paid = :commission_released_to_be_paid, commission_released_automatically = :commission_released_automatically, 
                due_date = :due_date, installment_number = :installment_number, total_installments_number = :total_installments_number
            WHERE commission_id = :commission_id
        ");
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO commissions (
                commission_id, enterprise_id, enterprise_name, bill_number, customer_id, customer_name, customer_situation_type, unit_name, 
                broker_id, broker_name, billing_broker_id, billing_broker_name, block_edit, value, installment_percentage, installment_status, 
                payment_operation_type, sales_contract_number, contract_bill_number, contract_percentage_paid, consider_embedded_interest, 
                commission_released_to_be_paid, commission_released_automatically, due_date, installment_number, total_installments_number
            ) VALUES (
                :commission_id, :enterprise_id, :enterprise_name, :bill_number, :customer_id, :customer_name, :customer_situation_type, :unit_name, 
                :broker_id, :broker_name, :billing_broker_id, :billing_broker_name, :block_edit, :value, :installment_percentage, :installment_status, 
                :payment_operation_type, :sales_contract_number, :contract_bill_number, :contract_percentage_paid, :consider_embedded_interest, 
                :commission_released_to_be_paid, :commission_released_automatically, :due_date, :installment_number, :total_installments_number
            )
        ");
    }

    // Executar a query (insert ou update)
    $stmt->execute([
        ':commission_id' => $record['commissionID'],
        ':enterprise_id' => $record['enterpriseID'],
        ':enterprise_name' => $record['enterpriseName'],
        ':bill_number' => $record['billNumber'],
        ':customer_id' => $record['customerID'],
        ':customer_name' => $record['customerName'],
        ':customer_situation_type' => $record['customerSituationType'],
        ':unit_name' => $record['unitName'],
        ':broker_id' => $record['brokerID'],
        ':broker_name' => $record['brokerName'],
        ':billing_broker_id' => $record['billingBrokerId'],
        ':billing_broker_name' => $record['billingBrokerName'],
        ':block_edit' => $record['blockEdit'] ? 1 : 0,
        ':value' => $record['value'],
        ':installment_percentage' => $record['installmentPercentage'],
        ':installment_status' => $record['installmentStatus'],
        ':payment_operation_type' => $record['paymentOperationType'],
        ':sales_contract_number' => $record['salesContractNumber'],
        ':contract_bill_number' => $record['contractBillNumber'],
        ':contract_percentage_paid' => $record['contractPercentagePaid'],
        ':consider_embedded_interest' => $record['considerEmbeddedInterest'],
        ':commission_released_to_be_paid' => $record['commissionReleasedToBePaid'],
        ':commission_released_automatically' => $record['commissionReleasedAutomatically'] ? 1 : 0,
        ':due_date' => $record['dueDate'],
        ':installment_number' => $record['installmentNumber'],
        ':total_installments_number' => $record['totalInstallmentsNumber']
    ]);
}

$offset = 0;
$limit = 200;
$totalRecords = 0;
$totalResults = 0;

do {
    $data = fetchData('commissions', $offset, $limit);
    $totalRecords = $data['resultSetMetadata']['count'];
    $results = $data['results'];
    $totalResults += count($results);

    echo sprintf(
        "[%s] Importando %d registros do total de %d\n",
        date('d/m/Y H:i:s'),
        $totalResults,
        $totalRecords
    );

    // Inserir ou atualizar cada comissão
    foreach ($results as $record) {
        saveCommission($pdo, $record);
    }

    // Atualizar o offset para a próxima página
    $offset += $limit;

} while ($offset < $totalRecords);

echo "Importação de comissões concluída.\n";