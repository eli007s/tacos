give permissons of `0755` to `db.json` and `textSampleResults.json`

PHP 5.6+ needed for `api`

PHP modules `php-json` `php-pdo` needed to run api

sqlite needed as well

GET - `/api/tacos`

returns list of tacos

GET - `/api/tacos/:name`

returns a taco in question

PUT - `/api/tacos/:name`

updates the taco in question

| NAME  | DESC  |
|---|---|
| name  | string  |
| tortilla  | string  |
| toppings  | string  |
| vegetarian  | bool  |
| soft  | bool  |


DELETE - `/api/tacos/:name`

deletes the taco in question


BONUS - use `/api2/*` for an `SQLite` version =)
