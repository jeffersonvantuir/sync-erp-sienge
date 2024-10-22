<?php

// Incluir o arquivo de conexão
require 'helpers/database.php';
require 'helpers/http.php';

// Obter a conexão com o banco de dados
$pdo = getDatabaseConnection();

function saveSalesContract($pdo, $record): void
{
    // Verificar se o contrato já existe na tabela
    $stmt = $pdo->prepare("SELECT sienge_id FROM sales_contracts WHERE sienge_id = :id");
    $stmt->execute([':id' => $record['id']]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Atualizar contrato existente
        $stmt = $pdo->prepare("
            UPDATE sales_contracts 
            SET company_id = :company_id, internal_company_id = :internal_company_id, company_name = :company_name, 
                enterprise_id = :enterprise_id, internal_enterprise_id = :internal_enterprise_id, enterprise_name = :enterprise_name, 
                receivable_bill_id = :receivable_bill_id, cancellation_payable_bill_id = :cancellation_payable_bill_id, number = :number, 
                correction_type = :correction_type, situation = :situation, discount_type = :discount_type, cancellation_reason = :cancellation_reason, 
                discount_percentage = :discount_percentage, value = :value, total_selling_value = :total_selling_value, 
                contract_date = :contract_date, issue_date = :issue_date, expected_delivery_date = :expected_delivery_date, 
                accounting_date = :accounting_date, keys_delivered_at = :keys_delivered_at, cancellation_date = :cancellation_date, 
                total_cancellation_amount = :total_cancellation_amount, creation_date = :creation_date, last_update_date = :last_update_date, 
                financial_institution_date = :financial_institution_date, financial_institution_number = :financial_institution_number, 
                pro_rata_indexer = :pro_rata_indexer, interest_percentage = :interest_percentage, interest_type = :interest_type, 
                fine_rate = :fine_rate, late_interest_calculation_type = :late_interest_calculation_type, daily_late_interest_value = :daily_late_interest_value, 
                contains_remade_installments = :contains_remade_installments, special_clause = :special_clause
            WHERE sienge_id = :id
        ");
    } else {
        // Inserir novo contrato
        $stmt = $pdo->prepare("
            INSERT INTO sales_contracts (
                sienge_id, company_id, internal_company_id, company_name, enterprise_id, internal_enterprise_id, enterprise_name, 
                receivable_bill_id, cancellation_payable_bill_id, number, correction_type, situation, discount_type, cancellation_reason, 
                discount_percentage, value, total_selling_value, contract_date, issue_date, expected_delivery_date, accounting_date, 
                keys_delivered_at, cancellation_date, total_cancellation_amount, creation_date, last_update_date, financial_institution_date, 
                financial_institution_number, pro_rata_indexer, interest_percentage, interest_type, fine_rate, late_interest_calculation_type, 
                daily_late_interest_value, contains_remade_installments, special_clause
            ) VALUES (
                :id, :company_id, :internal_company_id, :company_name, :enterprise_id, :internal_enterprise_id, :enterprise_name, 
                :receivable_bill_id, :cancellation_payable_bill_id, :number, :correction_type, :situation, :discount_type, :cancellation_reason, 
                :discount_percentage, :value, :total_selling_value, :contract_date, :issue_date, :expected_delivery_date, :accounting_date, 
                :keys_delivered_at, :cancellation_date, :total_cancellation_amount, :creation_date, :last_update_date, :financial_institution_date, 
                :financial_institution_number, :pro_rata_indexer, :interest_percentage, :interest_type, :fine_rate, :late_interest_calculation_type, 
                :daily_late_interest_value, :contains_remade_installments, :special_clause
            )
        ");
    }

    // Executar a query (insert ou update)
    $stmt->execute([
        ':id' => $record['id'],
        ':company_id' => $record['companyId'],
        ':internal_company_id' => $record['internalCompanyId'],
        ':company_name' => $record['companyName'],
        ':enterprise_id' => $record['enterpriseId'],
        ':internal_enterprise_id' => $record['internalEnterpriseId'],
        ':enterprise_name' => $record['enterpriseName'],
        ':receivable_bill_id' => $record['receivableBillId'],
        ':cancellation_payable_bill_id' => $record['cancellationPayableBillId'],
        ':number' => $record['number'],
        ':correction_type' => $record['correctionType'],
        ':situation' => $record['situation'],
        ':discount_type' => $record['discountType'],
        ':cancellation_reason' => $record['cancellationReason'],
        ':discount_percentage' => $record['discountPercentage'],
        ':value' => $record['value'],
        ':total_selling_value' => $record['totalSellingValue'],
        ':contract_date' => $record['contractDate'],
        ':issue_date' => $record['issueDate'],
        ':expected_delivery_date' => $record['expectedDeliveryDate'],
        ':accounting_date' => $record['accountingDate'],
        ':keys_delivered_at' => $record['keysDeliveredAt'],
        ':cancellation_date' => $record['cancellationDate'],
        ':total_cancellation_amount' => $record['totalCancellationAmount'],
        ':creation_date' => $record['creationDate'],
        ':last_update_date' => $record['lastUpdateDate'],
        ':financial_institution_date' => $record['financialInstitutionDate'],
        ':financial_institution_number' => $record['financialInstitutionNumber'],
        ':pro_rata_indexer' => $record['proRataIndexer'],
        ':interest_percentage' => $record['interestPercentage'],
        ':interest_type' => $record['interestType'],
        ':fine_rate' => $record['fineRate'],
        ':late_interest_calculation_type' => $record['lateInterestCalculationType'],
        ':daily_late_interest_value' => $record['dailyLateInterestValue'],
        ':contains_remade_installments' => empty($record['containsRemadeInstallments']) ? null : $record['containsRemadeInstallments'],
        ':special_clause' => $record['specialClause']
    ]);
}

function saveSalesContractPaymentConditions($pdo, $contractId, $paymentConditions) {
    // Deletar condições de pagamento antigas do contrato
    $stmt = $pdo->prepare("DELETE FROM sales_contract_payment_conditions WHERE contract_id = :contract_id");
    $stmt->execute([':contract_id' => $contractId]);

    foreach ($paymentConditions as $condition) {
        $stmt = $pdo->prepare("
            INSERT INTO sales_contract_payment_conditions (
                contract_id, bearer_name, indexer_name, condition_type_id, condition_type_name, interest_type, 
                match_maturities, installments_number, open_installments_number, bearer_id, indexer_id, 
                months_grace_period, first_payment, base_date, base_date_interest, total_value, outstanding_balance, 
                interest_percentage, total_value_interest, amount_paid, sequence_id, order_number, order_number_remade_installments, 
                paid_before_contract_additive
            ) VALUES (
                :contract_id, :bearer_name, :indexer_name, :condition_type_id, :condition_type_name, :interest_type, 
                :match_maturities, :installments_number, :open_installments_number, :bearer_id, :indexer_id, 
                :months_grace_period, :first_payment, :base_date, :base_date_interest, :total_value, :outstanding_balance, 
                :interest_percentage, :total_value_interest, :amount_paid, :sequence_id, :order_number, :order_number_remade_installments, 
                :paid_before_contract_additive
            )
        ");

        $stmt->execute([
            ':contract_id' => $contractId,
            ':bearer_name' => $condition['bearerName'],
            ':indexer_name' => $condition['indexerName'],
            ':condition_type_id' => $condition['conditionTypeId'],
            ':condition_type_name' => $condition['conditionTypeName'],
            ':interest_type' => $condition['interestType'],
            ':match_maturities' => $condition['matchMaturities'],
            ':installments_number' => $condition['installmentsNumber'],
            ':open_installments_number' => $condition['openInstallmentsNumber'],
            ':bearer_id' => $condition['bearerId'],
            ':indexer_id' => $condition['indexerId'],
            ':months_grace_period' => $condition['monthsGracePeriod'],
            ':first_payment' => $condition['firstPayment'],
            ':base_date' => $condition['baseDate'],
            ':base_date_interest' => $condition['baseDateInterest'],
            ':total_value' => $condition['totalValue'],
            ':outstanding_balance' => $condition['outstandingBalance'],
            ':interest_percentage' => $condition['interestPercentage'],
            ':total_value_interest' => $condition['totalValueInterest'],
            ':amount_paid' => $condition['amountPaid'],
            ':sequence_id' => $condition['sequenceId'],
            ':order_number' => $condition['orderNumber'],
            ':order_number_remade_installments' => $condition['orderNumberRemadeInstallments'],
            ':paid_before_contract_additive' => $condition['paidBeforeContractAdditive'] ? 1 : 0
        ]);
    }
}

function saveSalesContractUnits(\PDO $pdo, int $contractId, array $units): void
{
    if (empty($units)) {
        return;
    }

    // Deletar unidades antigas do contrato
    $stmt = $pdo->prepare("DELETE FROM sales_contract_units WHERE contract_id = :contract_id");
    $stmt->execute([':contract_id' => $contractId]);

    // Inserir novas unidades
    foreach ($units as $unit) {
        $stmt = $pdo->prepare("
            INSERT INTO sales_contract_units (
                sienge_id,
                contract_id,
                name,
                main,
                participation_percentage
            )
            VALUES (
             :sienge_id,
             :contract_id,
             :name,
             :main,
             :participation_percentage
            )
        ");

        $stmt->execute([
            ':sienge_id' => $unit['id'],
            ':name' => $unit['name'],
            ':main' => $unit['main'] ? 1 : 0,
            ':participation_percentage' => $unit['participationPercentage'] ?? null,
            ':contract_id' => $contractId
        ]);
    }
}

function saveSalesContractCustomers(\PDO $pdo, int $contractId, array $customers) {

    // Deletar clientes antigos do contrato
    $stmt = $pdo->prepare("DELETE FROM sales_contract_customers WHERE contract_id = :contract_id");
    $stmt->execute([':contract_id' => $contractId]);

    // Inserir novos clientes
    foreach ($customers as $customer) {
        $stmt = $pdo->prepare("
            INSERT INTO sales_contract_customers (
                contract_id,
                sienge_id,
                name,
                main,
                spouse,
                participation_percentage
            )
            VALUES (
                :contract_id,
                :sienge_id,
                :name,
                :main,
                :spouse,
                :participation_percentage
            )
        ");

        $stmt->execute([
            ':name' => $customer['name'],
            ':main' => $customer['main'] ? 1 : 0,
            ':spouse' => $customer['spouse'] ? 1 : 0,
            ':participation_percentage' => $customer['participationPercentage'] ?? null,
            ':contract_id' => $contractId,
            ':sienge_id' => $customer['id']
        ]);
    }
}

$offset = 0;
$limit = 200;
$totalRecords = 0;
$totalResults = 0;

do {
    $data = fetchData('sales-contracts', $offset, $limit);
    $totalRecords = $data['resultSetMetadata']['count'];
    $results = $data['results'];
    $totalResults += count($results);

    echo sprintf(
        "[%s] Importando %d registros do total de %d\n",
        date('d/m/Y H:i:s'),
        $totalResults,
        $totalRecords
    );

    // Inserir ou atualizar cada contrato e seus dados relacionados
    foreach ($results as $record) {
        saveSalesContract($pdo, $record);
        saveSalesContractPaymentConditions($pdo, $record['id'], $record['paymentConditions']);
        saveSalesContractUnits($pdo, $record['id'], $record['salesContractUnits'] ?? []);
        saveSalesContractCustomers($pdo, $record['id'], $record['salesContractCustomers']);
    }

    // Atualizar o offset para a próxima página
    $offset += $limit;

} while ($offset < $totalRecords);

echo "Importação de contratos de venda concluída.\n";