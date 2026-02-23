<?php

declare(strict_types=1);

namespace Tests;

use App\ExerciseQueryService;
use InvalidArgumentException;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use RuntimeException;

// Test unitari del servizio query con uso di mock PDO/PDOStatement.
final class ExerciseQueryServiceTest extends TestCase
{
    // Verifica il caso positivo: query eseguita e payload corretto.
    public function testRunReturnsPayloadWithPdoMocks(): void
    {
        // Righe simulate che la query dovrebbe restituire.
        $rows = [
            ['pnome' => 'Bullone'],
            ['pnome' => 'Vite'],
        ];

        // SQL di esempio associata all'esercizio 1.
        $sql = 'SELECT pnome FROM Pezzi';

        // Mock di PDOStatement con fetchAll valorizzato.
        $statement = $this->createMock(PDOStatement::class);
        $statement->expects($this->once())
            ->method('fetchAll')
            ->with()
            ->willReturn($rows);

        // Mock di PDO che restituisce il mock statement alla chiamata query().
        $pdo = $this->createMock(PDO::class);
        $pdo->expects($this->once())
            ->method('query')
            ->with($sql)
            ->willReturn($statement);

        // Crea il servizio passando factory PDO mockata e query mappata.
        $service = new ExerciseQueryService(
            static fn (): PDO => $pdo,
            [1 => $sql]
        );

        // Esegue la logica e verifica struttura/valori del payload.
        $result = $service->run(1);

        $this->assertSame(1, $result['exercise']);
        $this->assertSame(2, $result['count']);
        $this->assertSame($rows, $result['data']);
    }

    // Verifica errore per esercizio non presente nella mappa query.
    public function testRunThrowsExceptionForUnknownExercise(): void
    {
        $pdo = $this->createMock(PDO::class);

        $service = new ExerciseQueryService(
            static fn (): PDO => $pdo,
            [1 => 'SELECT 1']
        );

        $this->expectException(InvalidArgumentException::class);
        $service->run(99);
    }

    // Verifica errore quando PDO::query fallisce e restituisce false.
    public function testRunThrowsExceptionWhenQueryFails(): void
    {
        $sql = 'SELECT pnome FROM Pezzi';

        $pdo = $this->createMock(PDO::class);
        $pdo->expects($this->once())
            ->method('query')
            ->with($sql)
            ->willReturn(false);

        $service = new ExerciseQueryService(
            static fn (): PDO => $pdo,
            [1 => $sql]
        );

        $this->expectException(RuntimeException::class);
        $service->run(1);
    }

    // Verifica conversione chiavi esercizio in endpoint HTTP.
    public function testEndpointsReturnsPathList(): void
    {
        $pdo = $this->createMock(PDO::class);

        $service = new ExerciseQueryService(
            static fn (): PDO => $pdo,
            [1 => 'SELECT 1', 2 => 'SELECT 2', 10 => 'SELECT 10']
        );

        $this->assertSame(['/1', '/2', '/10'], $service->endpoints());
    }
}
