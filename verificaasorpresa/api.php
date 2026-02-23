<?php

declare(strict_types=1);

// Carica la configurazione dell'app Slim (routing, middleware, servizi).
require __DIR__ . '/bootstrap/app.php';

// Avvia la gestione delle richieste HTTP.
$app->run();
