<?php

declare(strict_types=1);

// Mappa degli esercizi SQL: chiave endpoint numerico -> query da eseguire.
return [
    // 1) pnome dei pezzi per cui esiste almeno un fornitore.
    1 => "
        SELECT DISTINCT p.pnome
        FROM Pezzi p
        JOIN Catalogo c ON c.pid = p.pid
    ",

    // 2) fnome dei fornitori che forniscono ogni pezzo.
    2 => "
        SELECT f.fnome
        FROM Fornitori f
        WHERE NOT EXISTS (
            SELECT 1
            FROM Pezzi p
            WHERE NOT EXISTS (
                SELECT 1
                FROM Catalogo c
                WHERE c.fid = f.fid
                  AND c.pid = p.pid
            )
        )
    ",

    // 3) fnome dei fornitori che forniscono tutti i pezzi rossi.
    3 => "
        SELECT f.fnome
        FROM Fornitori f
        WHERE NOT EXISTS (
            SELECT 1
            FROM Pezzi p
            WHERE p.colore = 'rosso'
              AND NOT EXISTS (
                SELECT 1
                FROM Catalogo c
                WHERE c.fid = f.fid
                  AND c.pid = p.pid
            )
        )
    ",

    // 4) pnome dei pezzi forniti da Acme e da nessun altro.
    4 => "
        SELECT p.pnome
        FROM Pezzi p
        JOIN Catalogo c ON c.pid = p.pid
        JOIN Fornitori f ON f.fid = c.fid
        WHERE f.fnome = 'Acme'
          AND NOT EXISTS (
              SELECT 1
              FROM Catalogo c2
              WHERE c2.pid = p.pid
                AND c2.fid <> c.fid
          )
    ",

    // 5) fid dei fornitori con costo superiore alla media del pezzo.
    5 => "
        SELECT DISTINCT c.fid
        FROM Catalogo c
        WHERE c.costo > (
            SELECT AVG(c2.costo)
            FROM Catalogo c2
            WHERE c2.pid = c.pid
        )
    ",

    // 6) per ciascun pezzo, fornitori con costo massimo su quel pezzo.
    6 => "
        SELECT p.pid, p.pnome, f.fnome, c.costo
        FROM Pezzi p
        JOIN Catalogo c ON c.pid = p.pid
        JOIN Fornitori f ON f.fid = c.fid
        WHERE c.costo = (
            SELECT MAX(c2.costo)
            FROM Catalogo c2
            WHERE c2.pid = c.pid
        )
        ORDER BY p.pid, f.fnome
    ",

    // 7) fid dei fornitori che forniscono solo pezzi rossi.
    7 => "
        SELECT f.fid
        FROM Fornitori f
        WHERE EXISTS (
            SELECT 1
            FROM Catalogo c
            WHERE c.fid = f.fid
        )
          AND NOT EXISTS (
            SELECT 1
            FROM Catalogo c
            JOIN Pezzi p ON p.pid = c.pid
            WHERE c.fid = f.fid
              AND p.colore <> 'rosso'
        )
    ",

    // 8) fid dei fornitori con almeno un pezzo rosso e almeno un pezzo verde.
    8 => "
        SELECT DISTINCT c1.fid
        FROM Catalogo c1
        JOIN Pezzi p1 ON p1.pid = c1.pid
        JOIN Catalogo c2 ON c2.fid = c1.fid
        JOIN Pezzi p2 ON p2.pid = c2.pid
        WHERE p1.colore = 'rosso'
          AND p2.colore = 'verde'
    ",

    // 9) fid dei fornitori con almeno un pezzo rosso o verde.
    9 => "
        SELECT DISTINCT c.fid
        FROM Catalogo c
        JOIN Pezzi p ON p.pid = c.pid
        WHERE p.colore IN ('rosso', 'verde')
    ",

    // 10) pid dei pezzi forniti da almeno due fornitori.
    10 => "
        SELECT c.pid
        FROM Catalogo c
        GROUP BY c.pid
        HAVING COUNT(DISTINCT c.fid) >= 2
    ",
];
