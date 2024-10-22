<?php

function loadEnvVars(): array
{
    return [
        'MYSQL_HOST' => 'mysql',
        'MYSQL_PORT' => '3306',
        'MYSQL_USERNAME' => 'dev',
        'MYSQL_PASSWORD' => 'nopassword',
        'MYSQL_DATABASE_NAME' => 'dev',
        'SIENGE_ERP_BASE_URL' => 'https://api....',
        'SIENGE_ERP_USERNAME' => '....',
        'SIENGE_ERP_PASSWORD' => '.....',
    ];
}