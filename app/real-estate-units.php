<?php

// Incluir o arquivo de conexão
require 'helpers/database.php';
require 'helpers/http.php';

// Obter a conexão com o banco de dados
$pdo = getDatabaseConnection();

function saveRealEstateUnit($pdo, $record) {
    // Verificar se a unidade já existe na tabela
    $stmt = $pdo->prepare("SELECT sienge_id FROM real_estate_units WHERE sienge_id = :id");
    $stmt->execute([':id' => $record['id']]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Atualizar unidade existente
        $stmt = $pdo->prepare("
            UPDATE real_estate_units
            SET enterprise_id = :enterprise_id, contract_id = :contract_id, indexer_id = :indexer_id, name = :name,
                property_type = :property_type, note = :note, commercial_stock = :commercial_stock, latitude = :latitude,
                longitude = :longitude, legal_registration_number = :legal_registration_number, floor = :floor,
                contract_number = :contract_number, delivery_date = :delivery_date, scheduled_delivery_date = :scheduled_delivery_date,
                private_area = :private_area, common_area = :common_area, terrain_area = :terrain_area, 
                non_proportional_common_area = :non_proportional_common_area, ideal_fraction = :ideal_fraction, 
                ideal_fraction_square_meter = :ideal_fraction_square_meter, general_sale_value_fraction = :general_sale_value_fraction, 
                terrain_value = :terrain_value, indexed_quantity = :indexed_quantity, prized_compliance = :prized_compliance, 
                usable_area = :usable_area, iptu_value = :iptu_value, real_estate_registration = :real_estate_registration
            WHERE sienge_id = :sienge_id
        ");
    } else {
        // Inserir nova unidade
        $stmt = $pdo->prepare("
            INSERT INTO real_estate_units (
                sienge_id, enterprise_id, contract_id, indexer_id, name, property_type, note, commercial_stock, latitude, longitude, 
                legal_registration_number, floor, contract_number, delivery_date, scheduled_delivery_date, private_area, common_area, 
                terrain_area, non_proportional_common_area, ideal_fraction, ideal_fraction_square_meter, general_sale_value_fraction, 
                terrain_value, indexed_quantity, prized_compliance, usable_area, iptu_value, real_estate_registration
            ) VALUES (
                :sienge_id, :enterprise_id, :contract_id, :indexer_id, :name, :property_type, :note, :commercial_stock, :latitude, :longitude, 
                :legal_registration_number, :floor, :contract_number, :delivery_date, :scheduled_delivery_date, :private_area, :common_area, 
                :terrain_area, :non_proportional_common_area, :ideal_fraction, :ideal_fraction_square_meter, :general_sale_value_fraction, 
                :terrain_value, :indexed_quantity, :prized_compliance, :usable_area, :iptu_value, :real_estate_registration
            )
        ");
    }

    // Executar a query (insert ou update)
    $stmt->execute([
        ':sienge_id' => $record['id'],
        ':enterprise_id' => $record['enterpriseId'],
        ':contract_id' => $record['contractId'],
        ':indexer_id' => $record['indexerId'],
        ':name' => $record['name'],
        ':property_type' => $record['propertyType'],
        ':note' => $record['note'],
        ':commercial_stock' => $record['commercialStock'],
        ':latitude' => $record['latitude'],
        ':longitude' => $record['longitude'],
        ':legal_registration_number' => $record['legalRegistrationNumber'],
        ':floor' => $record['floor'],
        ':contract_number' => $record['contractNumber'],
        ':delivery_date' => $record['deliveryDate'],
        ':scheduled_delivery_date' => $record['scheduledDeliveryDate'],
        ':private_area' => $record['privateArea'],
        ':common_area' => $record['commonArea'],
        ':terrain_area' => $record['terrainArea'],
        ':non_proportional_common_area' => $record['nonProportionalCommonArea'],
        ':ideal_fraction' => $record['idealFraction'],
        ':ideal_fraction_square_meter' => $record['idealFractionSquareMeter'],
        ':general_sale_value_fraction' => $record['generalSaleValueFraction'],
        ':terrain_value' => $record['terrainValue'],
        ':indexed_quantity' => $record['indexedQuantity'],
        ':prized_compliance' => $record['prizedCompliance'],
        ':usable_area' => $record['usableArea'],
        ':iptu_value' => $record['iptuValue'],
        ':real_estate_registration' => $record['realEstateRegistration']
    ]);
}

$offset = 0;
$limit = 200;
$totalRecords = 0;
$totalResults = 0;

do {
    $data = fetchData('units', $offset, $limit);
    $totalRecords = $data['resultSetMetadata']['count'];
    $results = $data['results'];
    $totalResults += count($results);

    echo sprintf(
        "[%s] Importando %d registros do total de %d\n",
        date('d/m/Y H:i:s'),
        $totalResults,
        $totalRecords
    );

    // Inserir ou atualizar cada unidade
    foreach ($results as $record) {
        saveRealEstateUnit($pdo, $record);
    }

    // Atualizar o offset para a próxima página
    $offset += $limit;

} while ($offset < $totalRecords);

echo "Importação de unidades de imóveis concluída.\n";