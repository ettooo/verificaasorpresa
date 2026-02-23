<?php

declare(strict_types=1);

use App\ExerciseQueryService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;

// Carica l'autoloader Composer (vendor + classi applicative).
require __DIR__ . '/../vendor/autoload.php';

// Legge configurazione database e dizionario delle query SQL degli esercizi.
$databaseConfig = require __DIR__ . '/../config/database.php';
$queries = require __DIR__ . '/../src/queries.php';

// Crea l'istanza Slim e abilita il parsing automatico dei body request.
$app = AppFactory::create();
$app->addBodyParsingMiddleware();

// Middleware errori: restituisce sempre un payload JSON uniforme in caso di eccezione.
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler(function (
    ServerRequestInterface $request,
    Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails
) use ($app): ResponseInterface {
    $status = 500;
    $response = $app->getResponseFactory()->createResponse($status);

    $payload = [
        'ok' => false,
        'status' => $status,
        'error' => [
            'type' => 'internal_error',
            'message' => $exception->getMessage(),
        ],
    ];

    $response->getBody()->write((string) json_encode($payload, JSON_UNESCAPED_UNICODE));

    return $response->withHeader('Content-Type', 'application/json');
});

// Factory PDO: crea una nuova connessione DB per ogni richiesta endpoint.
$createPdo = static function () use ($databaseConfig): PDO {
    return new PDO(
        $databaseConfig['dsn'],
        $databaseConfig['user'],
        $databaseConfig['password'],
        [
            // Abilita eccezioni per errori SQL.
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // Restituisce righe come array associativi.
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
};

// Servizio applicativo che esegue le query degli esercizi.
$service = new ExerciseQueryService($createPdo, $queries);

// Helper per serializzare payload JSON in modo consistente.
$json = static function (ResponseInterface $response, array $result, int $status = 200): ResponseInterface {
    $payload = [
        'ok' => $status >= 200 && $status < 400,
        'status' => $status,
        'result' => $result,
    ];

    $response->getBody()->write((string) json_encode($payload, JSON_UNESCAPED_UNICODE));
    return $response
        ->withStatus($status)
        ->withHeader('Content-Type', 'application/json');
};

// Endpoint root: espone metadati API ed elenco endpoint disponibili.
$app->get('/', static function (ServerRequestInterface $request, ResponseInterface $response) use ($json, $service): ResponseInterface {
    return $json($response, [
        'service' => 'fornitori-pezzi-catalogo',
        'availableEndpoints' => $service->endpoints(),
    ]);
});

// Registra dinamicamente gli endpoint /1 ... /10.
foreach ($queries as $exercise => $sql) {
    // Ogni route esegue la query associata all'esercizio e ritorna il risultato in JSON.
    $app->get('/' . $exercise, static function (ServerRequestInterface $request, ResponseInterface $response) use ($service, $json, $exercise): ResponseInterface {
        $payload = $service->run((int) $exercise);

        return $json($response, [
            'exerciseId' => $payload['exercise'],
            'totalRows' => $payload['count'],
            'items' => $payload['data'],
        ]);
    });
}
