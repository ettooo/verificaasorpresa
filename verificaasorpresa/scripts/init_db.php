<?php

declare(strict_types=1);

// Directory e file usati per il database SQLite locale.
$databaseDir = __DIR__ . '/../database';
$databasePath = $databaseDir . '/database.sqlite';
$schemaPath = $databaseDir . '/schema.sql';

// Crea la directory database se non esiste.
if (!is_dir($databaseDir)) {
    mkdir($databaseDir, 0777, true);
}

// Apre (o crea) il database SQLite e abilita gestione errori via eccezioni.
$pdo = new PDO('sqlite:' . $databasePath, null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

// Carica lo script SQL con schema + dati di esempio.
$sql = file_get_contents($schemaPath);
if ($sql === false) {
    // Termina con errore se lo schema non è leggibile.
    fwrite(STDERR, "Impossibile leggere schema SQL\n");
    exit(1);
}

// Esegue lo script SQL nel database.
$pdo->exec($sql);

// Conferma a video il percorso del database inizializzato.
fwrite(STDOUT, "Database inizializzato in: {$databasePath}\n");
