<?php

declare(strict_types=1);

// Percorso del database SQLite locale usato come fallback.
$defaultSqlitePath = __DIR__ . '/../database/database.sqlite';
// DSN predefinito per connessione SQLite.
$defaultDsn = 'sqlite:' . $defaultSqlitePath;

// Configurazione DB: usa variabili ambiente se presenti, altrimenti fallback SQLite.
return [
    'dsn' => getenv('DB_DSN') ?: $defaultDsn,
    'user' => getenv('DB_USER') ?: null,
    'password' => getenv('DB_PASSWORD') ?: null,
];
