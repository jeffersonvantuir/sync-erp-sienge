<?php

require 'database.php';

$pdo = getDatabaseConnection();

$tables = [
    'cost_centers',
    'customer_phones',
    'customer_addresses',
    'customers',
    'sales_contract_customers',
    'sales_contract_units',
    'sales_contract_payment_conditions',
    'sales_contracts',
    'real_estate_units',
    'commissions',
    'creditor_phones',
    'creditors',
];

foreach ($tables as $table) {
    $stmt = $pdo->prepare("DROP TABLE IF EXISTS $table");
    $stmt->execute();
}

echo sprintf("Tabelas %s deletadas\n", join(', ', $tables));