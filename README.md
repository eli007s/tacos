give permissons of `0755` to `db.json` and `textSampleResults.json`

PHP 5.6+ needed for `api`

PHP modules `php-json` `php-pdo` needed to run api

sqlite needed as well.

## Challenge 1

GET - `https://domain.com/api/tacos`

returns list of tacos

GET - `https://domain.com/api/tacos/{:name}`

returns a taco in question

PUT - `https://domain.com/api/tacos/{:name}`

updates the taco in question

| NAME  | DESC  |
|---|---|
| name  | string  |
| tortilla  | string  |
| toppings  | string  |
| vegetarian  | bool  |
| soft  | bool  |


DELETE - `https://domain.com/api/tacos/{:name}`

deletes the taco in question


BONUS - use `https://domain.com/api2/` for an `SQLite` version =)

## Challenge 2

Endpoint - `https://domain.com/clean`

API Endpoint - `https://domain.com/api/clean`

Paster a blob of text you want to be scanned and cleaned and hut `submit` You'll be taken to the `api` page where you
will see the `json` output...
