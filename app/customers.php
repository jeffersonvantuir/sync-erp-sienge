<?php

// Incluir o arquivo de conexão
require 'helpers/database.php';
require 'helpers/http.php';

// Obter a conexão com o banco de dados
$pdo = getDatabaseConnection();

function saveCustomer($pdo, $record) {
    // Verificar se o cliente já existe na tabela
    $stmt = $pdo->prepare("SELECT sienge_id FROM customers WHERE sienge_id = :id");
    $stmt->execute([':id' => $record['id']]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Atualizar cliente existente
        $stmt = $pdo->prepare("
            UPDATE customers 
            SET person_type = :personType, 
                document_number = :documentNumber,
                sex = :sex,
                email = :email,
                name = :name,
                state_registration_number = :stateRegistrationNumber, 
                city_registration_number = :cityRegistrationNumber,
                fantasy_name = :fantasyName,
                site = :site,
                technical_manager = :technicalManager, 
                share_capital = :shareCapital,
                establishment_date = :establishmentDate,
                modified_at = NOW()
            WHERE sienge_id = :id
        ");
    } else {
        // Inserir novo cliente
        $stmt = $pdo->prepare("
            INSERT INTO customers (
                sienge_id,
                person_type,
                sex,
                document_number,
                email,
                name, 
                state_registration_number,
                city_registration_number,
                fantasy_name,
                site,
                technical_manager, 
                share_capital,
                establishment_date,
                created_at,
                modified_at
            )
            VALUES (
                :id,
                :personType,
                :sex,
                :documentNumber,
                :email,
                :name,
                :stateRegistrationNumber, 
                :cityRegistrationNumber,
                :fantasyName,
                :site,
                :technicalManager,
                :shareCapital,
                :establishmentDate,
                NOW(),
                NOW()
            )
        ");
    }

    // Executar a query (insert ou update)
    $stmt->execute([
        ':id' => $record['id'],
        ':personType' => $record['personType'],
        ':sex' => $record['sex'] ?? null,
        ':documentNumber' => $record['cnpj'] ?? ($record['cpf'] ?? null),
        ':email' => $record['email'],
        ':name' => $record['name'],
        ':stateRegistrationNumber' => $record['stateRegistrationNumber'] ?? null,
        ':cityRegistrationNumber' => $record['cityRegistrationNumber'] ?? null,
        ':fantasyName' => $record['fantasyName'] ?? null,
        ':site' => $record['site'] ?? null,
        ':technicalManager' => $record['technicalManager'] ?? null,
        ':shareCapital' => $record['shareCapital'] ?? null,
        ':establishmentDate' => $record['establishmentDate'] ?? null
    ]);
}

function saveCustomerPhones($pdo, $customerId, $phones) {
    // Deletar telefones antigos do cliente
    $stmt = $pdo->prepare("DELETE FROM customer_phones WHERE customer_id = :customer_id");
    $stmt->execute([':customer_id' => $customerId]);

    // Inserir novos telefones
    foreach ($phones as $phone) {
        $stmt = $pdo->prepare("
            INSERT INTO customer_phones (customer_id, type, number, main, note)
            VALUES (:customer_id, :type, :number, :main, :note)
        ");
        $stmt->execute([
            ':customer_id' => $customerId,
            ':type' => $phone['type'],
            ':number' => $phone['number'],
            ':main' => false === empty($phone['main']) ? 1 : 0,
            ':note' => $phone['note']
        ]);
    }
}

function saveCustomerAddresses($pdo, $customerId, $addresses): void
{
    // Deletar endereços antigos do cliente
    $stmt = $pdo->prepare("DELETE FROM customer_addresses WHERE customer_id = :customer_id");
    $stmt->execute([':customer_id' => $customerId]);

    // Inserir novos endereços
    foreach ($addresses as $address) {
        $stmt = $pdo->prepare("
            INSERT INTO customer_addresses (customer_id, type, street_name, number, complement, neighborhood, city_id, city, state, zip_code, mail)
            VALUES (:customer_id, :type, :streetName, :number, :complement, :neighborhood, :cityId, :city, :state, :zipCode, :mail)
        ");
        $stmt->execute([
            ':customer_id' => $customerId,
            ':type' => $address['type'],
            ':streetName' => $address['streetName'],
            ':number' => $address['number'],
            ':complement' => $address['complement'],
            ':neighborhood' => $address['neighborhood'],
            ':cityId' => $address['cityId'],
            ':city' => $address['city'],
            ':state' => $address['state'],
            ':zipCode' => $address['zipCode'],
            ':mail' => false === empty($address['mail']) ? 1 : 0
        ]);
    }
}

$offset = 0;
$limit = 200;
$totalRecords = 0;
$totalResults = 0;

do {
    $data = fetchData('customers', $offset, $limit);
    $totalRecords = $data['resultSetMetadata']['count'];
    $results = $data['results'];
    $totalResults += count($results);

    echo sprintf(
        "[%s] Importando %d registros do total de %d\n",
        date('d/m/Y H:i:s'),
        $totalResults,
        $totalRecords
    );

    // Inserir ou atualizar cada cliente e seus dados relacionados
    foreach ($results as $record) {
        saveCustomer($pdo, $record);
        saveCustomerPhones($pdo, $record['id'], $record['phones']);
        saveCustomerAddresses($pdo, $record['id'], $record['addresses']);
    }

    // Atualizar o offset para a próxima página
    $offset += $limit;

} while ($offset < $totalRecords);

echo "Importação de clientes concluída.\n";