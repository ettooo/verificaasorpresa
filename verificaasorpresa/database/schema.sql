DROP TABLE IF EXISTS Catalogo;
DROP TABLE IF EXISTS Pezzi;
DROP TABLE IF EXISTS Fornitori;

CREATE TABLE Fornitori (
    fid TEXT PRIMARY KEY,
    fnome TEXT NOT NULL,
    indirizzo TEXT NOT NULL
);

CREATE TABLE Pezzi (
    pid TEXT PRIMARY KEY,
    pnome TEXT NOT NULL,
    colore TEXT NOT NULL
);

CREATE TABLE Catalogo (
    fid TEXT NOT NULL,
    pid TEXT NOT NULL,
    costo REAL NOT NULL,
    PRIMARY KEY (fid, pid),
    FOREIGN KEY (fid) REFERENCES Fornitori(fid),
    FOREIGN KEY (pid) REFERENCES Pezzi(pid)
);

INSERT INTO Fornitori (fid, fnome, indirizzo) VALUES
('F1', 'Acme', 'Via Verdi 10'),
('F2', 'Orion', 'Via Manzoni 21'),
('F3', 'Zenit', 'Corso Italia 7'),
('F4', 'Nova', 'Via Garibaldi 55');

INSERT INTO Pezzi (pid, pnome, colore) VALUES
('P1', 'Ingranaggio', 'rosso'),
('P2', 'Valvola', 'verde'),
('P3', 'Piastra', 'rosso'),
('P4', 'Supporto', 'blu'),
('P5', 'Giunto', 'verde');

INSERT INTO Catalogo (fid, pid, costo) VALUES
('F1', 'P1', 11.5),
('F1', 'P2', 18.0),
('F1', 'P3', 26.0),
('F1', 'P4', 16.5),
('F2', 'P1', 9.0),
('F2', 'P2', 19.5),
('F2', 'P5', 29.0),
('F3', 'P3', 24.0),
('F3', 'P4', 13.0),
('F3', 'P5', 31.0),
('F4', 'P1', 12.0),
('F4', 'P3', 20.0);
