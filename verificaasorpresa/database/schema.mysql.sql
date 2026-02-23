DROP TABLE IF EXISTS Catalogo;
DROP TABLE IF EXISTS Pezzi;
DROP TABLE IF EXISTS Fornitori;

CREATE TABLE Fornitori (
    fid VARCHAR(20) PRIMARY KEY,
    fnome VARCHAR(100) NOT NULL,
    indirizzo VARCHAR(150) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE Pezzi (
    pid VARCHAR(20) PRIMARY KEY,
    pnome VARCHAR(100) NOT NULL,
    colore VARCHAR(40) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE Catalogo (
    fid VARCHAR(20) NOT NULL,
    pid VARCHAR(20) NOT NULL,
    costo DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (fid, pid),
    CONSTRAINT fk_catalogo_fornitori FOREIGN KEY (fid) REFERENCES Fornitori(fid),
    CONSTRAINT fk_catalogo_pezzi FOREIGN KEY (pid) REFERENCES Pezzi(pid)
) ENGINE=InnoDB;

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
('F1', 'P1', 11.50),
('F1', 'P2', 18.00),
('F1', 'P3', 26.00),
('F1', 'P4', 16.50),
('F2', 'P1', 9.00),
('F2', 'P2', 19.50),
('F2', 'P5', 29.00),
('F3', 'P3', 24.00),
('F3', 'P4', 13.00),
('F3', 'P5', 31.00),
('F4', 'P1', 12.00),
('F4', 'P3', 20.00);
