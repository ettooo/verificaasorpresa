# verificaasorpresa

API REST in PHP con Slim Framework per risolvere 10 interrogazioni SQL sullo schema:

- `Fornitori(fid, fnome, indirizzo)`
- `Pezzi(pid, pnome, colore)`
- `Catalogo(fid, pid, costo)`

## Requisiti

- PHP >= 8.2
- Composer

## Installazione

```bash
composer install
```

## Inizializzazione database SQLite (default)

```bash
php scripts/init_db.php
```

Questo crea `database/database.sqlite` e carica lo schema/dati da `database/schema.sql`.

## Avvio API

```bash
php -S localhost:8080 -t public
```

## Test (PHPUnit con mock)

La suite include test unitari con mock di `PDO` e `PDOStatement` per validare la logica senza dipendere dal database reale.

```bash
composer test
```

## Come testare il codice

### 1) Test automatici (unit test)

```bash
composer test
```

### 2) Controllo sintassi PHP

```bash
find . -path ./vendor -prune -o -name '*.php' -print | xargs -n1 php -l
```

### 3) Test API manuale (endpoint)

Avvia prima il server:

```bash
php -S 127.0.0.1:8080 -t public
```

In un secondo terminale:

```bash
curl http://127.0.0.1:8080/
curl http://127.0.0.1:8080/1
curl http://127.0.0.1:8080/10
```

## Endpoint

- `GET /1` -> pnome dei pezzi per cui esiste almeno un fornitore
- `GET /2` -> fnome dei fornitori che forniscono ogni pezzo
- `GET /3` -> fnome dei fornitori che forniscono tutti i pezzi rossi
- `GET /4` -> pnome dei pezzi forniti da Acme e da nessun altro
- `GET /5` -> fid dei fornitori con costo sopra la media del pezzo
- `GET /6` -> per ciascun pezzo, fornitori con costo massimo
- `GET /7` -> fid dei fornitori che forniscono solo pezzi rossi
- `GET /8` -> fid dei fornitori con almeno un pezzo rosso e uno verde
- `GET /9` -> fid dei fornitori con almeno un pezzo rosso o verde
- `GET /10` -> pid dei pezzi forniti da almeno due fornitori

Nuovo formato JSON standard:

```json
{
	"ok": true,
	"status": 200,
	"result": {
		"exerciseId": 1,
		"totalRows": 3,
		"items": []
	}
}
```

Formato errore:

```json
{
	"ok": false,
	"status": 500,
	"error": {
		"type": "internal_error",
		"message": "..."
	}
}
```

## Configurazione DB alternativa

Puoi usare un DB diverso passando variabili ambiente:

- `DB_DSN` (esempio MySQL: `mysql:host=localhost;dbname=test;charset=utf8mb4`)
- `DB_USER`
- `DB_PASSWORD`

## Come fermare l'applicazione

Se il server è avviato con `php -S ...`:

- nel terminale dove è in esecuzione, premi `Ctrl + C`

Se non trovi il terminale corretto:

```bash
lsof -i :8080
kill <PID>
```
