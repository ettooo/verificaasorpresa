# verificaasorpresa

API REST in PHP con Slim Framework per eseguire 10 interrogazioni SQL sullo schema:

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

## Inizializzazione database

### SQLite (default)

```bash
php scripts/init_db.php
```

Crea `database/database.sqlite` e carica schema + dati da `database/schema.sql`.

### MySQL/MariaDB (Codespace)

Dump dedicato: `database/schema.mysql.sql`.

1) Avvia un container MySQL/MariaDB (esempio MySQL):

```bash
docker run --name verifica-mysql -e MYSQL_ROOT_PASSWORD=root -e MYSQL_DATABASE=verificaasorpresa -p 3306:3306 -d mysql:8
```

2) Inizializza schema e dati:

```bash
MYSQL_HOST=127.0.0.1 MYSQL_PORT=3306 MYSQL_DATABASE=verificaasorpresa MYSQL_USER=root MYSQL_PASSWORD=root php scripts/init_db_mysql.php
```

3) Configura l'API verso MySQL/MariaDB:

```bash
export DB_DSN='mysql:host=127.0.0.1;port=3306;dbname=verificaasorpresa;charset=utf8mb4'
export DB_USER='root'
export DB_PASSWORD='root'
```

## Avvio API

```bash
php -S localhost:8080 -t public
```

## Endpoint

- `GET /1` -> `pnome` dei pezzi per cui esiste almeno un fornitore
- `GET /2` -> `fnome` dei fornitori che forniscono ogni pezzo
- `GET /3` -> `fnome` dei fornitori che forniscono tutti i pezzi rossi
- `GET /4` -> `pnome` dei pezzi forniti da Acme e da nessun altro
- `GET /5` -> `fid` dei fornitori con costo sopra la media del pezzo
- `GET /6` -> per ciascun pezzo, fornitori con costo massimo
- `GET /7` -> `fid` dei fornitori che forniscono solo pezzi rossi
- `GET /8` -> `fid` dei fornitori con almeno un pezzo rosso e uno verde
- `GET /9` -> `fid` dei fornitori con almeno un pezzo rosso o verde
- `GET /10` -> `pid` dei pezzi forniti da almeno due fornitori

## Parametri richiesta (ove ha senso)

Filtri opzionali (applicati solo se la colonna è presente nel risultato dell'endpoint):

- `pid`
- `fid`
- `colore`

Paginazione opzionale:

- `limit`
- `offset`

Esempi:

```bash
curl 'http://127.0.0.1:8080/6?pid=P3'
curl 'http://127.0.0.1:8080/9?fid=F2'
curl 'http://127.0.0.1:8080/6?limit=2&offset=1'
```

## Formato risposta `application/json`

Successo:

```json
{
	"ok": true,
	"status": 200,
	"result": {
		"exerciseId": 1,
		"totalRows": 3,
		"totalBeforePagination": 5,
		"pagination": {
			"limit": 3,
			"offset": 0
		},
		"filters": {
			"pid": null,
			"fid": null,
			"colore": null
		},
		"items": []
	}
}
```

Errore:

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

## Test (unit test opzionali)

La suite include test unitari con mock di `PDO` e `PDOStatement`.

```bash
composer test
```

## Verifiche rapide

### Test automatici

```bash
composer test
```

### Controllo sintassi PHP

```bash
find . -path ./vendor -prune -o -name '*.php' -print | xargs -n1 php -l
```

### Test manuale API

```bash
curl http://127.0.0.1:8080/
curl http://127.0.0.1:8080/1
curl http://127.0.0.1:8080/10
```

## Come fermare l'applicazione

Se il server è avviato con `php -S ...`, nel terminale in esecuzione premi `Ctrl + C`.

Se non trovi il terminale corretto:

```bash
lsof -i :8080
kill <PID>
```
