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
     * @param array{pid?:string,fid?:string,colore?:string,limit?:int,offset?:int} $options
     * @return array{exercise:int,count:int,data:array<int, array<string, mixed>>,total:int,limit:?int,offset:int}
     */
    public function run(int $exercise, array $options = []): array
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

        // Filtri applicati ove ha senso: solo se la colonna è presente nel risultato.
        $rows = $this->applyFilters($rows, $options);

        $total = count($rows);

        // Paginazione opzionale a livello applicativo.
        $offset = max(0, (int) ($options['offset'] ?? 0));
        $limit = isset($options['limit']) ? max(1, (int) $options['limit']) : null;
        if ($limit !== null) {
            $rows = array_slice($rows, $offset, $limit);
        } elseif ($offset > 0) {
            $rows = array_slice($rows, $offset);
        }

        // Restituisce payload API uniforme.
        return [
            'exercise' => $exercise,
            'count' => count($rows),
            'data' => $rows,
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset,
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @param array{pid?:string,fid?:string,colore?:string} $options
     * @return array<int, array<string, mixed>>
     */
    private function applyFilters(array $rows, array $options): array
    {
        $filters = [
            'pid' => $options['pid'] ?? null,
            'fid' => $options['fid'] ?? null,
            'colore' => $options['colore'] ?? null,
        ];

        foreach ($filters as $column => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $rows = array_values(array_filter(
                $rows,
                static function (array $row) use ($column, $value): bool {
                    if (!array_key_exists($column, $row)) {
                        return true;
                    }

                    return (string) $row[$column] === (string) $value;
                }
            ));
        }

        return $rows;
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
