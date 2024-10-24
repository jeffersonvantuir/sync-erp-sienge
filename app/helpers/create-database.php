<?php

require 'database.php';

$pdo = getDatabaseConnection();

$stmt = $pdo->prepare("
    CREATE TABLE IF NOT EXISTS cost_centers (
        sienge_id INT PRIMARY KEY,
        name VARCHAR(255),
        cnpj VARCHAR(20),
        company_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        modified_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )
");

$stmt->execute();

$stmt = $pdo->prepare("
    CREATE TABLE IF NOT EXISTS customers (
        sienge_id INT PRIMARY KEY,
        person_type VARCHAR(50),
        sex VARCHAR(50),
        document_number VARCHAR(20),
        email VARCHAR(255),
        name VARCHAR(255),
        state_registration_number VARCHAR(50),
        city_registration_number VARCHAR(50),
        fantasy_name VARCHAR(255),
        site VARCHAR(255),
        technical_manager VARCHAR(255),
        share_capital DECIMAL(15, 2),
        establishment_date DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        modified_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )
");

$stmt->execute();

$stmt = $pdo->prepare("
    CREATE TABLE IF NOT EXISTS customer_phones (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT,
        type VARCHAR(50),
        number VARCHAR(50),
        main BOOLEAN,
        note VARCHAR(255),
        FOREIGN KEY (customer_id) REFERENCES customers(sienge_id)
    )
");

$stmt->execute();

$stmt = $pdo->prepare("
    CREATE TABLE IF NOT EXISTS customer_addresses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT,
        type VARCHAR(50),
        street_name VARCHAR(255),
        number VARCHAR(50),
        complement VARCHAR(255),
        neighborhood VARCHAR(255),
        city_id INT,
        city VARCHAR(255),
        state VARCHAR(50),
        zip_code VARCHAR(20),
        mail BOOLEAN,
        FOREIGN KEY (customer_id) REFERENCES customers(sienge_id)
    )
");
$stmt->execute();

$stmt = $pdo->prepare("
    CREATE TABLE IF NOT EXISTS sales_contracts (
        sienge_id INT PRIMARY KEY,
        company_id INT,
        internal_company_id INT,
        company_name VARCHAR(255),
        enterprise_id INT,
        internal_enterprise_id INT,
        enterprise_name VARCHAR(255),
        receivable_bill_id INT,
        cancellation_payable_bill_id INT,
        number VARCHAR(50),
        correction_type VARCHAR(50),
        situation VARCHAR(50),
        discount_type VARCHAR(50),
        cancellation_reason TEXT,
        discount_percentage DECIMAL(5,2),
        value DECIMAL(15,2),
        total_selling_value DECIMAL(15,2),
        contract_date DATE,
        issue_date DATE,
        expected_delivery_date DATE,
        accounting_date DATE,
        keys_delivered_at DATE,
        cancellation_date DATE,
        total_cancellation_amount DECIMAL(15,2),
        creation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_update_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        financial_institution_date DATE,
        financial_institution_number VARCHAR(50),
        pro_rata_indexer DECIMAL(5,2),
        interest_percentage DECIMAL(5,2),
        interest_type CHAR(1),
        fine_rate DECIMAL(5,2),
        late_interest_calculation_type CHAR(1),
        daily_late_interest_value DECIMAL(15,2),
        contains_remade_installments BOOLEAN,
        special_clause TEXT,
        FOREIGN KEY (enterprise_id) REFERENCES cost_centers(sienge_id)
    )
");
$stmt->execute();

$stmt = $pdo->prepare("
    CREATE TABLE IF NOT EXISTS sales_contract_payment_conditions (
        sienge_id INT AUTO_INCREMENT PRIMARY KEY,
        contract_id INT,
        bearer_name VARCHAR(255),
        indexer_name VARCHAR(255),
        condition_type_id VARCHAR(50),
        condition_type_name VARCHAR(50),
        interest_type CHAR(1),
        match_maturities CHAR(1),
        installments_number INT,
        open_installments_number INT,
        bearer_id INT,
        indexer_id INT,
        months_grace_period INT,
        first_payment DATE,
        base_date DATE,
        base_date_interest DATE,
        total_value DECIMAL(15,2),
        outstanding_balance DECIMAL(15,2),
        interest_percentage DECIMAL(5,2),
        total_value_interest DECIMAL(15,2),
        amount_paid DECIMAL(15,2),
        sequence_id INT,
        order_number INT,
        order_number_remade_installments INT,
        paid_before_contract_additive BOOLEAN,
        FOREIGN KEY (contract_id) REFERENCES sales_contracts(sienge_id)
    )
");
$stmt->execute();


$stmt = $pdo->prepare("
    CREATE TABLE IF NOT EXISTS sales_contract_customers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sienge_id INT,
        contract_id INT,
        name VARCHAR(255),
        spouse BOOLEAN,
        main BOOLEAN,
        participation_percentage DECIMAL(5, 2),
        FOREIGN KEY (contract_id) REFERENCES sales_contracts(sienge_id),
        FOREIGN KEY (sienge_id) REFERENCES customers(sienge_id)
    );
");
$stmt->execute();

$stmt = $pdo->prepare("
    CREATE TABLE IF NOT EXISTS sales_contract_units (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sienge_id INT,
        contract_id INT,
        main BOOLEAN,
        name VARCHAR(255),
        participation_percentage DECIMAL(5, 2),
        FOREIGN KEY (contract_id) REFERENCES sales_contracts(sienge_id)
    )
");
$stmt->execute();

$stmt = $pdo->prepare("
    CREATE TABLE IF NOT EXISTS real_estate_units (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sienge_id INT,
        enterprise_id INT,
        contract_id INT,
        indexer_id INT,
        name VARCHAR(255),
        property_type VARCHAR(255),
        note TEXT,
        commercial_stock CHAR(1),
        latitude VARCHAR(50),
        longitude VARCHAR(50),
        legal_registration_number VARCHAR(255),
        floor VARCHAR(50),
        contract_number VARCHAR(50),
        delivery_date DATE,
        scheduled_delivery_date DATE,
        private_area DECIMAL(15,2),
        common_area DECIMAL(15,2),
        terrain_area DECIMAL(15,2),
        non_proportional_common_area DECIMAL(15,2),
        ideal_fraction DECIMAL(15,2),
        ideal_fraction_square_meter DECIMAL(15,2),
        general_sale_value_fraction DECIMAL(15,2),
        terrain_value DECIMAL(15,2),
        indexed_quantity DECIMAL(15,2),
        prized_compliance VARCHAR(255),
        usable_area DECIMAL(15,2),
        iptu_value DECIMAL(15,2),
        real_estate_registration VARCHAR(255)
    )
");
$stmt->execute();

$stmt = $pdo->prepare("
    CREATE TABLE IF NOT EXISTS commissions (
        commission_id INT PRIMARY KEY,
        enterprise_id INT,
        enterprise_name VARCHAR(255),
        bill_number INT,
        customer_id INT,
        customer_name VARCHAR(255),
        customer_situation_type VARCHAR(50),
        unit_name VARCHAR(255),
        broker_id INT,
        broker_name VARCHAR(255),
        billing_broker_id INT,
        billing_broker_name VARCHAR(255),
        block_edit BOOLEAN,
        value DECIMAL(15,2),
        installment_percentage DECIMAL(5,2),
        installment_status VARCHAR(50),
        payment_operation_type VARCHAR(50),
        sales_contract_number VARCHAR(50),
        contract_bill_number INT,
        contract_percentage_paid DECIMAL(5,2),
        consider_embedded_interest BOOLEAN,
        commission_released_to_be_paid BOOLEAN,
        commission_released_automatically BOOLEAN,
        due_date DATE,
        installment_number INT,
        total_installments_number INT
    )
");
$stmt->execute();

$stmt = $pdo->prepare("
    CREATE TABLE IF NOT EXISTS creditors (
        id INT PRIMARY KEY,
        name VARCHAR(255),
        trade_name VARCHAR(255),
        cpf VARCHAR(20),
        cnpj VARCHAR(20),
        supplier CHAR(1),
        broker CHAR(1),
        employee CHAR(1),
        active BOOLEAN,
        state_registration_number VARCHAR(50),
        state_registration_type VARCHAR(50),
        payment_type_id INT,
        city_id INT,
        city_name VARCHAR(255),
        street_name VARCHAR(255),
        number VARCHAR(50),
        complement VARCHAR(255),
        neighborhood VARCHAR(255),
        state VARCHAR(50),
        zip_code VARCHAR(20)
    )
");
$stmt->execute();

$stmt = $pdo->prepare("
    CREATE TABLE IF NOT EXISTS creditor_phones (
        id INT AUTO_INCREMENT PRIMARY KEY,
        creditor_id INT,
        ddd VARCHAR(10),
        number VARCHAR(20),
        main BOOLEAN,
        type INT,
        extension VARCHAR(10),
        observation TEXT,
        FOREIGN KEY (creditor_id) REFERENCES creditors(id)
    )
");
$stmt->execute();

$stmt = $pdo->prepare("
    CREATE TABLE IF NOT EXISTS sales_commissions (
        id INT PRIMARY KEY,
        contract_id INT,
        broker_id INT,
        customer_id INT,
        installments_number INT,
        bill_id INT,
        rate DECIMAL(5,2),
        value DECIMAL(15,2),
        base_value DECIMAL(15,2),
        type CHAR(1)
    )
");
$stmt->execute();

$stmt = $pdo->prepare("
        CREATE TABLE IF NOT EXISTS sales_commission_installments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        commission_id INT,
        installment_id INT,
        due_date DATE,
        amount DECIMAL(15,2),
        status VARCHAR(50),
        FOREIGN KEY (commission_id) REFERENCES sales_commissions(id)
    )
");
$stmt->execute();
