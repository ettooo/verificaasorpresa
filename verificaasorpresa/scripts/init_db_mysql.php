<?php

declare(strict_types=1);

$databaseHost = getenv('MYSQL_HOST') ?: '127.0.0.1';
$databasePort = getenv('MYSQL_PORT') ?: '3306';
$databaseName = getenv('MYSQL_DATABASE') ?: 'verificaasorpresa';
$databaseUser = getenv('MYSQL_USER') ?: 'root';
$databasePassword = getenv('MYSQL_PASSWORD') ?: 'root';

$schemaPath = __DIR__ . '/../database/schema.mysql.sql';
$sql = file_get_contents($schemaPath);
if ($sql === false) {
    fwrite(STDERR, "Impossibile leggere schema MySQL\n");
    exit(1);
}

$dsnWithoutDb = sprintf('mysql:host=%s;port=%s;charset=utf8mb4', $databaseHost, $databasePort);
$pdoAdmin = new PDO($dsnWithoutDb, $databaseUser, $databasePassword, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);
$pdoAdmin->exec(sprintf('CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci', $databaseName));

$dsnWithDb = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $databaseHost, $databasePort, $databaseName);
$pdo = new PDO($dsnWithDb, $databaseUser, $databasePassword, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$pdo->exec($sql);

fwrite(STDOUT, "Database MySQL inizializzato: {$databaseName} ({$databaseHost}:{$databasePort})\n");
