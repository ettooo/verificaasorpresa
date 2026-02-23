<?php

declare(strict_types=1);

namespace App;

use InvalidArgumentException;
use PDO;
use RuntimeException;

// Servizio applicativo che incapsula l'esecuzione delle query degli esercizi.
final class ExerciseQueryService
{
    /** @var callable():PDO */
    // Factory usata per ottenere una connessione PDO on-demand.
    private $pdoFactory;

    /** @var array<int, string> */
    // Mappa esercizio => query SQL.
    private array $queries;

    /**
     * @param callable():PDO $pdoFactory
     * @param array<int, string> $queries
     */
    public function __construct(callable $pdoFactory, array $queries)
    {
        // Salva dipendenze necessarie all'esecuzione.
        $this->pdoFactory = $pdoFactory;
        $this->queries = $queries;
    }

    /**
     * @return array{exercise:int,count:int,data:array<int, array<string, mixed>>}
     */
    public function run(int $exercise): array
    {
        // Valida che l'esercizio richiesto esista nella mappa query.
        if (!isset($this->queries[$exercise])) {
            throw new InvalidArgumentException('Esercizio non valido');
        }

        // Crea connessione DB e prova ad eseguire la query SQL dell'esercizio.
        $pdo = ($this->pdoFactory)();
        $statement = $pdo->query($this->queries[$exercise]);

        // In caso di fallimento esplicito, segnala errore applicativo.
        if ($statement === false) {
            throw new RuntimeException('Esecuzione query fallita');
        }

        // Recupera tutte le righe restituite dalla query.
        $rows = $statement->fetchAll();

        // Restituisce payload API uniforme.
        return [
            'exercise' => $exercise,
            'count' => count($rows),
            'data' => $rows,
        ];
    }

    /**
     * @return list<string>
     */
    public function endpoints(): array
    {
        // Converte le chiavi numeriche (1..10) in percorsi HTTP (/1.. /10).
        return array_map(
            static fn (int $exercise): string => '/' . $exercise,
            array_keys($this->queries)
        );
    }
}
