<?php

require 'helpers/database.php';
require 'helpers/http.php';

$pdo = getDatabaseConnection();

function saveCreditor(\PDO $pdo, array $record): void
{
    $stmt = $pdo->prepare("SELECT id FROM creditors WHERE id = :id");
    $stmt->execute([':id' => $record['id']]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $stmt = $pdo->prepare("
            UPDATE creditors
            SET name = :name, trade_name = :trade_name, cpf = :cpf, cnpj = :cnpj, supplier = :supplier,
                broker = :broker, employee = :employee, active = :active, state_registration_number = :state_registration_number,
                state_registration_type = :state_registration_type, payment_type_id = :payment_type_id, city_id = :city_id,
                city_name = :city_name, street_name = :street_name, number = :number, complement = :complement,
                neighborhood = :neighborhood, state = :state, zip_code = :zip_code
            WHERE id = :id
        ");
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO creditors (
                id, name, trade_name, cpf, cnpj, supplier, broker, employee, active, state_registration_number, 
                state_registration_type, payment_type_id, city_id, city_name, street_name, number, complement, 
                neighborhood, state, zip_code
            ) VALUES (
                :id, :name, :trade_name, :cpf, :cnpj, :supplier, :broker, :employee, :active, :state_registration_number,
                :state_registration_type, :payment_type_id, :city_id, :city_name, :street_name, :number, :complement, 
                :neighborhood, :state, :zip_code
            )
        ");
    }

    // Executar a query (insert ou update)
    $stmt->execute([
        ':id' => $record['id'],
        ':name' => $record['name'],
        ':trade_name' => $record['tradeName'],
        ':cpf' => $record['cpf'],
        ':cnpj' => $record['cnpj'],
        ':supplier' => $record['supplier'],
        ':broker' => $record['broker'],
        ':employee' => $record['employee'],
        ':active' => $record['active'] ? 1 : 0,
        ':state_registration_number' => $record['stateRegistrationNumber'],
        ':state_registration_type' => $record['stateRegistrationType'],
        ':payment_type_id' => $record['paymentTypeId'],
        ':city_id' => $record['address']['cityId'],
        ':city_name' => $record['address']['cityName'],
        ':street_name' => $record['address']['streetName'],
        ':number' => $record['address']['number'],
        ':complement' => $record['address']['complement'],
        ':neighborhood' => $record['address']['neighborhood'],
        ':state' => $record['address']['state'],
        ':zip_code' => $record['address']['zipCode']
    ]);
}

function saveCreditorPhones(\PDO $pdo, int $creditorId, array $phones): void
{
    $stmt = $pdo->prepare("DELETE FROM creditor_phones WHERE creditor_id = :creditor_id");
    $stmt->execute([':creditor_id' => $creditorId]);

    foreach ($phones as $phone) {
        $stmt = $pdo->prepare("
            INSERT INTO creditor_phones (creditor_id, ddd, number, main, type, extension, observation)
            VALUES (:creditor_id, :ddd, :number, :main, :type, :extension, :observation)
        ");

        $stmt->execute([
            ':creditor_id' => $creditorId,
            ':ddd' => $phone['ddd'],
            ':number' => $phone['number'],
            ':main' => $phone['main'] ? 1 : 0,
            ':type' => $phone['type'],
            ':extension' => $phone['extension'],
            ':observation' => $phone['observation']
        ]);
    }
}

$offset = 0;
$limit = 200;
$totalRecords = 0;
$totalResults = 0;

do {
    $data = fetchData('creditors', $offset, $limit);
    $totalRecords = $data['resultSetMetadata']['count'];
    $results = $data['results'];
    $totalResults += count($results);

    echo sprintf(
        "[%s] Importando %d registros do total de %d\n",
        date('d/m/Y H:i:s'),
        $totalResults,
        $totalRecords
    );

    // Inserir ou atualizar cada credor e seus telefones
    foreach ($results as $record) {
        saveCreditor($pdo, $record);
        saveCreditorPhones($pdo, $record['id'], $record['phones']);
    }

    // Atualizar o offset para a próxima página
    $offset += $limit;

} while ($offset < $totalRecords);

echo "Importação de credores concluída.\n";